<?php
// ***************************************************************
//  Copyright(c) Yeto
//  FileName	: Param.php
//  Creator 	: John
//  Date		: 2016-03-11
//  Comment		:
// ***************************************************************
class Param {
    private $regex = '/^\d+$/';

    /**
     * SQL 参数注入预防
     */
    public function inject_replace ($param = '', $max_length = 20) {
	    // 		echo '接收参数: '. $param . '</br>';

	    // 去除空格
	    $param = trim($param);
	    // 把字符串中的换行符\n替换HTML换行符
	    //$param = nl2br($param);
	    // 引用字符串用斜杠
	    // 		$param = addslashes(sprintf("%s", $param));
	    // 限制字符串长度
	    $param = substr($param, 0, $max_length);

	    // 非法关键字
	    $Illegal_keywords = array("select", "insert", "update", "delete", "and", "not", "union", "into", "load_file", "outfile", "sleep", "--");
	    foreach ($Illegal_keywords as $keywords) {
	        if(strripos($param, $keywords, 0) >= 0) {
		        // str_ireplace() 函数对大小写不敏感
		        $param = str_ireplace($keywords, '', $param);
	        }
	    }

	    // 		echo '返回参数: '. $param . '</br>';
	    return $param;
    }


    /**
     * 从  request 中获取 int 数据
     */
    public function int_request ($param_name = "") {
	    if($param_name == null || $param_name == "")
	        return -1;

	    $param = 0;
	    if(isset($_REQUEST[$param_name]))
	        $param = $_REQUEST[$param_name];
	    else
	        return -1;

	    $param = intval($param);
	    if(!preg_match($this->regex, $param))
	        return -1;

	    return $param;
    }

    /**
     * 从  post 中获取 int 数据
     */
    public function int_post ($param_name = "") {
	    if($param_name == null || $param_name == "")
	        return -1;

	    $param = 0;
	    if(isset($_POST[$param_name]))
	        $param = $_POST[$param_name];
	    else
	        return -1;

	    //$param = intval($param);
	    if(!preg_match($this->regex, $param))
	        return -1;

	    return $param;
    }

    /**
     * 从  post 中获取 int 类型的 rows 数据
     */
    public function rows_int_post ($param_name = "rows") {
	    if($param_name == null || $param_name == "")
	        return 50;

	    $rows = 0;
	    if(isset($_POST[$param_name]))
	        $rows = $_POST[$param_name];

	    if(!preg_match($this->regex, $rows))
	        return 50;

	    if($rows < 1 || $rows > 500)
	        $rows = 50;

	    return $rows;
    }

    /**
     * 从  post 中获取 int 类型的 page 数据
     */
    public function page_int_post ($param_name = "page") {
	    if($param_name == null || $param_name == "")
	        return 1;

	    $page = 0;
	    if(isset($_POST[$param_name]))
	        $page = $_POST[$param_name];

	    if(!preg_match($this->regex, $page))
	        return 1;

	    if($page <= 0 || $page > 1000)
	        $page = 1;

	    return $page;
    }

    /**
     * 从  post 中获取 sort 排序字段
     */
    public function sort_post ($param_name = "sort") {
	    if($param_name == null || $param_name == "")
	        return 1;

	    $sort = "";
	    if(isset($_POST[$param_name]))
	        $sort = $_POST[$param_name];

	    if($sort == "")
	        return NULL;
	    else
	        return $this->inject_replace($sort);
    }

    /**
     * 从  post 中获取 order 排序标示
     */
    public function order_post ($param_name = "order") {
	    if($param_name == null || $param_name == "")
	        return 'desc';

	    $order = "";
	    if(isset($_POST[$param_name]))
	        $order = $_POST[$param_name];

	    if($order != 'desc' && $order != 'asc' && $order != 'DESC' && $order != 'ASC')
	        $order = 'DESC';

	    return trim($order);
    }

    /**
     * 获取从库
     */
    public function slaveDao () {
	    return Model::getDao("slave");
    }

    /**
     * 基于 HTTP-REQUEST 获取 sdkid, 然后判断SDK是否有权限
     */
    public function permission_sdk ($param_name = "sdkid") {
	    $sdkid = $this->int_request($param_name);
	    if($sdkid <= 0)
	        return false;

	    return $this->check_permission_sdk($sdkid);
    }

    /**
     * 根据SESSION缓存数据判断SDK是否有权限
     * 注意: 根据SESSION缓存数据判断不是非常严禁的做法, 最好是根据全局缓存或者 DB 数据
     */
    public function check_permission_sdk ($sdkid =  0) {
	    if($sdkid == null || $sdkid == "" || $sdkid == 0)
	        return false;

	    $session = Session::getInstance();

	    $userinfo = $session->getValue('userinfo');
	    $privilege = $userinfo[Config::AUTH_ID] == Config::AUTH_PRIVILEGE_ID ? true : false;
	    if($privilege)
	        return true;

	    //$sdkList = $session->getValue(Config::SDK_LIST);
        $sdk_list = explode(',', Model::getDao()->table('users_group')->where("group_id = '{$userinfo["groupid"]}'")->find("sdk_list"));
        foreach($sdk_list as $sdk) {
            $sdkList[] = ['sdkid' => $sdk];
        }

	    if(!is_array($sdkList))
	        return false;

	    // 		var_dump($sdkList);

	    if(count($sdkList) > 0) {
	        foreach($sdkList as $sdk) {
		        if($sdk['sdkid'] == $sdkid)
		            return true;
	        }
	    }

	    return false;
    }

    /**
     * 检查指定的DB表是否存在
     */
    public function check_exist_db_table ($gid, $sdkid, $serverId, $db_table) {
	    $gameDao = Model::getGameDao($gid, $sdkid, $serverId);
	    if(!$gameDao)
	        return false;

	    $all_table = $gameDao->getlist("SHOW TABLES;");
	    if(count($all_table) <= 0)
	        return false;

	    foreach ($all_table as $table) {
	        if(in_array($db_table, $table))
		        return true;
	    }

	    return false;
    }
}
