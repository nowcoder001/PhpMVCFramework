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
 * cookie class of yogurt framework.
 * @filesource		yogurt/utils/Cookie.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

class Cookie{
	private static $instance;	

	/**
	 * construct of Cookie class
	 */
	private function Cookie() {
	}
	
	/**
	 * get cookie instance
	 * @return object
	 */
	public static function getInstance() {
		 if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
	}
	
	/**
	 * set one cookie var=>$value
	 * @param string $name
	 * @param mix $value
	 * @param boolean $isArray 
	 * @return string 
	 */
	public function setValue($name, $value, $expire = null) {
		if(is_null($expire)){$expire=time() + 108000;}//默认保存一个月		
		return setcookie(md5($name), $value, $expire, '/', DN);
	}
	
	/**
	 * get one cookie var
	 * @param string name
	 * @param boolean $isArray
	 * @return string 
	 */
	public function getValue($name) {
		$name = md5($name);
		return isset($_COOKIE[$name])?$_COOKIE[$name]:null;
	}
	
	/**
	 * set one array cookie var=>$value
	 * @param string $array array's name
	 * @param string $name
	 * @return string 
	 */
	public function setArrayValue($name, $val) {
		(array)$_COOKIE[md5($name)][] = $val;	
	}
	
	/**
	 * get one array cookie var
	 * @param string $array
	 * @param string $name
	 * @return string
	 */
	public function getArrayValue($name) {
			$name  = md5($name);
		return isset($_COOKIE[$name])?$_COOKIE[$name]:array();		
	}
	
	/**
	 * delete one cookie var
	 */
	public function del($name) {	
		setcookie(md5($name), '', time()-ini_get('session.gc_maxlifetime'), '/', DN);
	}
		
	/**
	 * clean cookie array
	 */
	public function clean() {		
		foreach ($_COOKIE AS $key=>$value) {
		 setcookie($key, "", time()-ini_get('session.gc_maxlifetime'), '/', DN);
		}
		unset ($_COOKIE);
		self::$instance = null;
	}
}
?>