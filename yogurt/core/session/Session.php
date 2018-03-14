<?php

class Session extends Object
{
    private static $instance = null;
    private static $saveType = Config::SESS_SAVE_TYPE;
    private static $sessionName = Config::SESS_NAME;
    private static $lifeTime = Config::SESS_LIFE_TIME;
    private static $garbage =  Config::SESS_GARBAGE;
    private static $allowAgent = Config::SESS_ALLOW_AGENT;
    private static $agent = '';
    private static $domain = DN;

    private function Session() {
    	self::init();
        session_start();
    }

    /**
     * 初始化
     */
    private function init() {
	$lifeTime = ('' === self::$lifeTime || 0 == self::$lifeTime)
    		  ? 31536000 //60 * 60 * 24 * 365
    		  : self::$lifeTime;
    	if (function_exists('ini_set')) {
	    ini_set('session.use_trans_sid', 0);
	    ini_set('url_rewriter.tags', '');
	    ini_set('session.use_cookies', 1);
	    ini_set('session.name', self::$sessionName);
	    ini_set('session.cookie_lifetime', 0);
	    ini_set('session.gc_maxlifetime', $lifeTime);
	    ini_set('session.gc_probability', self::$garbage);
	    ini_set('session.gc_divisor', 100);
	    ini_set('session.auto_start', 0);
	    //ini_set('session.cookie_domain', self::$domain);
	    if(empty(self::$saveType))
		return;
	    ini_set('session.save_handler', 'user');
	    ini_set('session.serialize_handler', 'php');
	}

  	// session save type: Files, Memory, Database
  	$save_type = self::$saveType;
  	if(!$save_type)
  	    return;

    	$sessClassName = "Session".ucfirst(strtolower($save_type));
	Y::import('core.session.' . $sessClassName);
	$handler = new $sessClassName;echo $handler;
	session_set_save_handler(array($handler, 'open'),
				 array($handler, 'close'),
				 array($handler, 'read'),
				 array($handler, 'write'),
				 array($handler, 'destroy'),
				 array($handler, 'gc'));
    }

    public static function getInstance() {
    	if (null != self::$instance)
    	    return self::$instance;

    	return self::$instance = new Session();
    }

    /**
     * validate agent
     * @return boolean
     */
    public static function validAgent() {
    	if ('' == self::$allowAgent || '*' == self::$allowAgent) {
    	    return true;
    	} else {
    	    $allowAgent = explode(self::$allowAgnet);
    	    foreach ($allowAgent AS $value) {
    		if (preg_match('/' . $value . '/i', self::$agent)) {
    		    return true;
    		}
    	    }
    	    return false;
    	}
    }

    /**
     * set one session var=>$value
     * @param string $name
     * @param mix $value
     * @param boolean $isArray
     * @return string
     */
    public function setValue($name, $val) {
	$_SESSION[md5($name)] =$val;
    }

    /**
     * get one session var
     * @param string name
     * @param boolean $isArray
     * @return string
     */
    public function getValue($name) {
	$name = md5($name);
	return isset($_SESSION[$name])?$_SESSION[$name]:null;
    }

    public function getLang() {
        return $this->getValue('lang');
    }

    /**
     * set one array session var=>$value
     * @param string $array array's name
     * @param string $val
     * @return string
     */
    public function setArrayValue($name, $val) {
	(array)$_SESSION[md5($name)][] = $val;
    }

    /**
     * get one array session var
     * @param string $name
     * @return string
     */
    public function getArrayValue($name) {
	$name  = md5($name);
	return isset($_SESSION[$name])?$_SESSION[$name]:array();
    }

    // 分解 session 值
    public function decodeSession($data){
   	if(  strlen( $data) == 0)
	{
            return array();
	}
	/*	$vars=preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
           $data,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	   for($i=0; $vars[$i]; $i++) $result[$vars[$i++]]=unserialize($vars[$i]);
	   return $result;
	 */
	// match all the session keys and offsets
	preg_match_all('/(^|;|\})([a-zA-Z0-9_]+)\|/i', $data, $matchesarray, PREG_OFFSET_CAPTURE);

	$returnArray = array();

	$lastOffset = null;
	$currentKey = '';
	foreach ( $matchesarray[2] as $value )
	{
            $offset = $value[1];
            if(!is_null( $lastOffset))
            {
		$valueText = substr($data, $lastOffset, $offset - $lastOffset );
		$returnArray[$currentKey] = unserialize($valueText);
            }
            $currentKey = $value[0];

            $lastOffset = $offset + strlen( $currentKey )+1;
	}

	$valueText = substr($data, $lastOffset );
	$returnArray[$currentKey] = unserialize($valueText);
	return $returnArray;
    }

    public function getSettings()
    {
        // get the settings
        $gc_maxlifetime = ini_get('session.gc_maxlifetime');
        $gc_probability = ini_get('session.gc_probability');
        $gc_divisor     = ini_get('session.gc_divisor');

        // return them as an array
        return array(
            'session.gc_maxlifetime'    =>  $gc_maxlifetime . ' seconds (' . round($gc_maxlifetime / 60) . ' minutes)',
            'session.gc_probability'    =>  $gc_probability,
            'session.gc_divisor'        =>  $gc_divisor,
            'probability'               =>  $gc_probability / $gc_divisor * 100 . '%',
        );
    }

    /**
     * delete one session var
     */
    public function del($name) {
	unset($_SESSION[md5($name)]);
    }

    /**
     * clean session array
     */
    public function clean() {
	foreach ($_SESSION AS $key => $value) {
	    unset ($_SESSION[$key]);
	}
	session_unset();
	session_destroy();
	self::$instance = null;
    }

}
?>
