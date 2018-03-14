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
 * base action class.
 * yogurt framework.
 * action 的主要任务是实现业务的业务流程操作，功能包括：1 form 表单验证 2 跳转
 * 由于初始化的时候，已经确认了View对象，所以只能控制在文件里
 * PHP versions 5
 *
 * @filesource yogurt/core/Action.php
 * @since Yogurt v 0.9
 * @version $1.0
 */
abstract class Action extends Object {
    protected $actionName;
    protected static $errors = array();
    protected $mimeName = Config::VIEW_MIME_NAME;
    protected static $tplDir = TPL_DIR;
    protected static $varArray = array();

    protected $session = null;
    protected $userinfo = null;
    protected $privilege = null;

    /**
     * construts of Action
     *
     * @param
     *
     */

    public $log_flag = true;
    public function log() {
        try {
            return Model::getDao('main')->table('log')->insert([
                'user' => $this->userinfo['username'],
                'ip' => get_ip(),
                'timestamp' => time::microtime(),
                'action' => $_REQUEST['action'],
                'url' => $_SERVER['REQUEST_URI'],
                'request' => json_encode($_REQUEST)
            ]);
        }
        catch(Exception $e) {
            return false;
        }
    }

    public function __construct() {
	    parent::Object();
        $this->session = Session::getInstance();
        if(!$this->session->getValue('lang'))
            $this->session->setValue('lang', 'zh_CN');
        $this->userinfo = $this->session->getValue('userinfo');
	    $this->privilege = $this->userinfo[Config::AUTH_ID] == Config::AUTH_PRIVILEGE_ID ? true : false;
	    $this->param = new Param();
    }

    /**
     * execute function, called by Application
     */
    public function execute() {
        if($this->log_flag) $this->log();

        $args = func_get_args();
        $method = array_shift($args);
	    if(!method_exists($this, $method)) {
	        Y::errors(404, 'can\'t found the ' . ' ' . $this . ' class has ' . $method . '() method !');
	    }
	    $this->actionName = $method;
        return call_user_func_array([$this, $this->actionName], $args);
    }

    protected function assign($varName = '', $value = '') {
	    if(is_array($varName) && '' == $value) {
	        self::$varArray = array_merge(self::$varArray, $varName);
	    }
        else {
	        self::$varArray[$varName] = $value;
	    }
	    return self::$varArray;
    }

    protected function setMimeName($mimeName) {
	    $this->mimeName = $mimeName;
    }

    protected function setTplDir($tplDir) {
	    self::$tplDir = $tplDir;
    }

    public function setErrors($error) {
	    $this->errors[] = $error;
	    return $this;
    }

    public function getErrors() {
	    $this->errors;
	    return $this;
    }

    public function index() {
        $this->display();
    }

    public function showErrors($dataType, $error = null) {
	    if($error !== null)	{
	        $this->errors[] = $error;
	    }
	    if(count($this->errors) > 0) {
	        $response = json_encode(array('status' => 0,'msg' => $this->errors ));
	        $this->errors = array();
	        exit($dataType == 'jsonp' ? $_GET['callback'] . '(' . $response . ')' : $response);
	    }
	    return true;
    }

    /**
     * 封装view对象里的display()方法
     * tpl格式 : 1 null 2 index 3 index.php 4 default {: / # } index 注意：若最后出现 .
     * 则认为是后缀名
     * 默认采用 config 设置的后缀名
     */
    protected function display($tpl = null, $tplType = Config::VIEW_TEMPLATE_TYPE) {
	    $suffix = Config::VIEW_TPL_SUFFIX;
	    if(false !== ($rpos = strrpos($tpl, '.'))) {
	        $suffix = '.' . substr($tpl, $rpos + 1); // 文件后缀名
	        $tpl = substr($tpl, 0, $rpos);
	    }

	    $moduleName = R::rqst('moduleName');

	    if(is_null($tpl)) {
	        if(false !== ($ppos = strrpos($moduleName, '/'))) {
		        $packageName = substr($moduleName, 0, $ppos);
		        $tplPrefix = substr($moduleName, $ppos+1);
	        }
	        else {
		        $packageName = $moduleName;
		        $tplPrefix = $moduleName;
	        }

	        $tplPrefix = ucfirst($tplPrefix);
	        $tpl = $tplPrefix . '_' .ucfirst($this->actionName);
	    }
	    else {
	        if(false !== ($ppos = strrpos($moduleName, '/'))) {
		        $packageName = substr($moduleName, 0, $ppos);
	        }
	    }

	    // 转变成 即为目录名/文件名 default/index
	    $tpl = str_replace(Config::$WILDCARD, '/', $tpl);

	    if(false !== ($fpos = strrpos($tpl, '/'))) {
	        $tplName = substr($tpl, $fpos + 1); // 文件名
	        $dirs = substr($tpl, 0, $fpos);
	    }
        else {
	        $tplName = $tpl;
	        if(file_exists(self::$tplDir . Y::moduleNameToPath($moduleName). '/' . $tplName . $suffix))
		        $dirs = strtolower($packageName);
	    }

	    $tpl_dir = ENTRY_DIR."pages".DS.Y::moduleNameToPath($moduleName);

        $view = Y::factory($tplType, 'View');
	    $view->setMimeName($this->mimeName);
	    $view->setCacheTime($cacheTime);
	    $view->assign(self::$varArray);
        $view->setTplDir($tpl_dir);
	    $view->setTplName($tplName . $suffix);
	    return $view->display();
    }
}
?>
