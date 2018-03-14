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
 * session save to database handle class of yogurt framework.
 * @filesource		yogurt/core/session/SessionMysql.class.php
 * @copyright		Copyright (c)2009-2010, rick <158672319@qq.com> 2009-8-21
 * @link			http://www.yogurt-framework.com/download/
 * @since			Yogurt v 0.9
 * @version			$3.0
 *  sql: 

CREATE TABLE `sessions` (
  `id` char(32) NOT NULL DEFAULT '',
  `val` blob NOT NULL,
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;
 */

class SessionMysql {		
	private static $dao = null;
	private static $sess_table = 'sessions';

    /**
     * open session table
     * @param string $savePath
     * @param string $sessionName
     */
    public static function open($savePath, $sessionName) {    	
    	/* self::$dao= new DaoMysql();//取得 原生
    	 self::$dao->connect(); */
    	 self::$dao=Model::getDao();
    	 self::$sess_table=TBL_PREFIX.self::$sess_table;      	          
    	return true;
    }
    
    /**
     * read session value from database
     * @param string $key
     * @return string
     */
    public static function read($key) {
    	//$key = self::$dao->escape($key); 
    	$strQuery = 'SELECT val FROM ' . self::$sess_table . 
					' WHERE id = "' . $key.
					'" AND expiry > ' . time().' LIMIT 1';
				
		return self::$dao->getOne($strQuery);
    }
    
    /**
     * write session data to database
     * @param string $key
     * @param string $value
     */
    public static function write($key, $value) {
    	$lifeTime=time() + ini_get('session.gc_maxlifetime'); //gc 回收时间   
    	$uid=(int)$_SESSION[md5("userinfo")][Config::AUTH_ID];
    	//$key = self::$dao->escape($key); 
    	$value=self::$dao->escape($value);
    	$strQuery = 'INSERT INTO ' . self::$sess_table . 
    				' VALUES("'.$key.'" ,"'.$value.'",'.$lifeTime.' ,'.$uid.')'.
                    ' ON DUPLICATE KEY UPDATE val="'.$value.'", uid='.$uid.',' .
    				' expiry='.$lifeTime;
    	//write_file_log($strQuery,"session/php");				
       return self::$dao->execute($strQuery);    		
    }	

    /**
     * gc method for session save handle
     * @param $maxLifeTime
     */
    public static function gc($lifeTime) {
    	$strQuery = 'DELETE FROM ' . self::$sess_table . 
    				' WHERE expiry < ' .(time()-$lifeTime) ;
    	return self::$dao->execute($strQuery);
    }
         
    /**
     * destory session value
     */
    public static function destroy($key) {
    	//$key=self::$dao->escape($key);
    	$strQuery = 'DELETE FROM ' . self::$sess_table .
    				' WHERE id = "' .$key.'"';
    	return self::$dao->execute($strQuery);
    }
     /**
     * close session db
     */
    public static function close() {
    	self::$dao = null;
    	return true;
    }
}

?>