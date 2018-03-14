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
 * session save to Redis handle class of yogurt framework.
 * @filesource		yogurt/core/session/SessionRedis.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
       
class SessionRedis {
	private static $redis = null;
	
    /**
     * open handle for session save handle
     * @param string $savePath
     * @param string $sessionName
     */
    public static function open($savePath, $sessionName) {
    	return self::$redis = new RedisUtils('main');  	
    }
    
    /**
     * read handle for session save handle
     * @param string $key
     * @return string
     */
    public static function read($key) {
    	$val = self::$redis->hget("sessions",$key);   	
    	return (false !== $val) ? $val : null;
    }
    
    /**
     * wirte handle form session save handle
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public static function write($key, $val) {
    	return self::$redis->hset("sessions",$key, $val);
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
    	self::$redis->hdel("sessions",$key);
    }
    
    /**
     * gc handle for session save handle
     */
    public static function gc($maxLifeTime) {
    	return true;
    }
}
?>