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
 * mysqliDao class of yogurt framework.
 * 封装最基本的mysql操作函数
 * @filesource		yogurt/core/model/dao/DaoMysqli.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

class DaoMysqli extends Dao {
    //数据库连接标识
    protected $mysqli = null;
    //数据返回类型, 1代表数组, 2代表对象
    protected $returnType = 1;

    protected $stmt;

    protected $result;
    
    /**
     * construct of DaoMysqli
     */
    public function __construct() {
	parent::Dao();
    }

    /**
     * getMysql 产生的是连接数据库的句柄，而非对象
     */
    public function connect($schema = 'main') {
	if(null != $this->mysqli)
	    return $this->mysqli;

        $database = $GLOBALS['DATABASE'][$schema];
    	$usr = $database["username"];
	$pwd = $database["password"];
        if(!isset($database['port']))
            $database['port'] = '3306';

        $dsn = $database['host'];
        $this->mysqli = new mysqli($dsn, $usr, $pwd, $database['dbname'], $database['port']);

        if(mysqli_connect_error())
	    throw new DaoException($this->mysqli->error, $this->mysqli->errno);

	$charset = isset($database["charset"]) ? $database["charset"] : $this->charset;
        $this->mysqli->set_charset( $charset) ;

    	return $this->mysqli;
    }

    /**
     * begin transaction
     */
    public function begin() {
    	$this->mysqli->autocommit(FALSE);
    }

    /**
     * commit transaction
     */
    public function commit() {
    	$this->mysqli->commit();
    }

    /**
     * rollback transaction
     */
    public function rollback() {
    	$this->mysqli->rollback();//有任何错误发生，回滚并取消执行结果
    }

    /**
     * 执行SQL命令
     *
     * @access      public
     * @param       string    $sql    SQL命令
     * @param       resource  $link   数据库连接标识
     * @return      bool              是否执行成功
     */
    public function execute($sql = '',$showError = true) {
	$stime = time::microtime();

        $sql = empty($sql) ? $this->sql : $sql;
        //transaction or not
        //exit($sql);
        if($this->mysqli->query($sql)){
            $etime = time::microtime();
            $time = $etime - $stime;
            $this->queryString[] = $sql . ' ' . number_format($time,3);
            $lastid =  $this->mysqli->insert_id;
            $affrows = $this->mysqli->affected_rows;
            if($lastid > 0) return $lastid ;
            return $affrows > 0?$affrows:false;
        }
        if($showError) {
            throw new DaoException($this->mysqli->error, $this->mysqli->errno);
	    //Y::errors(500,$this->mysqli->error." in SQL: $sql");
	}
	return false;

    }

    /**
     * query method return a result
     * @param string $sql
     * @param int $fetchMode
     * @return array
     */
    protected function query($sql = '', $prefix = "list"){
        $stime = time::microtime();
        $sql = empty($sql) ? $this->sql : $sql;

        if(empty($value)) {
            $result = $this->mysqli->query($sql);
            if($result === false){
                throw new DaoException($this->mysqli->error, $this->mysqli->errno);
                //Y::errors(500, $this->mysqli->error." in SQL: $sql");
            }
            switch($prefix){
                case 'one':
                    $value = implode((array) $result->fetch_row());
                    break;
                case 'row':
                    $value = count($row = (array) $this->fetch($result))>1?$row:implode($row);
                    break;
                case 'list':
                    $value = $this->fetchAll($result);
                    break;
            }

            $result->close();

	    $etime = time::microtime();
            $time = $etime - $stime;
            $this->queryString[] = $sql . ' ' . number_format($time,3);
        }
        $this->options=array();
        return $value;
    }

    /**
     * 读取结果集中的所有记录到数组中
     *
     * @access public
     * @param  resource  $result  结果集
     * @return array
     */
    public function fetchAll($result = NULL) {
        $rows = array();
        while($row = $this->fetch($result)) {
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
    public function fetch($result = NULL, $type = NULL) {
        $result = is_null($result) ? $this->result : $result;
        $type   = is_null($type)   ? $this->returnType : $type;
        $func   = $type === 1 ? 'fetch_assoc' : 'fetch_object';
        return  $result->{$func}() ;
    }
}
?>
