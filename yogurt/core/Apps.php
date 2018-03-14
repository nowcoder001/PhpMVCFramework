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
 * Web Application 应用程序统一入口 ；
 * 作用：URI解析、认证、实例化Action类,执行对应的方法
 */

class Apps {
    private static $instance = null;
    private $actionSeparator = Config::ACTION_SEPARATOR;

    /**
     * get instance of Application
     * @return class An Application instance
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new Apps();
        }
        return self::$instance;
    }

    /**
     * init application param
     */
    public function init() {
    	// uri分解
    	$rewriteType = Config::REWRITE_NO;
    	$requestUrl = $_SERVER['REQUEST_URI'];
    	if(preg_match('/^\/[[:alnum:]\-_\.]+\/(.*)/', $requestUrl))  // url like '/news/add/type/1.html'
	        $rewriteType = Config::REWRITE_DIR;

    	$uriArray = (array)Url::getUriArray($requestUrl, $rewriteType); // 分解url, 定位到相应的Action

	    $defaultAction = ["admin", "login"];

        $moduleName = str_replace(Config::$WILDCARD, '/', empty($uriArray['moduleName']) ? $defaultAction[0] : $uriArray['moduleName']);

        $actionName = empty($uriArray['actionName']) ? $defaultAction[1] : $uriArray['actionName'];

    	R::setRqst('REWRITE_TYPE', $rewriteType);
    	R::setRqst('moduleName', $moduleName);
    	R::setRqst('actionName', $actionName);

    	foreach ($uriArray['params'] AS $key => $value)
    	R::setRqst($key, $value);

    	// 无须验证的函数
	    $methodArray = [
            "login",
            "loginAjax",
            "quit",
            "disposePlayers",
            "searchPlayers",
            "upload",
            "execl",
            "stepStart",
            "setError",
            "main",
            "receive",
            "getUnreceivedList",
            "setLang",
            "run",
            "analysis",
            'develLog',
            'recv',
            'uploadReplay',
            'downloadReplay',
            'replayList'
        ];
	    $moduleArray = [
            "sysadmin/UserGroup",
            "admin/admin",
            "admin",
            "api/Point",
            "api/Error",
            "api/Cdkey",
            "api/Recharge",
            "api/Analysis",
            "analysis/active/active"
        ];

	    if(in_array($actionName, $methodArray)) {

	    }
	    else {
	        // 检查是否过期
	        $session = Session::getInstance();
	        $SESS_OVERDUE_TIME = $session->getValue(Config::SESS_OVERDUE_TIME);
	        $CURRENT_TIME = time();

	        if(!$SESS_OVERDUE_TIME) {
		        //self::send_event();
                //var_dump("overduetime");
		        self::logout("session_timeout_escape");
	        }
	        if(($CURRENT_TIME - $SESS_OVERDUE_TIME) >= Config::SESS_LIFE_TIME) {
                //var_dump("overdue");
		        self::logout("session_timeout_escape");
	        }
	        // 没有过期则更新该过期时间
            // 	    	$session->setValue('SESS_OVERDUE_TIME', (time() + ini_get('session.gc_maxlifetime')));
	        $session->setValue(Config::SESS_OVERDUE_TIME, (time() + Config::SESS_LIFE_TIME));
	    }

	    // 指定某些无法分配权限的模块或者函数动作无须权限认证
	    //if(in_array($moduleName, $moduleArray) && in_array($actionName, $methodArray))
	    if(false) {
    	    // 权限认证
	        if(!call_user_func_array(array(new Auth, "check"), array($moduleName, $actionName))) {
                // 	        	exit("<script> top.location.href='/' </script> ");
	            Y::errors(403, 'sorry, can\'t execute module = ' . $moduleName . ', action = ' . $actionName);
	        }
	    }

        // 加载并实例化ActionClass  action 为程序执行的最小单元
        if(is_object($action = Y::loadClass($moduleName))) {
            return $action->execute($actionName);
        }
    }

    static function logout($msg = 'logout') {
	    if(R::rqst('ajax'))
	        exit(json::encode(['rows' => [], 'total' => 0, 'escape' => $msg]));
	    else
	        exit("<script> top.location.href='/' </script> ");
    }
}
?>
