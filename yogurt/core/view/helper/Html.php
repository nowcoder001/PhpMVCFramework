<?php 
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
/*
 * @filesource		core/view/helper/Html.class.php
 * html类库作为模板组件有力补充，包括常用的UI组件如 form、datagird、tree、静态文件生成 等;
 */
 
class Html{ 
	 
    var $dir;        //dir for the htmls(without/)
    var $rootdir;    //root of html files(without/):html
    var $name;       //html文件存放路径
    var $dirname;    //指定的文件夹名称
    var $url;        //获取html文件信息的来源网页地址
    var $time;       //html文件信息填加时的时间
    var $dirtype;    //目录存放方式:year,month,,,,
    var $nametype;   //html文件命名方式:name
    public $fields = array();
    
    function Html($nametype='name',$dirtype='year',$rootdir='html')
    {
        $this->setvar($nametype,$dirtype,$rootdir);
    }
    
    public static function js_submit($url,$data=array()){
    	$str = "<script>\n";
    	$formStr='var inputArray= {';
    	 foreach((array)$data as $k =>$v){
        	$formStr.=" '$k':'$v'";       	
        }
        $formStr .="};\n";   	
    	$str .=' var formObj = document.createElement("form");	    
		formObj.method = "'.($data?'POST':'GET').'";			 
		formObj.action = "'.$url.'";
		formObj.target="_self";
		//formObj.style.display = "none";	
		'.$formStr.' for(k in inputArray){
			var input = document.createElement("input"); 
			    input.name=k;
			    input.value=inputArray[k];
			    formObj.appendChild(input);
		}
		document.body.appendChild( formObj );	
		formObj.body.submit();
		document.removeChild( formObj );';
    	$str .= "\n</script>";
	    die($str);
    }
    
    public static function form_submit($url,$data=array()){
    	echo "<html>\n";
        echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>\n";
        echo "<head><title>Processing Payment...</title></head>\n";
        echo "<body onLoad=\"document.forms['gateway_form'].submit();\">\n";
        echo "<form method=\"".($data?'POST':'GET')."\" name=\"gateway_form\" ";
        echo "action=\"$url\">\n";
        foreach ((array)$data as $name => $value)
        {
             echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
        }
        echo "</form>\n";
        echo "</body></html>\n";
    }
    
          
    function setvar($nametype='name',$dirtype='year',$rootdir='html')
    {
      $this->rootdir=$rootdir;
      $this->dirtype=$dirtype;
      $this->nametype=$nametype;
    }
 
    /**
 * Add a javascript file to the html file. 
 * @param String $files The location and name of the javascript files, you can send more than 1 parameter.
 * @return String The HTML code to add the file.
 * $js_files = load_js('js/form.js', 'js/validate.js', 'js/index.js'); 
$template->assign('js_files', $js_files); 
Template(smarty)- 
<head> 
<!-- some other javascript file that is common to all other pages that use this header template. --> 
"<script language="JavaScript" type="text/javascript" src="common.js"></script> 
{$js_files} 
</head>
 * 
 */
/*function load_js($files)
{
	$htmlString = "";
	for($i = 0; $i < func_num_args(); $i++)
	{
		$htmlString .= "<script language=\"JavaScript\" type=\"text/javascript\" src=\"" . func_get_arg($i) . "\"></script>\n";
	}

	return $htmlString;
}*/


// Output a javascript alert and jump to request url
function js_alert($msg, $url = '')
{
	$str = '<script>alert ("' . $msg . '");';
	if (empty($url))
	{
		$str .= 'window.location.href="' . $_SERVER['HTTP_REFERER'] . '";';
	}

	else
	{
		$str .= 'window.location.href="' . $url . '";';
	}

	$str .= '</script>';
	die($str);
}

// Output a javascript redirect
function js_redirect($url = '')
{
	$str = '<script>';
	if (empty($url))
	{
		$str .= 'window.location.href="' . $_SERVER['HTTP_REFERER'] . '";';
	}

	else {
		$str .= 'window.location.href="' . $url . '";';
	}

	$str .= '</script>';

	echo $str;
	die();
}
    
    function createdir($dir='')
    {
        $this->dir=$dir?$dir:$this->dir;
 
        if (!is_dir($this->dir))
        {
            $temp = explode('/',$this->dir);
            $cur_dir = '';
            for($i=0;$i<count($temp);$i++)
            {
                $cur_dir .= $temp[$i].'/';
                if (!is_dir($cur_dir))
                {
                @mkdir($cur_dir,0777);
                }
            }
        }
    }
 
    function getdir($dirname='',$time=0)
    {
        $this->time=$time?$time:$this->time;
        $this->dirname=$dirname?$dirname:$this->dirname;
 
        switch($this->dirtype)
        {
        case 'name':
        if(empty($this->dirname))
           $this->dir=$this->rootdir;
        else
           $this->dir=$this->rootdir.'/'.$this->dirname;
        break;
        case 'year':
        $this->dir=$this->rootdir.'/'.date("Y",$this->time);
        break;
 
        case 'month':
        $this->dir=$this->rootdir.'/'.date("Y-m",$this->time);
        break;
 
        case 'day':
        $this->dir=$this->rootdir.'/'.date("Y-m-d",$this->time);
        break;
        }
 
        $this->createdir();
 
        return $this->dir;
    }
 
    function geturlname($url='')
    {
        $this->url=$url?$url:$this->url;
 
        $filename=basename($this->url);
        $filename=explode(".",$filename);
        return $filename[0];
    }
 
    function geturlquery($url='')
    {
        $this->url=$url?$url:$this->url;
 
        $durl=parse_url($this->url);
        $durl=explode("&",$durl[query]);
        foreach($durl as $surl)
        {
          $gurl=explode("=",$surl);
          $eurl[]=$gurl[1];
        }
        return join("_",$eurl);
    }
 
    function getname($url='',$time=0,$dirname='')
    {
        $this->url=$url?$url:$this->url;
        $this->dirname=$dirname?$dirname:$this->dirname;
        $this->time=$time?$time:$this->time;
 
        $this->getdir();
 
        switch($this->nametype)
        {
        case 'name':
        $filename=$this->geturlname().'.htm';
        $this->name=$this->dir.'/'.$filename;
        break;
 
        case 'time':
        $this->name=$this->dir.'/'.$this->time.'.htm';
        break;
 
        case 'query':
        $this->name=$this->dir.'/'.$this->geturlquery().'.htm';
        break;
 
        case 'namequery':
        $this->name=$this->dir.'/'.$this->geturlname().'-'.$this->geturlquery().'.htm';
        break;
 
        case 'nametime':
        $this->name=$this->dir.'/'.$this->geturlname().'-'.$this->time.'.htm';
        break;
 
        }
        return $this->name;
    }
 
    function createhtml($url='',$time=0,$dirname='',$htmlname='')
    {
        $this->url=$url?$url:$this->url;
        $this->dirname=$dirname?$dirname:$this->dirname;
        $this->time=$time?$time:$this->time;
      //上面保证不重复地把变量赋予该类成员
        if(empty($htmlname))
            $this->getname();
        else
            $this->name=$dirname.'/'.$htmlname;  //得到name
            
 
        $content=file($this->url) or die("Failed to open the url ".$this->url." !");;
 
        //关键步---用file读取$this->url
 
        $content=join("",$content);
        $fp=@fopen($this->name,"w") or die("Failed to open the file ".$this->name." !");
        if(@fwrite($fp,$content))
        return true;
        else
        return false;
        fclose($fp);
    }
/////////////////以name为名字生成html
 
    function deletehtml($url='',$time=0,$dirname='')
    {
        $this->url=$url?$url:$this->url;
        $this->time=$time?$time:$this->time;
 
        $this->getname();
 
        if(@unlink($this->name))
        return true;
        else
        return false;
    }
 
    /**
     * function::deletedir()
     * 删除目录
     * @param $file 目录名(不带/)
     * @return 
     */
     function deletedir($file)
     {
        if(file_exists($file))
        {
            if(is_dir($file))
            {
                $handle =opendir($file);
                while(false!==($filename=readdir($handle)))
                {
                    if($filename!="."&&$filename!="..")
                      $this->deletedir($file."/".$filename);
                }
                closedir($handle);
                rmdir($file);
                return true;
            }else{
                unlink($file);
            }
        }
    }
    
    
	/**
	 * 构建下拉框
	 *
	 * @param string $selName select的名称
	 * @param array $source 数据源
	 * @param mixed $default 默认值
	 * @param array $attrArray 属性数组
	 * @param boolean emptyChoose 是否加入请选择
	 */
	public function select($selName, $source, $default=null, $attrArray=null, $emptyChoose=true)
	{
		$html = "<select name=\"$selName\"";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= "$key=\"$value\" ";
			}
		}
		$html .= $attrStr . ">\n";
		if($emptyChoose)
			$html .= "<option value=''>请选择</option>\n";

		foreach($this->$source as $k => $v)
		{
			if ($k == $default) 
			{				
				$html .= "<option value=\"$k\" selected=\"selected\">$v</option>\n";
			}
			else
			{
				$html .= "<option value=\"$k\">$v</option>\n";
			}
		}
		$html .= "</select>\n";
		return $html;
	}

	/**
	 * 构建单选框
	 *
	 * @param string $radioName radio的名称
	 * @param array $source 数据源
	 * @param mixed $default 默认值
	 * @param array $attrArray 属性数组
	 */
	public function radio( $radioName, $source, $default=null, $attrArray=null )
	{
		$html = "";
		$flag = false;
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= "$key=\"$value\" ";
			}
		}
		foreach($source as $k => $v)
		{
			$id = $radioName . "_" . $k;
			if($k == $default || ($default == null && $flag == false))
			{				
				$html .= "<input type=\"radio\" name=\"$radioName\" value=\"$k\" checked=\"checked\" id=\"$id\""
					   . $attrStr . "/>\n"
					   . "<label for=\"$id\" id=\"label_$id\">$v</label>\n";
				$flag = true;
			}
			else
			{
				$html .= "<input type=\"radio\" name=\"$radioName\" value=\"$k\" id=\"$id\""
					   . $attrStr . "/>\n"
					   . "<label for=\"$id\" id=\"label_$id\">$v</label>\n";
			}			
		}
		return $html;
	}

	/*
	 * 构建复选框
	 *
	 * @param string $radioName radio的名称
	 * @param array $source 数据源
	 * @param mixed $default 默认值
	 * @param array $attrArray 属性数组
	 */
	public function checkbox($checkName, $source, $default=null, $attrArray=null)
	{
		$html = "";
		$attrStr = " ";
		if(!empty($attrArray) && is_array($attrArray))
		{
			foreach($attrArray as $key => $value)
			{
				$attrStr .= "$key=\"$value\" ";
			}
		}
		foreach($source as $k => $v)
		{
			$id = $checkName . "_" . $k;
			if(is_array($default) && in_array($k, $default))
			{
				$html .= "<input type=\"checkbox\" name=\"$checkName\" value=\"$k\" checked=\"checked\" id=\"$id\""
					   . $attrStr . "/>\n"
					   . "<label for=\"$id\" id=\"label_$id\">$v</label>\n";
			}
			else
			{
				$html .= "<input type=\"checkbox\" name=\"$checkName\" value=\"$k\" id=\"$id\""
					   . $attrStr . "/>\n"
					   . "<label for=\"$id\" id=\"label_$id\">$v</label>\n";
			}
		}
		return $html;
	}
	
	/*
	 * html的格式化
	 */
	public function h($str)
	{
		 return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($str)));
	}
 
}
?>