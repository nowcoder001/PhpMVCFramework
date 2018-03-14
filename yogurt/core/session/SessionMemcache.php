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
 * session save to memcache handle class of yogurt framework.
 * @filesource		yogurt/core/session/SessionMemcache.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
       
class SessionMemcache {
	private static $mc = null;
	
    /**
     * open handle for session save handle
     * @param string $savePath
     * @param string $sessionName
     */
    public static function open($savePath, $sessionName) {
    	self::$mc = new MC('session');
    	return true;
    }
    
    /**
     * read handle for session save handle
     * @param string $key
     * @return string
     */
    public static function read($key) {
    	$value = @self::$mc->get($key);   	
    	return (false !== $value) ? $value : null;
    }
    
    /**
     * wirte handle form session save handle
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public static function write($key, $value) {
    	return self::$mc->set($key, $value, 0, ini_get('session.gc_maxlifetime'));
    }
    
    /**
     * close handle
     */
    public static function close() {
    	return true;
    }
    
    /**
     * destory handle for session save handle
     */
    public static function destroy($key) {
    	self::$mc->delete($key, 0);
    }
    
    /**
     * gc handle for session save handle
     */
    public static function gc($maxLifeTime) {
    	return true;
    }
}
?>