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

class DaoMysqlGame extends Dao {
    //数据库连接标识
    protected $mysql   = null;
    //数据返回类型, 1代表数组, 2代表对象
    protected $returnType   = 1;

    /**
     * construct of DaoMysqli
     */
    public function connect($schema='main') {
	if (null != $this->conn) {
	    return $this->conn;
	}

        try {
	    $database= $GLOBALS['DATABASE'][$schema];
	    $usr =  $database["username"];
	    $pwd =  $database["password"];
	    if(!isset($database['port'])){
		$database['port'] = '3306';
	    }
	    $dsn = $database['host'].':'.$database['port'];

	    $this->conn = mysql_connect($dsn, $usr, $pwd, true) or die('connect db error');

	    mysql_select_db($database['dbname'], $this->conn) or die('select db error');
	    $charset = isset($database["charset"])?$database["charset"]:$this->charset;
	    mysql_query("set names " . $charset, $this->conn);
	    return $this->conn;
        }
        catch (Exception $e) {
            return ['code' => $e->getCode(), 'msg' => $e->getMessage()];
        }
    }

    /**
     * 连接对应的游戏服数据库
     */
    public function connectGame($gid, $sdkid, $serverid) {
    	if (null != $this->mysql)
    	    return $this->mysql;

    	$dao = Model::getDao("slave");

        $database = $dao->table("game_servers")->where(sql::cond_join([
            sql::bicond('gid', $gid),
            sql::bicond('sdkid', $sdkid),
            sql::bicond('server_id', $serverid)
        ]))->find();

    	if (!$database)
            throw new Exception('can not find game server record');

        if(!isset($database['db_port']))
            $database['db_port'] = '3306';

        $this->mysql = new mysqli($database['db_host'] , $database['db_username'], $database['db_password'], $database['db_name'], $database['db_port']);
        $this->mysql->options(MYSQLI_OPT_CONNECT_TIMEOUT,5);

        if($this->mysql->connect_errno) {
            throw new Exception('Game Server DB Connect Error: '. json::encode(arr::columns($database, ['server_id', 'sdkid', 'name', 'db_host', 'db_username'])));
        }
        /* if (mysqli_connect_error())
         * {
         *     //         	printf("Connect failed: %s\n", mysqli_connect_error());
         *     //         	exit();
	   die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
         * }*/

        $charset = isset($database["charset"]) ? $database["charset"] : $this->charset;
        $this->mysql->set_charset($charset) ;

    	return $this->mysql;
    }

    /**
     * begin transaction
     */
    public function begin() {
    	$this->mysql->autocommit(FALSE);
    }

    /**
     * commit transaction
     */
    public function commit() {
    	$this->mysql->commit();
    }

    /**
     * rollback transaction
     */
    public function rollback() {
    	$this->mysql->rollback();//有任何错误发生，回滚并取消执行结果
    }

    /**
     * 执行SQL命令
     *
     * @access      public
     * @param       string    $sql    SQL命令
     * @param       resource  $link   数据库连接标识
     * @return      bool              是否执行成功
     */
    public function execute($sql = '',$showError=true) {
        $stime = time::microtime();
        $sql = empty($sql) ? $this->sql : $sql;
        //transaction or not
        if($this->mysql->query($sql)) {
            $etime = time::microtime();
            $time = $etime - $stime;
            $this->queryString[] = $sql . ' ' . number_format($time,3);
            $lastid =  $this->mysql->insert_id;
            $affrows=$this->mysql->affected_rows;
            if($lastid > 0) return $lastid ;
            return $affrows > 0?$affrows:true;
        }
        if($showError) {
	    Y::errors(500,$this->mysql->error." in SQL: $sql");
	}
	return false;
    }

    /**
     * query method return a result
     * @param string $sql
     * @param int $fetchMode
     * @return array
     */
    protected function query($sql = '', $prefix="list") {
        $stime = time::microtime();
        $sql = empty($sql) ? $this->sql : $sql;

        if(empty($value)){
            $result = $this->mysql->query($sql);
            if($result === false){
                Y::errors(500,$this->mysql->error." in SQL: $sql");
            }
            switch($prefix){
                case 'one':
                    $value=implode((array) $result->fetch_row());
                    break;
                case 'row':
                    $value=count($row= (array) $this->fetch($result))>1?$row:implode($row);
                    break;
                case 'list':
                    $value=$this->fetchAll($result);
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
