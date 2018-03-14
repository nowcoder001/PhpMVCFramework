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
 *  Http 对手册中提及HTTP中的方法进行功能性的封装
 */
class HttpUtils {
	 
   //http://hi.baidu.com/v7aju8bo/blog/item/aa1030fb9eb4a46a034f56e2.html
   //http://www.hebaodans.com/2009/07/p-h-p-c-u-r-l-yong-fa/
    public static function curlRequest($url,$fields=array()){
     if (!function_exists('curl_init')) 
     throw new Exception('curl is not enable!');
    
    $ch = curl_init();    
   	curl_setopt($ch, CURLOPT_URL, $url);
   	curl_setopt($ch, CURLOPT_TIMEOUT, Config::HTTP_REQUEST_TIME);  // 设置超时限制防止死循环
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回   
 	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
 	curl_setopt($ch, CURLOPT_VERBOSE, 1);
 	//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    //curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
   	//---https
   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //从证书中检查SSL加密算法是否存在  
    // --- authentication
    /*
      curl_setopt($ch, CURLOPT_USERPWD, $usr.':'.$pwd);
     */
    //---- PUT/POST
    if(is_array($fields)&&!empty($fields)){   	
   	$fields_string = http_build_query($fields);
   	/* or 
   	 * //url-ify the data for the POST
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string,'&');
   	 */
    curl_setopt($ch, CURLOPT_POST, count($fields));     
   	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);   	
    }   
   	 //----cookie   
    curl_setopt($ch, CURLOPT_COOKIE, 1);     
   	curl_setopt($ch, CURLOPT_COOKIEJAR, CACHE_DIR.'cookie.txt'); 
   	curl_setopt($ch, CURLOPT_COOKIEFILE, CACHE_DIR.'cookie.txt');// 读取上面所储存的Cookie信息 

   	$result= curl_exec($ch);     
   	//self::$status = curl_getinfo($ch,CURLINFO_HTTP_CODE);
   	
   	if (curl_errno($ch)) 
   	{   
      // echo 'Errno'.curl_error($ch);   
    }   
   	curl_close($ch); 
   	return $result;
     }

	/**
	* Perform parallel cURL request.
	*
	* @param array $urls Array of URLs to make request.
	* @param array $options (Optional) Array of additional cURL options.
	* @return mixed Results from the request (if any).
	*/
	public function curlMultiRequest($urls, $options = array()) {
	    $ch = array();
	    $results = array();
	    $mh = curl_multi_init(); 
	    foreach($urls as $key => $val) {
	        $ch[$key] = curl_init();
	        if ($options) {
	            curl_setopt_array($ch[$key], $options);
	        }
	        curl_setopt($ch[$key], CURLOPT_URL, $val);
	        curl_multi_add_handle($mh, $ch[$key]);
	    }
	 
	    $running = null;
	    do {
	        curl_multi_exec($mh, $running);
	    }
	    while ($running > 0);
	 
	    // Get content and remove handles.
	    foreach ($ch as $key => $val) {
	        $results[$key] = curl_multi_getcontent($val);
	        curl_multi_remove_handle($mh, $val);
	    }
	 
	    curl_multi_close($mh);
	    return $results;
	}
    
	public function post_data($url){
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10);
	return curl_exec($ch);
    }
	
	//http://www.itokit.com/2011/0529/66329.html
    public static function fileRequest($url,$data=array()){
	    $http=array('method'=>'GET','header'=>"Accept-language: en\r\n",'timeout'=>Config::HTTP_REQUEST_TIME);
		if(is_array($data)&&!empty($data)){ 
		 	// 创建表单的数据 $data 
		foreach($data as $key=>$value) 
		$values[]="$key=".urlencode($value); 
		$queryString=implode("&",$values); 
		$http=array('method'=>'POST','header'=>"Content-type: application/x-www-form-urlencoded\r\n".  
		               "Content-length:".strlen($queryString)."\r\n" .   
		               "Cookie: foo=bar\r\n" .   
		               "\r\n",'content'=>$queryString);
		}
	    $opts = array('http'=>$http);
	    $context = stream_context_create($opts);
	    
	    // Open the file using the HTTP headers set above
	    return file_get_contents($url, false, $context);
    }
    
    /**
     * http://www.nowamagic.net/academy/detail/12220214
     *
     */
    public function sockRequest($url,$data=array()){
    	// 解析所给的地址
    	$uri=parse_url($url);
    	if(!isset($uri["port"])) $port=80;
    	$path = $uri['path'] ? $uri['path'].($uri['query'] ? '?'.$uri['query'] : '') : '/';
    	$method ='GET';
    	if(is_array($data)&&!empty($data)){
    		// 创建表单的数据 $data
    		foreach($data as $key=>$value)
    			$values[]="$key=".urlencode($value);
    		$queryString=implode("&",$values);
    		$method ='POST';
    	}
    
    	$fp = fsockopen($uri["host"],$port, $errno, $errstr, Config::HTTP_REQUEST_TIME);
    
    	if(!$fp){echo "$errstr ($errno)<br />\r\n";die;}
    
    	// 创建post表单请求:
    	$header   =$method." ".$path." HTTP/1.0\r\n";
    	$header   .="Host: ".$uri["host"]."\r\n";
    	//$header .= "Referer: http://".$uri['host'].$uri['path']."\r\n";
    	//$header.= "Cookie: ". session_id()."; path=/;\r\n";
    	//$header  .="Connection: Close\r\n\r\n";
    	//$header  .="Content-Type: text/html; charset=UTF-8 ";
    	if($method =='POST'){
    		$header.="Content-type: application/x-www-form-urlencoded\r\n";
    		$header.="Content-Length: ".strlen($queryString)."\r\n";
    		$header .= "\r\n";
    		$header.=trim($queryString);
    	}
    	$header .= "\r\n";
    	fwrite($fp, $header);
    	while(!feof($fp)) {
    		$result .= fread($fp,4096);//fgets($fp, 128);
    		//echo fgets($fp, 128);
    	}
    	fclose($fp);
    	return $result;
    }
    
}
?>
