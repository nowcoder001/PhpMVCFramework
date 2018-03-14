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
 * base是整 个架构的组织与管理者，作用：
 *
 * I 文件加载、类注册与路径解析
 *  base 加载类有两种方式：
 * ①   yourt 框架基类库
 * autoload[加载yogurt运行的必须类] and import[主要有：action、view、dao、orm、utils、cache]
 * plugins 插件方式 加载类库
 * import需求： 可以加载yogurt目录下的任何文件，支持如 core.action.Action 、 core/Action以及 core.action.* 三种方式 ,第三类库支持
 *
 * ②   加载应用模块的类库、工具类、功能函数以及第三方类库 -->封装
 *  通过简化的方法：Action[a]、Model[m]、Form[f]、Utils[u]、Function[fun]...进行加载；
 *
 * II 调试信息、日志、错误提示[如404、403、500]
 *
 */
abstract class YBase {
    private static   $version = '1.2.1'; // version of Framework
    private static   $_classes = [];
    private static   $_imports = [];
    private static   $classFileSuffix = Config::FILE_SUFFIX;
    private static   $path = null;
    //protected static $logger ; // logger object

    public static function getVersion() {
 	    return self::$version;
    }

    /**
     * @return string the path of the framework
     */
    public static function getFrameWorkPath() {
	    return Y_DIR;
    }

    //create appclicaton: web or game
    public static function createApps()	{
 	    return Apps::getInstance()->init();
    }

    /********************* 调试、日志、错误提示 *********************/
    public static function getLog() {
 	    if (!isset(self::$logger)) {
            self::$logger =  Logs::init();
        }
        return self::$logger;
    }

    /**
     * Writes a trace message.
     * This method will only log a message when the application is in debug mode.
     * @param string message to be logged
     * @param string category of the message
     * @see log
     */
    public static function trace() {
	    debug_print_backtrace();
    }

    /*
     * 错误提示页
     * php执行错误的类型：1 解析级 ； 2 应用
     * 错误码： 403 404 500
     */
    public static function errors($error_num = 404, $msg='') {
	    global $URL;
	    if(DEVELOP) {  //开发模式	 Development Pattern;
            //set_exception_handler('exception_handler'); set_error_handler("error_handler");
            throw new Exception($msg);
        }
        else { // 生产模式     Production Pattern;
            //写日志
            @file_put_contents(sprintf(LOG_DIR."error/%s.log",date('Ymd')),"[".date('Y-m-d H:i:s')."] [".$error_num."] Error:".$msg." URL: ".$URL."\r\n",FILE_APPEND);
            //跳转
            require_once(C_DIR."tips/error.php");
        }
        exit;
    }

    /********************* 加载类之应用程序 *********************/

    /**
     * load action class
     * @param String $moduleName
     * @param String $actionName
     * @return boolean
     */
    public static function loadClass($moduleName = null, $classType = 'Action', $showError = true) {
	    $className = arr::split('/', $moduleName);
	    $className = ucfirst(end($className)).$classType;

	    $path = M_DIR. self::moduleNameToPath($moduleName)."/".$className. self::$classFileSuffix;

    	if(self::loadFile($path, $showError)) {
    	    return new $className($moduleName);
    	}
    	return false;
    }

    static function moduleNameToPath($name) {
	    return strtolower(arr::join(DS, array_slice(arr::split('/', $name), 0, -1)));
    }

    /********************* 加载类之yogurt类库 *********************/
    /**
     * Factory模式取类
     */
    public static function factory($className, $classType, $_init = NULL) {
 	    $classType = ucfirst(strtolower($classType));
 	    $className= $classType.ucfirst($className);
	    if($classType == "Dao") {
	        $classFile = 'model.' . strtolower($classType) . '.' . $className;
 	    }
        else {
	        $classFile = strtolower($classType) . '.' . $className;
 	    }
 	    return (Y::import('core' . DS . $classFile)) ?  new $className($_init) : null;
    }

    /**
     * 默认载入框架类库
     * $class 支持fileName,dir.fileNamedir.*,dir/fileName,dir//fileName,dir\\fileName,dir\\fileName
     */
    public static function import($filename,$path='',$phpEx=Config::FILE_SUFFIX) {
 	    $filename  = str_replace(array('.','#'), array('/','.'), $filename);// 把.转换成/,#转成 . 当文件出现.时采用#否则一律用.
 	    $dirs=null;
 	    if(empty($path)) {// 单文件 加载 yogurt 类
 	        $filename = Y_DIR.$filename.$phpEx;
 	    }
        else {
 	        $dirs=$path;  //以(filename,package.lib)  的方式加载第三方类库
 	        $filename=$filename.$phpEx;
 	    }
 	    $incPath = false;
        if (!empty($dirs) && (is_array($dirs) || is_string($dirs))) {
            if (is_array($dirs)) {
                $dirs = implode(PS, $dirs);
            }
            $incPath = get_include_path();
            set_include_path($incPath. PS .$dirs ); //set_include_path(get_include_path() . PATH_SEPARATOR . $path.$dirs);
        }
 	    return self::loadFile($filename);// 加载单独文件
    }

    //载入文件库
    public static function loadFile($files, $showError = true) {
        Filter::filename($files);
        if (!is_readable($files)) {
            if($showError)
		        self::errors(404,"Sorry,File \"$files\" was can\'t loaded!");
	        else
		        return false;
        }
        return include_once($files);
    }

    /**
     * Class autoload loader.
     * This method is provided to be invoked within an __autoload() magic method.
     * @param string class name
     * @return boolean whether the class has been loaded successfully
     */
    public static function autoload($className) {
	    $include_dir = "includes";
        $files = array_filter(scandir(APP_DIR.$include_dir), function($file) {
            $class = explode('.', $file);
            return $class[1] == 'class';
        });

        $webapps_classes = array_combine(array_map(function($file) {
            $class = explode('.', $file);
            return $class[0];
        }, $files), $files);

        if(isset($webapps_classes[$className])) {
            return include(APP_DIR.DS.$include_dir.DS.$webapps_classes[$className]);
        }

	    if(isset(self::$_coreClasses[$className])) {
	        return include(Y_DIR.self::$_coreClasses[$className]);
        }
	    else
	        // 载入文件后判断指定的类或接口是否已经定义
	        return class_exists($className,false) || interface_exists($className,false);
    }

    //启动方法
    public static function registerAutoload($className = 'YBase', $enabled = true) {
	    if (!function_exists('spl_autoload_register')) {
            throw new Exception('spl_autoload does not exist in this PHP installation');
        }

        if ($enabled === true) {
            spl_autoload_register([$className, 'autoload']);
        }
        else {
            spl_autoload_unregister([$className, 'autoload']);
        }
    }

    // yogurt 库类加载
    private static $_coreClasses = [
        'Object'=>'core/Object.php',
        'Action' => 'core/Action.php',
        'debug' => 'functions/debug.php',
        'arr' => 'functions/arr.php',
        'csv' => 'functions/csv.php',
        'lisp' => 'functions/lisp.php',
        'linear' => 'functions/linear.php',
        'php' => 'functions/php.php',
        'html' => 'functions/html.php',
        'R' => 'functions/R.php',
        'js' => 'functions/js.php',
        'sexp' => 'functions/sexp.php',
        'sql' => 'functions/sql.php',
        'str' => 'functions/str.php',
        'time' => 'functions/time.php',
        'tri' => 'functions/tri.php',
        'xml' => 'functions/xml.php',
        'json' => 'functions/json.php',
        'SMTP' => 'util/PHPMailer/class.smtp.php',
        'PHPMailer' => 'util/PHPMailer/class.phpmailer.php',
        'Param' => 'core/Param.php',
	    'Apps' => 'core/Apps.php',
	    'Url'=> 'core/Url.php',
	    'Model'=>'core/model/Model.php',
	    'Dao'=>'core/model/dao/Dao.php',
	    'DaoException' => 'core/model/dao/DaoException.php',
	    'DaoPdo'=>'core/model/dao/DaoPdo.php',
	    'DaoMysql'=>'core/model/dao/DaoMysql.php',
	    'DaoMysqli'=>'core/model/dao/DaoMysqli.php',
	    'View'=> 'core/view/View.php',
        'ViewSmarty' => 'core/view/ViewSmarty.php',
        'Smarty' => 'core/view/smarty/libs/Smarty.class.php',
	    'Html'=> 'core/view/helper/Html.php',
	    'Mime' => 'core/view/Mime.php',
	    'Session'=>'core/session/Session.php',
	    'Category'=>'util/Category.php',
        'ClientUtils'=>'util/ClientUtils.php',
        'CookieUtils'=>'util/CookieUtils.php',
        'Cryption' => 'util/Cryption.php',
        'Files'=>'util/Files.php',
        'Filter' =>'util/Filter.php',
        'FtpUtils'=>'util/FtpUtils.php',
        'HttpUtils'=>'util/HttpUtils.php',
        'ImageUtils'=>'util/ImageUtils.php',
        'Json' => 'util/Json.php',
        'Logs'=> 'util/Logs.php',
        'Mailer'=>'util/Mailer.php',
        'MCUtils'=>'util/MCUtils.php',
        'MimeMail'=>'util/MimeMail.php',
        'MysqlUtils'=>'util/MysqlUtils.php',
        'XmlParser'=>'util/XmlParser.php',
        'RedisUtils'=>'util/RedisUtils.php',
        'MongoClientUtils'=>'util/MongoClientUtils.php',
        'MongoUtils'=>'util/MongoUtils.php',
        'Sqlite3Utils'=>'util/Sqlite3Utils.php',
        'ExcelUtils'=>'util/ExcelUtils.php'
    ];
}
?>
