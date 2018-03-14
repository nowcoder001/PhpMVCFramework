<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */

class Url {
    private $rewriteType = Config::REWRITE_NO;
    private static $url;
    private static $uri;
    private static $host;
    private static $port;
    private static $file;
    private static $query;

    public static function getDomain($url=null) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : $_SERVER['HTTP_HOST'];
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
	    $prefix = substr($domain,0,strlen($domain)-strlen($regs['domain'])-1);
            return array("domain"=>$regs['domain'],"prefix"=>$prefix);
        }
        return false;
    }

    /**
     * get uri string
     * @return String
     */
    public static function getUri() {
	return self::$uri = $_SERVER['REQUEST_URI'];
    }

    /**
     * get url string
     * @return string
     */
    public static function getUrl() {
    	return self::$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    /**
     * get host
     * @return String
     */
    public static function getHost() {
    	return self::$host = $_SERVER['HTTP_HOST'];
    }

    /**
     * get port
     * @return int
     */
    public static function getPort() {
    	return self::$port = $_SERVER['SERVER_PORT'];
    }

    /**
     * get query string
     * @return String
     */
    public static function getQuery() {
    	return self::$query = $_SERVER['QUERY_STRING'];;
    }

    /**
     * get execute file name
     * @return String
     */
    public static function getFile() {
    	return self::$file = $_SERVER['PHP_SELF'];
    }

    /**
     * processRouter: Url路由,分解成物理文件
     *
     * yogurt suport 2 URL styles:
     *  1. '/?actionAccessor=moduleValue!actionValue&param1=paramValue1&..&paramN=paramValueN'
     * 	2. '/moduleValue/actionValue/param1/paramValue1/.../paramN/paramValueN.SuffixName'
     *
     * 2 use mod_rewrite, before use this styles, web server must suport  mod_rewrite.
     * in httpd.conf VirtualHost:
     * #######################################################
     *  RewriteEngine on
     *  #RewriteLog 'rewrite.log'
     *  #RewriteLogLevel 9
     *
     * RewriteCond %{DOCUMENT_ROOT}%{SCRIPT_FILENAME} !-f
     * RewriteCond %{DOCUMENT_ROOT}%{SCRIPT_FILENAME} !-d
     * RewriteCond %{SCRIPT_FILENAME} !\.(js|ico|gif|jpg|png|css|swf)

     * RewriteRule ^/(.*)$ /index.php
     * ########################################################
     *
     * @param string $requestUrl
     */
    public function processRouter($requestUrl = '') {
    	if(empty($requestUrl)){$requestUrl=$_SERVER['REQUEST_URI'];}
    	if (preg_match('/^\/[[:alnum:]\-_\.]+\/(.*)/', $requestUrl)) { // url like '/news/add/type/1.html'
	    $this->rewriteType = Config::REWRITE_DIR;
    	}
    	return self::getUriArray($requestUrl, $this->rewriteType);
    	//Y::debug('processRouter() method, current url = ' . $requestUrl . ', rewriteType = ' . $this->rewriteType,'Test','debug');
    }

    /**
     * get uri array from url  yogurt 定义的URL格式为：模块[目录+类名(最后一个数值)] / 方法 / 参数 三部分固定组成
     * @param string $url
     * @param int $rewriteType
     * @return array('moduleName','actionName','params');
     */
    public static function getUriArray($uri, $rewriteType = Config::REWRITE_NO) {
    	$uri=trim($uri,'/');
    	$urlArray = array('params'=>array());
    	$params=$moduleArray=array();

    	switch($rewriteType){
    	    case Config::REWRITE_DIR:
                if (false !== ($pos = strpos($uri, '?'))){
                    /* $url_dir2 ="admin[.user or :user]/login/?a=AA&b=B7&c=9.8&d=中国人";
    		     * $url_dir3 ="openapi/login.html[do]?a=AA&b=B7&c=9.8&d=16"
    		     */
    		    $moduleArray=explode('/',substr($uri,0,$pos));
		    parse_str(substr($uri,$pos+1,strlen($uri)),$urlArray['params']);

    	        }else{
    	     	    //$url_dir1 ="openapi/login/a/AA/b/B7/c/9.8/d/16.html";
    	     	    $moduleArray=explode('/',$uri);
    	     	    //unset($moduleArray[0],$moduleArray[1]);

    	     	    if(is_array($moduleArray)){
    	     		if(!is_numeric($str=end($moduleArray))&&false!==($pos = strrpos($str, '.'))) $moduleArray[array_search(end($moduleArray),$moduleArray)]=substr($str,0,$pos);
    	     		foreach($moduleArray as $k =>$v){
    	     		    if (0 ==  $k % 2) {
				$urlArray['params'][$v] = $moduleArray[$k+1];
			    }
    	     		}
    	     	    }
    	        }
    		break;

    	    default:   //文件由= 得出
    		/*  $url_default1="?aa=openapi!login&a=AA&b=B7&c=9.8&d=16";
                 *  $url_default2="login.html?aa=openapi!login&a=AA&b=B7&c=9.8&d=16";
                 *  $url_default3 ="?a=AA&b=B7&c=9.8&d=16"
    		 */
    		$uriArray=parse_url($uri);

    		parse_str($uriArray['query'], $urlArray['params']);
    		$urlArray['params']['path']=$uriArray['path'];
    		$moduleArray=explode(Config::ACTION_SEPARATOR,reset($urlArray['params']));
    	}
        //     	     if(!(count($moduleArray)>1)){$moduleArray[0]=$moduleArray[1]=null;}
        //     		 $urlArray['moduleName'] = $moduleArray[0];
        // 			 $urlArray['actionName'] = $moduleArray[1];
        // 			 if(false !== ($apos = strpos($moduleArray[1], '.'))){
        //     		 	   $urlArray['actionName'] = substr($moduleArray[1],0,$apos);
        //     		 }
        $action_array = explode('!', $urlArray['params']['action']);
        $urlArray['moduleName'] = $action_array[0];
        $urlArray['actionName'] = $action_array[1];

    	return  array_merge_recursive($urlArray,array($rewriteType)) ;
    }
    public static function prevUrl(){}
    /**
     * process url
     * @param mix $url
     * @param mix $params only array or string(like 'xx=1,bb=a')
     * @param int $rewriteType
     * @return string
     */
    public static function processUrl($url = '', $params = '', $rewriteType = Config::REWRITE_NO) {
    	if ('' == $url) {
    	    return '/';
    	}
    	$accessorArray = self::getAccessor($rewriteType);
    	$paramAccessor = $accessorArray['paramAccessor'];
    	$valueAccessor = $accessorArray['valueAccessor'];
    	unset($accessorArray);

    	if (!is_array($url)) {
    	    $urlArray = self::getUriArray($url, $rewriteType);
    	} else {
    	    $urlArray = $url;
    	}

    	if (Config::REWRITE_NO != $rewriteType) {
    	    $redirectUrl = '/' . $urlArray['moduleName'] . $paramAccessor . $urlArray['actionName'];
    	} else {
    	    $redirectUrl = '/?action=' . $urlArray['moduleName'] . Config::ACTION_SEPARATOR . $urlArray['actionName'];
    	}

    	//process params to array
    	if (!is_array($params)) {
    	    $paramsArray = explode(',', trim($params));

    	    $params = array();
    	    foreach ($paramsArray AS $value) {
    		if ('' != $value) {
    		    $row = explode('=', trim($value));
    		    $params[trim($row[0])] = trim($row[1]);
    		}
    	    }
    	}

    	if (!array_key_exists('params', $urlArray)) {
    	    $urlArray['params'] = array();
    	}

    	$newParamsArray = array_merge($urlArray['params'], $params);
    	$paramUrl = "";
    	foreach ($newParamsArray AS $key => $value) {
    	    $paramUrl .= $paramAccessor . $key . $valueAccessor . $value;
    	}
    	unset($newParamsArray);

    	$redirectUrl .= $paramUrl . $urlArray['suffix'];

    	return $redirectUrl;
    }

    static public function arrurl() {
        $args = func_get_args();
        $method = end($args);
        array_pop($args);
        return "/?action=".implode('.', $args)."!".$method;
    }
}
?>
