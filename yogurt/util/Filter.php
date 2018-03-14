<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
 /**
 * 两个主要的功能：1 过滤数据 2 验证数据
 * @filesource		yogurt/utils/Filter.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
 
class Filter {	
    private $messages = array();
	private $rules = array();
	 /**
     * Error messages.
     *
     * @var array
     */
    protected $errors = array();
    
    protected $tagsArray;
	protected $attrArray;

	protected $tagsMethod;
	protected $attrMethod;

	protected $xssAuto;
	protected $tagBlacklist = array('applet', 'body', 'bgsound', 'base', 'basefont', 'embed', 'frame', 'frameset', 'head', 'html', 'id', 'iframe', 'ilayer', 'layer', 'link', 'meta', 'name', 'object', 'script', 'style', 'title', 'xml');
	protected $attrBlacklist = array('action', 'background', 'codebase', 'dynsrc', 'lowsrc');
	public function __construct($tagsArray = array(), $attrArray = array(), $tagsMethod = 0, $attrMethod = 0, $xssAuto = 1) {		
		for ($i = 0; $i < count($tagsArray); $i++) $tagsArray[$i] = strtolower($tagsArray[$i]);
		for ($i = 0; $i < count($attrArray); $i++) $attrArray[$i] = strtolower($attrArray[$i]);
		$this->tagsArray = (array) $tagsArray;
		$this->attrArray = (array) $attrArray;
		$this->tagsMethod = $tagsMethod;
		$this->attrMethod = $attrMethod;
		$this->xssAuto = $xssAuto;
	}
	
	public function process($source) {
		if (is_array($source)) {
			foreach($source as $key => $value)
				if (is_string($value)) $source[$key] = $this->remove($this->decode($value));
			return $source;
		} else if (is_string($source)) {
			return $this->remove($this->decode($source));
		} else return $source;	
	}
	
	protected function remove($source) {
		$loopCounter=0;
		while($source != $this->filterTags($source)) {
			$source = $this->filterTags($source);
			$loopCounter++;
		}
		return $source;
	}	
	
	protected function filterTags($source) {
		$preTag = NULL;
		$postTag = $source;
		$tagOpen_start = strpos($source, '<');
		while($tagOpen_start !== FALSE) {
			$preTag .= substr($postTag, 0, $tagOpen_start);
			$postTag = substr($postTag, $tagOpen_start);
			$fromTagOpen = substr($postTag, 1);
			$tagOpen_end = strpos($fromTagOpen, '>');
			if ($tagOpen_end === false) break;
			$tagOpen_nested = strpos($fromTagOpen, '<');
			if (($tagOpen_nested !== false) && ($tagOpen_nested < $tagOpen_end)) {
				$preTag .= substr($postTag, 0, ($tagOpen_nested+1));
				$postTag = substr($postTag, ($tagOpen_nested+1));
				$tagOpen_start = strpos($postTag, '<');
				continue;
			} 
			$tagOpen_nested = (strpos($fromTagOpen, '<') + $tagOpen_start + 1);
			$currentTag = substr($fromTagOpen, 0, $tagOpen_end);
			$tagLength = strlen($currentTag);
			if (!$tagOpen_end) {
				$preTag .= $postTag;
				$tagOpen_start = strpos($postTag, '<');			
			}
			$tagLeft = $currentTag;
			$attrSet = array();
			$currentSpace = strpos($tagLeft, ' ');
			if (substr($currentTag, 0, 1) == "/") {
				$isCloseTag = TRUE;
				list($tagName) = explode(' ', $currentTag);
				$tagName = substr($tagName, 1);
			} else {
				$isCloseTag = FALSE;
				list($tagName) = explode(' ', $currentTag);
			}		
			if ((!preg_match("/^[a-z][a-z0-9]*$/i",$tagName)) || (!$tagName) || ((in_array(strtolower($tagName), $this->tagBlacklist)) && ($this->xssAuto))) { 				
				$postTag = substr($postTag, ($tagLength + 2));
				$tagOpen_start = strpos($postTag, '<');
				continue;
			}
			while ($currentSpace !== FALSE) {
				$fromSpace = substr($tagLeft, ($currentSpace+1));
				$nextSpace = strpos($fromSpace, ' ');
				$openQuotes = strpos($fromSpace, '"');
				$closeQuotes = strpos(substr($fromSpace, ($openQuotes+1)), '"') + $openQuotes + 1;
				if (strpos($fromSpace, '=') !== FALSE) {
					if (($openQuotes !== FALSE) && (strpos(substr($fromSpace, ($openQuotes+1)), '"') !== FALSE))
						$attr = substr($fromSpace, 0, ($closeQuotes+1));
					else $attr = substr($fromSpace, 0, $nextSpace);
				} else $attr = substr($fromSpace, 0, $nextSpace);
				if (!$attr) $attr = $fromSpace;
				$attrSet[] = $attr;
				$tagLeft = substr($fromSpace, strlen($attr));
				$currentSpace = strpos($tagLeft, ' ');
			}
			$tagFound = in_array(strtolower($tagName), $this->tagsArray);			
			if ((!$tagFound && $this->tagsMethod) || ($tagFound && !$this->tagsMethod)) {
				if (!$isCloseTag) {
					$attrSet = $this->filterAttr($attrSet);
					$preTag .= '<' . $tagName;
					for ($i = 0; $i < count($attrSet); $i++)
						$preTag .= ' ' . $attrSet[$i];
					if (strpos($fromTagOpen, "</" . $tagName)) $preTag .= '>';
					else $preTag .= ' />';
			    } else $preTag .= '</' . $tagName . '>';
			}
			$postTag = substr($postTag, ($tagLength + 2));
			$tagOpen_start = strpos($postTag, '<');			
		}
		$preTag .= $postTag;
		return $preTag;
	}
	protected function filterAttr($attrSet) {	
		$newSet = array();
		for ($i = 0; $i <count($attrSet); $i++) {
			if (!$attrSet[$i]) continue;
			$attrSubSet = explode('=', trim($attrSet[$i]));
			list($attrSubSet[0]) = explode(' ', $attrSubSet[0]);
			if ((!eregi("^[a-z]*$",$attrSubSet[0])) || (($this->xssAuto) && ((in_array(strtolower($attrSubSet[0]), $this->attrBlacklist)) || (substr($attrSubSet[0], 0, 2) == 'on')))) 
				continue;
			if ($attrSubSet[1]) {
				$attrSubSet[1] = str_replace('&#', '', $attrSubSet[1]);
				$attrSubSet[1] = preg_replace('/\s+/', '', $attrSubSet[1]);
				$attrSubSet[1] = str_replace('"', '', $attrSubSet[1]);
				if ((substr($attrSubSet[1], 0, 1) == "'") && (substr($attrSubSet[1], (strlen($attrSubSet[1]) - 1), 1) == "'"))
					$attrSubSet[1] = substr($attrSubSet[1], 1, (strlen($attrSubSet[1]) - 2));
				$attrSubSet[1] = stripslashes($attrSubSet[1]);
			}
			if (	((strpos(strtolower($attrSubSet[1]), 'expression') !== false) &&	(strtolower($attrSubSet[0]) == 'style')) ||
					(strpos(strtolower($attrSubSet[1]), 'javascript:') !== false) ||
					(strpos(strtolower($attrSubSet[1]), 'behaviour:') !== false) ||
					(strpos(strtolower($attrSubSet[1]), 'vbscript:') !== false) ||
					(strpos(strtolower($attrSubSet[1]), 'mocha:') !== false) ||
					(strpos(strtolower($attrSubSet[1]), 'livescript:') !== false) 
			) continue;
			$attrFound = in_array(strtolower($attrSubSet[0]), $this->attrArray);
			if ((!$attrFound && $this->attrMethod) || ($attrFound && !$this->attrMethod)) {
				if ($attrSubSet[1]) $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[1] . '"';
				else if ($attrSubSet[1] == "0") $newSet[] = $attrSubSet[0] . '="0"';
				else $newSet[] = $attrSubSet[0] . '="' . $attrSubSet[0] . '"';
			}	
		}
		return $newSet;
	}
	protected function decode($source) {
		$source = html_entity_decode($source, ENT_QUOTES, "ISO-8859-1");
		$source = preg_replace('/&#(\d+);/me',"chr(\\1)", $source);
		$source = preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)", $source);
		return $source;
	}
	
	public function safeSQL($source, &$connection) {
		if (is_array($source)) {
			foreach($source as $key => $value)
				if (is_string($value)) $source[$key] = $this->quoteSmart($this->decode($value), $connection);
			return $source;
		} else if (is_string($source)) {
			if (is_string($source)) return $this->quoteSmart($this->decode($source), $connection);
		} else return $source;	
	}
	protected function quoteSmart($source, &$connection) {
		if (get_magic_quotes_gpc()) $source = stripslashes($source);
		$source = $this->escape($source, $connection);
		return $source;
	}
	
	private function my_stripslashes($str)
    {
     return is_array($str) ? array_map('my_stripslashes', $str) : stripslashes($str);
   }

  	 /**
     * addslashes value
     * @param mix $value
     * @return mix
     */
    private static function my_addslashes($val) {
    	 return is_array($val) ? array_map('my_addslashes', $val) : addslashes($val);
    /*	if (is_array($val)) {
    		foreach ($val AS $k => $v) {
    			$val[$k] = self::my_addslashes($v);
    		}
    	} else {
    		return addslashes($val);
    	}   	
    	return $val;*/
    	//stripslashes
    }

  private function my_mysql_real_escape_string($str)
    {
       return is_array($str) ? array_map('my_mysql_real_escape_string', $str) : mysql_real_escape_string($str);
    }
	
	public static function escape($str) {
		/*if (version_compare(phpversion(),"4.3.0", "<")) mysql_escape_string($str);
		else mysql_real_escape_string($string);
		return $string;*/
		 /* if (get_magic_quotes_gpc())
        {
        	
          $str = self::my_stripslashes($str);
        }
      if (function_exists("mysql_real_escape_string"))
        {
         $str = self::my_mysql_real_escape_string($str);
         }
      else
        {
          $str = self::my_addslashes($str);
        }*/
        if (!get_magic_quotes_gpc()) {
        	$str =addslashes($str);
        }
      return $str;
	}
	
    /**
     * htmlspecialchars value
     * @param mix $value
     * @return mix
     */
    private static function htmlspecialchars($value) {
    	if (is_array($value)) {
    		foreach ($value AS $k => $v) {
    			$value[$k] = self::htmlspecialchars($v);
    		}
    	} else {
    		return addslashes($value);
    	}
    	
    	return $value;
    }
 
	 
// addslashes 后转回
function santize()
{
  //if (get_magic_quotes_gpc()){
      $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
      while (list($key, $val) = each($process))
        {
          foreach ($val as $k => $v)
            {
              unset($process[$key][$k]);
              if (is_array($v))
                {
                  $process[$key][stripslashes($k)] = $v;
                  $process[] = &$process[$key][stripslashes($k)];
                }
              else
                {
                  $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
      unset($process);
   // }
}

 private function is_meta_injection($str)
    {
      if (preg_match("/(\%27)|(\')|(\-\-)|(\%23)|(\#)/ix", $str, $matches) == true)
        {
          return true;
        }
      if (preg_match("/((\%3D)|(=))[^\n]*((\%27)|(\')|(\-\-)|(\%3B)|(;))/i", $str, $matches) == true)
        {
          return true;
        }
      if (preg_match("/\w*((\%27)|(\'))((\%6F)|o|(\%4F))((\%72)|r|(\%52))/ix", $str, $matches) == true)
        {
          return true;
        }
      if (preg_match("/((\%27)|(\'))union/ix", $str, $matches) == true)
        {
          return true;
        }
      return false;
    }

  private function is_css_injection($str)
    {
      if (preg_match("/((\%3C)|<)((\%2F)|\/)*[a-z0-9\%]+((\%3E)|>)/ix", $str, $matches) == true)
        {
          return true;
        }
      if (preg_match("/((\%3C)|<)((\%69)|i|(\%49))((\%6D)|m|(\%4D))((\%67)|g|(\%47))[^\n]+((\%3E)|>)/i", $str, $matches) == true)
        {
          return true;
        }
      if (preg_match("/((\%3C)|<)[^\n]+((\%3E)|>)/i", $str, $matches) == true)
        {
          return true;
        }
      return false;
    }

  public function is_injection($str)
    {
      if ($this->is_meta_injection($str))
        return true;

      if ($this->is_css_injection($str))
        return true;
      return false;
    }

  public function strip_html($str)
    {
      $search = array('@<script[^>]*?>.*?</script>@si', '@<[\/\!]*?[^<>]*?>@si', '@<style[^>]*?>.*?</style>@siU', '@<![\s\S]*?--[ \t\n\r]*>@');

      $str = preg_replace($search, '', $str);

      return $str;
    }
########################### 验证 ###############################
 /**
     * if value is email
     * @param String $value the value being tested
     * @param boolean $isEmpty if field can be empty
     * @param boolean
     */
    public static function email($str) {
    	if(strlen($str) == 0)   	
        	return false;

    	// in case value is several addresses separated by newlines
    	$_addresses = preg_split('![\n\r]+!', $str);

    	foreach($_addresses as $_address) {
			$_is_valid = !(preg_match('!@.*@|\.\.|\,|\;!', $_address) ||
	        	!preg_match('!^.+\@(\[?)[a-zA-Z0-9\.\-]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$!', $_address));
        
        	if(!$_is_valid)
            	return false;
    	}
    	return true;
    }
    /**
     * check is ip 
     * @param String ip
     * @return String 
     * @static
     */
    public static function ip($ip){
    	$long = ip2long($ip);
        return !($long == -1 || $long === FALSE);
    }
    
     /*
     * 判断是否为有效网址   
     */
    public static function url($str){     
        if (!preg_match("^http://[_a-zA-Z0-9-]+(.[_a-zA-Z0-9-]+)*$", $str)) return false;     
             return true;     
    }  
    
    /**
     * if int
     * @param String $value the value being tested
     * @param boolean $isEmpty if field can be empty
     * @return boolean
     */
    public static function isInt($str) {                
        return preg_match('!^\d+$!', $str);
    }
    
    /**
     * if is length
     * @param String $value the value being tested
     * @param int $min min of length
     * @param int $max max of length
     * @param boolean $isEmpty if field can be empty
     * @return boolean
     */
    public static function between($str, $min = -1, $max = 100) {   	
    	if (strlen($str) >= $min && strlen($str) <= $max)return true;
    	return false;
    }
    
    /**
     * if is Equal
     * @param String $value1 first  value
     * @param String $value2 third value
     * @param boolean $isEmpty if field can be empty
     * @return boolean
     */
    public static function eq($value1 = "", $value2 = "") {
    	if ((0 == strlen($value1)) && (0 == strlen($value2)))
    		return false;
    		
    	return 0 == strcmp($value1, $value2);
    }
    
    /**
     * if isNumber
     * @param String $value test value
     * @param boolean $isEmpty if field can be empty
     * @return boolean
     */
    public static function numeric($str) {   	
    	return preg_match('!^\d+(\.\d+)?$!',$str);
    }
    
    /**
     * if is reg exp
     * @param String $exp regule exp
     * @param String $value test value
     * @param $isEmpty if field can be empty
     */
    public static function regexp($exp, $value, $isEmpty = false) {
    	if (strlen($exp)) return false;
    	if(strlen($value) == 0)return $isEmpty;
         return (preg_match($exp, $value));
    }
    
    /**
     * check if value is empty
     * @param String $value
     * @param boolean
     */
    public static function required($str) {
    	return strlen(trim($str)) > 0;
    }
   
    public static function qq($str){
    	return preg_match('/^[1-9]\d{4,12}$/', trim($str));
    }
    
    public static function username($username){
    	if(Filter::email($username))
    		return true;
    	else
    	    return preg_match("/^[_a-zA-Z0-9]{4,32}$/i",$username);
     }
    
    public static function realname($str){ 
     	return preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $str)?true:false;
     }
       // 验证身份证(中国) 
    public static function idcard($str){
    	$length=strlen($str);
    if($length == 18 or $length == 15 ) {
    	 if($length==15){
    	 	   $str = substr($str, 0, 6) . '19'. substr($str, 6, 9); 
    	 	// 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
          if (array_search(substr($str, 12, 3), array('996', '997', '998', '999')) !== false){ 
                 $str = substr($str, 0, 6) . '18'. substr($str, 6, 9); 
          }
         $str = $str.idcard_verify_number($str); 
       }
    	 return idcard_verify_number(substr($str, 0, 17))==strtoupper(substr($str, 17, 1)); 
     }
     
     return false;
    }
    
    public static function isTime($time){
     return preg_match('/[\d]{4}-[\d]{1,2}-[\d]{1,2}\s[\d]{1,2}:[\d]{1,2}:[\d]{1,2}/', $time);
    }
    
    public static function mobile($str){
    	return preg_match('#^13[\d]{9}$|14^[0-9]\d{8}|^15[0-9]\d{8}$|^18[0-9]\d{8}$#', $str);
    }
    
    public static function zipcode($str){
     return preg_match('/^[1-9]\d{5}$/', trim($str));
    } 
   
     //验证座机电话
    public static function phone($str,$type){
     if(!$str=preg_replace("/\s/","",$str)){
     return false;
     }
    switch($type)
        {
            case "CHN":
                if(preg_match("/^([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str))
                {
                    return true;
                }else{
                    return false;
                }
               // break;
            case "INT":
                if(preg_match("/^[0-9]{4}-([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str))
                {
                    return true;
                }else{
                    return false;
                }
               // break;
        }

    }
    
    //特殊字符检测     
    public static function char($char)
	{ 
            if (!preg_match("/^[_a-zA-Z0-9]*$/", $char)) return false; 
            return true;        
	}
	
	
	/**
     * Ensure that filename does not contain exploits
     *
     * @param  string $filename
     * @return void
     * @throws Exception
     */
	 public static function filename($filename)
    {
        if (preg_match('/[^a-z0-9\\/\\\\_.:-]/i', $filename)) {           
            throw new Exception('Security check: Illegal character in filename');
        }
    }
     
     public static function money($str){     
        if (!ereg("^[0-9][.][0-9]$", $str)) return false;     
             return true;     
    }   
    
     function halt($dataType='json',$error=null){
     	//static $errors=array();
 		if(null!=$error){$this->errors[]=$error;}
 		
 		if(count($this->errors) > 0){
 		$response=array('status'=>false,'data'=>$this->errors);	
 		 switch($dataType){
  	  	   case 'json':
  	  	    exit(json_encode($response)); 				
  	  	   break;
  	  	   case 'jsonp':
  	  	    exit($_GET['callback'].'('.json_encode($response).')');	
  	  	   break;
  	  	 /*  default: 
  	  	    if(empty($next)){$next=$_SERVER['HTTP_REFERER'];}
  	  	    jump($next);*/  	  	     	     	
  	     }
 		}
 		//$errors=array();
 		return true;
 	}  
 	
   	/*public function __call($method, $arguments) {
 		$this->fuctions[$method] = $arguments;
 		return $this;   	 
 	}*/
   
}
// 计算身份证校验码，根据国家标准gb 11643-1999
function idcard_verify_number($idcard_base)
{
    if (strlen($idcard_base) != 17)
    { 
        return false; 
    }
    // 加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

    // 校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++){
    $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }

    $mod = $checksum % 11;
    $verify_number = $verify_number_list[$mod];

    return $verify_number;
}
?>