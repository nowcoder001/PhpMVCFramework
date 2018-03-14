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
 * dao base class of yogurt framework.
 * @filesource		yogurt/core/model/dao/Dao.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 * 1 原生sql语句和连贯操作、[bind]操作支持
 * 2 事务、缓存支持
 * 3 调试：容错处理、日志[生产环境]、测速
 * 4 支持
 */
abstract class Dao extends Object {
    protected $daoType = Config::DB_DEFAULT_DAO_TYPE;

    protected $cache = null;   //cache class

    protected $isTransaction = false;
    protected $fetchMode;
    protected $bindArray = array();
    public $table = ''; //当前操作的表
    protected $charset = 'utf8';
    protected $sql = '';

    public $queryString =[];
    protected $options = []; //查询参数

    /**
     * construts of Dao class
     */

    abstract public function connect($schema=null);

    /**
     * execute sql
     * @param string $sql
     * @param string $tables
     * @return int
     */
    abstract protected function execute($sql = '',$showError=true);

    /**
     * get a result
     * @param string $sql
     * @param $fet
     */
    abstract protected function query($sql = '', $column="list");

    /**
     * get one column value
     * @param string $sql
     * @param string $tables
     * @return string
     */
    public function getOne($sql = '') {
	return $this->query($sql, 'one');
    }

    /**
     * get one row
     * @param string $sql
     * @param string $table
     * @param string $fetchMode
     * @return array
     */
    public function getRow($sql = '') {
	return $this->query($sql, 'row');
    }

    /**
     * get all list
     * @param string $sql
     * @param string $tables
     * @param int $fetchMode
     * @return array
     */
    public function getList($sql = '') {
	return $this->query($sql, 'list');
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
    public function findAll($field = '*', $table = '',$where = NULL, $prefix = 'list') {
        $sql = sql::tosql([
            'select' => $field?$field:$this->options['field'],
            'from' => $table?$table:$this->table,
            'where' => $where?$where:$this->options['where'],
            'join' => $this->options['join'],
            'group' => $this->options['group'],
            'having' => $this->options['having'],
            'order' => $this->options['order'],
            'limit' => $this->options['limit']
        ]);

        return $this->query($sql,$prefix);
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
    public function find($field = '*', $table = '',$where = NULL) {
        $prefix=(false!==strpos($field,',') or $field=='*')?'row':'one';
        return $this->findAll( $field, $table,$where, $prefix);
    }

    /**
     * insert execute
     * @param string $sql
     * @param string $tables
     * @return int
     */
    public function insert($data = '', $table = NULL, $showError = true) {
	if(is_string($data)) {
            $sql = $data;
        }
	elseif(is_array($data)) {
            $table = is_null($table) ? $this->table : $table;
            $sql   = "INSERT INTO `{$table}`";
            $fields = $values = array();
            $field = $value = '';
            //遍历记录, 格式化字段名称与值
            foreach($data as $key => $val) {
                $fields[] = "`{$table}`.`{$key}`";
                $values[] = sql::value($val);
            }
            $field = join(',', $fields);
            $value = join(',', $values);
            unset($fields, $values);
            $sql .= "({$field}) VALUES({$value})";
        }
        return  $this->execute($sql,$showError);
    }

    /**
     * update execute
     * @param string $sql
     * @param string $tables
     * @return int
     */
    public function update($data = '', $table = NULL, $where = NULL, $showError = true){
    	if(is_string($data)) {
            $sql =$data;
    	}
	elseif(is_array($data)) {
    	    $table  = is_null($table) ? $this->table : $table;
            $where  = is_null($where) ? $this->options['where'] : $where;
            $sql    = "UPDATE `{$table}` SET ";
            $values = array();
            foreach($data as $key => $val) {
                $values[] = "`{$table}`.`{$key}` = ". sql::value($val);
            }
            $value = join(',', $values);
            $sql = $sql . $value . " WHERE {$where}";
    	}

    	return $this->execute($sql,$showError);
    }

    /**
     * delete execute
     * @param string $sql
     * @param string $tables
     * @return int
     */
    public function delete($where = NULL, $table = NULL) {
	$table = is_null($table) ? $this->table : $table;
        $where = is_null($where) ? $this->options['where'] : $where;
        $sql   = "DELETE FROM `{$table}` WHERE {$where}";
        return $this->execute($sql);
    }

    /*
     * 事务操作
     */
    public function transaction($sql = array()){
	if(!is_array($sql) or empty($sql)){exit('sql must be array');}
        $this->begin();
        $j=1;
        foreach($sql as $v){
            if(!$this->execute($v,true)){ $j=0;}
        }
        if($j==1){
            $this->commit();
            return true;
        }else{
            $this->rollback();
	    Y::errors(500,'execute() method, can\'t execute error sql, sql = ' . print_r($sql));
        }
    }

    /**
     * get sql
     * @return sql
     */
    public function getSql() {
	return $this->sql;
    }

    /**
     * set sql
     * @param string $sql
     */
    public function setSql($sql) {
	$this->sql = $sql;
    }

    /**
     * set fetch mode
     * @param string $fetchMode
     */
    public function setFetchMode($fetchMode) {
	$this->fetchMode = $fetchMode;
    }

    /**
     * get fetch mode
     * @param int
     */
    public function getFetchMode() {
	return $this->fetchMode;
    }

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
}
?>
