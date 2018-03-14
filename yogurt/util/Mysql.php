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
 * mysql 需求：
 * 1 写：支持多数据自动添加、修改、删除以及原生sql操作，事务支持
 * 2 读： 原生sql操作和连贯操作，cache 支持
 * 3 调试：容错处理、日志[生产环境]、测速
 */

class Mysql{
   //数据库连接标识
    protected $conn         = null;
            //当前操作的表
    public    $table        = '';
           //数据库查询次数
    protected $queryCount   = 0;   
    public $queryString = array();
        //当前执行的SQL语句
    protected $sql          = '';    
        //查询参数
    protected $options      = array();          
        //缓存次数
    protected $cacheCount   = 0;
       //缓存路径
    protected $cachePath    = '';
        //数据返回类型, 1代表数组, 2代表对象
    protected $returnType   = 1;
    
    public function __construct($c){
        if(!isset($c['port'])){
            $c['port'] = '3306';
        }
        $server = $c['host'] . ':' . $c['port'];
		
        $this->conn = mysql_connect($server, $c['username'], $c['password'], true) or die('connect db error');
        mysql_select_db($c['dbname'], $this->conn) or die('select db error');
        if($c['charset']){
            mysql_query("set names " . $c['charset'], $this->conn);
        }
         $this->cachePath = isset($db['cachepath']) ? $db['cachepath'] : './';
    }

    public function getOne($sql=''){
    	 return$this->query($sql,'one');
    	  	   	
    }
    
    public function getRow($sql=''){
    	 return $this->query($sql,'row');
    	   	 	
    } 
        /**
         * 
         *
         * @access public
         * @param  resource  $result  结果集
         * @param  int       $type    返回类型, 1为数组, 2为对象
         * @return mixed              根据返回类型返回
         */
    public function getList($sql='', $pageNum = 1, $pageCount = 0){   	
		$pageSql="";
		if(1!= $pageNum&&0!= $pageCount){$pageSql = ' LIMIT ' . ($pageNum - 1) * $pageCount . ', ' . $pageCount;}		
		$sql = $sql.$pageSql;   	
        return $this->query($sql,'list');    
    }

       /**
         * 读取结果集中的所有记录到数组中
         *
         * @access public
         * @param  resource  $result  结果集
         * @return array
         */
     public function fetchAll($result = NULL)
        {
                $rows = array();
                while($row = $this->fetch($result))
                {
                        $rows[] = $row;
                }
                return $rows;
        }

        /**
         * 读取结果集中的一行记录到数组中
         *
         * @access public
         * @param  resource  $result  结果集
         * @param  int       $type    返回类型, 1为数组, 2为对象
         * @return mixed              根据返回类型返回
         */
      public function fetch($result = NULL, $type = NULL)
        {
                $result = is_null($result) ? $this->result : $result;
                $type   = is_null($type)   ? $this->returnType : $type;
                $func   = $type === 1 ? 'mysql_fetch_assoc' : 'mysql_fetch_object';
                return $func($result);
        }
        
         /**
         * 查询符合条件的一项记录
         *
         * @access      public
         * @param       string    $where  查询条件
         * @param       string    $field  查询字段
         * @param       string    $table  表
         * @return      mixed             符合条件的记录
         */
      public function findFirst($where = NULL, $field = '*', $table = '')
        {
             return $this->findAll($where = NULL, $field = '*', $table = '', 'one');
        }       
        
         /**
         * 查询符合条件的一条记录
         *
         * @access      public
         * @param       string    $where  查询条件
         * @param       string    $field  查询字段
         * @param       string    $table  表
         * @return      mixed             符合条件的记录
         */
       public function find($where = NULL, $field = '*', $table = '')
        {
             return $this->findAll($where = NULL, $field = '*', $table = '', 'row');
        }
        
    
        /**
         * 查询符合条件的所有记录
         *
         * @access      public
         * @param       string    $where  查询条件
         * @param       string    $field  查询字段
         * @param       string    $table  表
         * @return      mixed             符合条件的记录
         */
        public function findAll($where = NULL, $field = '*', $table = '', $prefix = 'list')
        {
                $this->options['where'] = is_null($where) ? $this->options['where'] : $where;
                $this->options['field'] = isset($this->options['field']) ? $this->options['field'] : $field;
                $this->options['table'] = $table == '' ?  $this->table : $table;
                $sql   = "SELECT {$this->options['field']} FROM {$this->options['table']} ";
                $sql  .= isset($this->options['join']) ? ' LEFT JOIN ' . $this->options['join'] : '';
                $sql  .= isset($this->options['where']) ? ' WHERE ' . $this->options['where'] : '';
                $sql  .= isset($this->options['group']) ? ' GROUP BY ' . $this->options['group'] : '';
                $sql  .= isset($this->options['having']) ? ' HAVING ' . $this->options['having'] : '';
                $sql  .= isset($this->options['order']) ? ' ORDER BY ' . $this->options['order'] : '';
                $sql  .= isset($this->options['limit']) ? ' LIMIT ' . $this->options['limit'] : '';                         
                return $this->query($sql,$prefix); 
        }

     /**
     * 执行 mysql_query 并返回其结果.
     */
    public function query($sql = '', $column="list",$link = NULL){
        $stime = microtime(true);
        $this->queryCount ++;
        $sql = empty($sql) ? $this->sql : $sql;
        $link = is_null($link) ? $this->conn : $link;
        $result = mysql_query($sql, $link);      
        if($result === false){
            throw new Exception(mysql_error($link)." in SQL: $sql");            	
        }
        
        $etime = microtime(true);
        $time = number_format(($etime - $stime) * 1000, 2);
        $this->queryString[] = $time . ' ' . $sql;
        $value    = NULL;

                //如果开启了缓存, 那么重缓存中获取数据
                if($this->options['cache'] === TRUE)
                {
                        $value = $this->readCache();
                }

                //如果读取失败, 或则没有开启缓存
                if(is_null($value))
                {                     
                        switch($column){
                        	case 'one': 
                        	$value=mysql_fetch_row($result); 
                        	break;
                        	case 'row':
                        	$value=$this->fetch($result);
                        	break;
                        	case 'list':
                        	$value=$this->fetchAll($result);
                        	break;
                        }                       
                        if($this->options['cache'] === TRUE)
                                //如果开启了缓存, 那么就写入
                            $this->writeCache($value);
                            $this->options = array();
                }
          return $value;
    }

        /**
         * 执行SQL命令
         *
         * @access      public
         * @param       string    $sql    SQL命令
         * @param       resource  $link   数据库连接标识
         * @return      bool              是否执行成功
         */
     public function execute($sql = '',$exe='insert',$link = NULL)
        {       	      	
                $sql = empty($sql) ? $this->sql : $sql;
                $link = is_null($link) ? $this->conn : $link;
                $this->queryString[] =$sql;
                if(mysql_query($sql, $link))
                {
                        if('insert'==$exe){
                        	
                        	return mysql_insert_id($link);                      	
                        }else{
                            return mysql_affected_rows($link);                       	
                        }
                }
                  throw new Exception(mysql_error($link)." in SQL: $sql");               
        }

        /**
         * 插入记录
         *
         * @access public
         * @param  string or array  $sql  插入的记录, 格式:array('字段名'=>'值', '字段名'=>'值');
         * @param  string $table 表名
         * @return bool          当前记录id
         */
      public function insert($data='', $table = NULL)
        {
        	if(is_string($data)){       		  	
        	    $sql=$data;
        	}elseif(is_array($data)){
        		$table = is_null($table) ? $this->table : $table;
                $sql   = "INSERT INTO `{$table}`";
                $fields = $values = array();
                $field = $value = '';               
                //遍历记录, 格式化字段名称与值
                foreach($data as $key => $val)
                {
                        $fields[] = "`{$table}`.`{$key}`";
                        $val=mysql_real_escape_string($val);//过虑数据
                        $values[] = is_numeric($val) ? $val : "'{$val}'";
                }
                $field = join(',', $fields);
                $value = join(',', $values);
                unset($fields, $values);
                $sql .= "({$field}) VALUES({$value})";  		
        	}    
        	    return  $this->execute($sql);	           
        }

    /**
     * 更新$arr[id]所指定的记录.
     * @param array $row 要更新的记录, 键名为id的数组项的值指示了所要更新的记录.
     * @return int 影响的行数.
     * @param string $field 字段名, 默认为'id'.
     */
    function update($data='', $where = NULL, $table = NULL){
    	if(is_string($data)){
                $sql =$data;
    	}elseif(is_array($data)){
    	        $table  = is_null($table) ? $this->table : $table;
                $where  = is_null($where) ? $this->options['where'] : $where;
                $sql    = "UPDATE `{$table}` SET ";
                $values = array();
                foreach($data as $key => $val)
                {
                        $val      = is_numeric($val) ? $val : "'{$val}'";
                        $values[] = "`{$table}`.`{$key}` = {$val}";
                }
                $value = join(',', $values);
                $sql = $sql . $value . " WHERE {$where}";     
    	}
    	return $this->execute($sql,"update");
    }

         /**
         * 删除记录
         *
         * @access public
         * @param  string  $where  条件
         * @param  string  $table  表名
         * @return bool            影响行数
         */
     public function delete($where = NULL, $table = NULL)
        {
                $table = is_null($table) ? $this->table : $table;
                $where = is_null($where) ? $this->options['where'] : $where;
                $sql   = "DELETE FROM `{$table}` WHERE {$where}";               
                return $this->execute($sql,"delete");
        }
        
          /**
         * 缓存当前查询
         *
         * @access      public
         * @param       string    $name   缓存名称
         * @param       int       $time   缓存有效时间, 默认为60秒
         * @param       string    $path   缓存文件存放路径
         * @return      object            数据库操作对象
         */
        public function cache($name = '', $time = 60, $path = '')
        {
                $this->options['cache']         = TRUE;
                $this->options['cacheTime']     = $time;
                $this->options['cacheName']     = empty($name) ? md5($this->sql) : $name;
                $this->options['cachePath']     = empty($path) ? $this->cachePath : $path;
                return $this;
        }

        /**
         * 读取缓存
         *
         * @access      public
         * @return      mixed   如果读取成功返回缓存内容, 否则返回NULL
         */
        protected function readCache()
        {
                $file = $this->options['cachePath'] . $this->options['cacheName'] . '.php';
                if(file_exists($file))
                {
                        //缓存过期
                        if((filemtime($file) + $this->options['cacheTime']) < time())
                        {
                                @unlink($file);
                                return NULL;
                        }

                        if(1 === $this->returnType)
                        {
                                $row = include $file;
                        }
                        else
                        {
                                $data = file_get_contents($file);
                                $row  = unserialize($data);
                        }
                        return $row;
                }
                return NULL;
        }

        /**
         * 写入缓存
         *
         * @access      public
         * @param       mixed   $data   缓存内容
         * @return      bool            是否写入成功
         */
        public function writeCache($data)
        {
                $this->cacheCount++;
                $file = $this->options['cachePath'] . $this->options['cacheName'] . '.php';
                if(1 === $this->returnType)
                        $data = '<?php return ' . var_export($data, TRUE) . '; ?>';
                else
                        $data = serialize($data);
                return file_put_contents($file, $data);
        }
       
 
        //获得当前查询所用到的SQL语句    
    public function getQueryString(){
        return $this->queryString;	       	
        } 

/*    function escape(&$val){
        if(is_object($val) || is_array($val)){
            $this->escape_row($val);
        }
    }
    
    function escape_row(&$row){
        if(is_object($row)){
            foreach($row as $k=>$v){
                $row->$k = mysql_real_escape_string($v);
            }
        }else if(is_array($row)){
            foreach($row as $k=>$v){
                $row[$k] = mysql_real_escape_string($v);
            }
        }
    }

    function escape_like_string($str){
        $find = array('%', '_');
        $replace = array('\%', '\_');
        $str = str_replace($find, $replace, $str);
        return $str;
    }*/
    
      //自动加载函数, 实现特殊操作
    public function __call($func, $args)
        {
                if(in_array($func, array('field', 'join', 'where', 'order', 'group', 'limit', 'having')))
                {
                        $this->options[$func] = array_shift($args);
                        return $this;
                }
                elseif($func === 'table')
                {
                        $this->options['table'] = array_shift($args);
                        $this->table            = $this->options['table'];
                        return $this;
                }
                //如果函数不存在, 则抛出异常
                exit('Call to undefined method Db::' . $func . '()');
        }

    /**
     * 开始一个事务.
     */
    public function begin(){
        mysql_query('begin');
    }

    /**
     * 提交一个事务.
     */
    public function commit(){
        mysql_query('commit');
    }

    /**
     * 回滚一个事务.
     */
    public function rollback(){
        mysql_query('rollback');
    }
 
     //清空结果集
   public function free($result = null)
        {
                $result = is_null($result) ? $this->result : $result;
                return mysql_free_result($result);
        }
    
        //返回错误信息
        public function getError($link = NULL)
        {
                $link = is_null($link) ? $this->link : $link;
                return mysql_error($link);
        }

        //返回错误编号
        public function getErrno($link = NULL)
        {
                $link = is_null($link) ? $this->link : $link;
                return mysql_errno($link);
        }
        
    function close() {
               @mysql_close($this->conn);
      }
}
