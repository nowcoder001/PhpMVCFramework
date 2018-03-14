<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
if ( !extension_loaded('sqlite3') ) {
           throw new Exception('sqlite3 is not enable!');            	
}
class Sqlite3Utils  {
    private static $sqlite3 = null;
	
    /**
	 * construct of DaoSqlite3
	 */
	public function __construct() {
		
	}
	/** 
	 * when sqlite $dbname = ':memory:', close sqlite database will delete all data;
	 * 
	 * construts of sqlite3
	 * 
	 * notice: Starting with SQLite library version 2.8.2, you can specify :memory: 
	 * as the filename to create a database that lives only in the memory of the computer. 
	 * This is useful mostly for temporary processing, as the in-memory database 
	 * will be destroyed when the process ends. It can also be useful when coupled 
	 * with the ATTACH DATABASE SQL statement to load other databases and move and 
	 * query data between them
	 *
	 */
	public function connect($schema=':memory:') {   	
    	if (null != $this->sqlite3) {
    		return $this->sqlite3;
    	}
    	
       if(!$this->sqlite3=new SQLite3($schema)){
         	Y::errors(403,'sqlite cache file: $sqliteDbFile can\'t open!');	        	
         } ;	
       return $this->sqlite3;
    }
     
         /**
         * 执行SQL命令
         *
         * @access      public
         * @param       string    $sql    SQL命令
         * @param       resource  $sqlite3   数据库连接标识
         * @return      bool              是否执行成功
         */
    public function execute($sql = '',$showError=true)
        {              	
        	$sql = empty($sql) ? $this->sql : $sql;
        	$stime = $this->microtime_float();	
            $result=$this->sqlite3->exec($sql);
            $etime = $this->microtime_float();
            $this->queryString[] = $etime-$stime.' '.$sql; 
            if($result){
            	$lastid=$this->sqlite3->lastInsertRowID();  
            	return $lastid?$lastid:$result;
            }
                 
           return  $showError?Y::errors(500,$this->sqlite3->lastErrorMsg()." in SQL: $sql"):false;	
		}
	
	/**
	 * query method return a result
	 * @param string $sql
	 * @param string $prefix : one、row、list
	 * @return value
	 */
	public function query($sql = '', $prefix="list"){
		$sql = ('' != $sql) ? $sql : $this->sql;
        $value = null;			
		//get one value from db and save to cache
		$cachetime=abs($this->options['cachetime']);
		 if($cachetime>0){
		$cacheClass =Y::factory($this->cacheType,'Cache');
        $cacheClass->setLifeTime($cachetime);//设定缓存时间
		$value =$cacheClass->get($sql);
		 }
		if (null == $value) {
			//get record from db				 
			$stime =  $this->microtime_float();	
			switch ($prefix) {		
				case 'one':
					$value = $this->sqlite3->querySingle($sql);
					break;		 
				case 'row':
				  $value=$this->sqlite3->querySingle($sql, true);
				   break;
				case 'list':
				   $result = $this->sqlite3->query($sql);
                    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                       $value[]=$row;
                     }
				 break;					
		     }	
		     $etime = $this->microtime_float();
             $this->queryString[] = $etime-$stime . ' ' . $sql;
			 if(is_object($cacheClass))$cacheClass->set($sql,$value);
		     $this->options = array(); 
		}
		  return $value;
    }
    
    /**
	 * get Sqlite3 Stmt
	 * @param string $sql
	 */
	private function getSqlite3Stmt($sql) {		
		$stmt = $this->sqlite3->prepare($sql);		
		if (is_array($this->bindArray)) {
			foreach ($this->bindArray AS $key => $bindArray) {
				$stmt->bindParam($bindArray['param'], $bindArray['value'], $bindArray['type']);
			}
		}
		$this->bindArray = array();
		return $stmt;
	}
	
	public function escape($val=''){  	 
   	     return is_numeric($val) ? $val :$this->sqlite3->quote($val);
   }
    
    /*function open() {
    	 self::$db = new SQLite3(SERVERS_DIR.'/appdata/session.db');	
    	 self::$sess_table=TBL_PREFIX.self::$sess_table;      	         
         self::$db->exec(" CREATE TABLE IF NOT EXISTS `".self::$sess_table."` (
                                  `session_key` varchar(32) NOT NULL DEFAULT '',
                                  `session_value` blob NOT NULL,
                                  `session_expiry` int(11)  NOT NULL DEFAULT '0',
                                  `uid` mediumint(8)  NOT NULL DEFAULT '0',
                                   PRIMARY KEY (`session_key`,`uid`)
                    ) ;");
                    
              return true;         
    }
      *//**
     * read session value from database
     * @param string $key
     * @return string
     *//*
    public static function read($key) {
    	$key = self::$db->escapeString($key);   	
    	$strQuery = "SELECT session_value FROM ". self::$sess_table . 
					" WHERE session_key = '" . $key."' AND session_expiry > " . time()." LIMIT 1";
				
		return self::$db->querySingle($strQuery);	
    }
    
    *//**
     * write session data to database
     * @param string $key
     * @param string $value
     *//*
    public static function write($key, $value) { 
    	$session = Session :: getInstance();
    	$uid = (int)$session->getValue(Config::AUTH_ID);
    	$lifeTime=(int)time() + ini_get('session.gc_maxlifetime'); //gc 回收时间    	 
    	$key = self::$db->escapeString($key); 
    	$value=self::$db->escapeString($value);
    	if(self::$db->querySingle("SELECT session_key FROM ".self::$sess_table." WHERE  session_key='".$key ."'")){// uid=$uid and
    		$strQuery ="UPDATE ".self::$sess_table ." SET session_value='".$value."' , session_expiry=".$lifeTime;
    	}else{
    		$strQuery="INSERT INTO '".self::$sess_table ."' VALUES('".$key."','".$value."',".$lifeTime." ,".$uid.")";  		
    	}
    	//$strQuery="REPLACE INTO '".self::$sess_table ."' VALUES('".$key."','".$value."',".$lifeTime." ,".$uid.")";
    	$strQuery = 'INSERT INTO ' . self::$sess_table . 
    				' VALUES("'.$key.'" ,"'.$value.'",'.$lifeTime.' ,'.$uid.')'.
                    ' ON DUPLICATE KEY UPDATE session_value="'.$value.'", uid='.$uid.',' .
    				' session_expiry='.$lifeTime;
    			 
    	return self::$db->exec($strQuery);    		
    }	

    *//**
     * gc method for session save handle
     * @param $maxLifeTime
     *//*
    public static function gc($lifeTime) {
    	$strQuery = 'DELETE FROM ' . self::$sess_table . 
    				' WHERE session_expiry < ' .(time()-$lifeTime) ;				
    	return self::$db->exec($strQuery);
    }
         
    *//**
     * destory session value
     *//*
    public static function destroy($key) {
    	$key=self::$db->escapeString($key);
    	$strQuery = "DELETE FROM " . self::$sess_table ." WHERE session_key ='" .$key."'";		
    	return self::$db->exec($strQuery);
    }
     *//**
     * close session db
     *//*
    public static function close() {
    	self::$db = null;
    	return true;
    }*/
    
}
?>