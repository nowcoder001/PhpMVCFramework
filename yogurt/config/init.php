<?php
class init {
    static function initial() {
	    self::Consts();
	    self::includes();
        self::phpConf();
	    self::config();
	    self::loadFramework();
    }

    static function phpConf() {
	    error_reporting(E_ALL^E_NOTICE^E_DEPRECATED);

	    $settings = [
	        ['date.timezone', Config::TIMEZONE],
	        ['memory_limit', '1024M'],
	        ['display_errors', 'On'],
	        ['MAX_EXECUTION_TIME', 3600]
	    ];

	    array_map(function($cons) {
	        return ini_set($cons[0], $cons[1]);
	    }, $settings);
    }

    static function includes() {
	    include_once(Y_DIR.'config/config.php');
	    include_once(Y_DIR.'config/tablenames.php');
    }

    static function loadFramework() {
	    include_once(Y_DIR.'YBase.php');
	    include_once(Y_DIR.'Y.php');
	    include_once(Y_DIR.'includes/func.php');
    }

    static function Consts() {
        error_reporting(E_ALL^E_NOTICE^E_DEPRECATED);
        
	    define('DS', DIRECTORY_SEPARATOR);
	    define('PS', PATH_SEPARATOR);
	    //define('Y_DIR',dirname(dirname(__FILE__)).DS);
        define('Y_DIR', realpath(dirname(dirname(dirname(__FILE__)))).DS.'yogurt'.DS);

	    define('C_DIR', Y_DIR."config".DS);//配置文件目录
	    define('CORE_DIR',Y_DIR.'core'.DS);//Yogurt 核心类库目录

	    defined('ROOT_DIR') or define('ROOT_DIR', realpath(dirname(dirname(dirname(__FILE__))).DS));

	    define('APP_DIR', realpath(ROOT_DIR.DS.'webapps').DS);//应用程序目录 yogurt-framework/webapps
        define('ENTRY_DIR', realpath(APP_DIR.DS.'entry').DS);//应用程序目录 yogurt-framework/webapps
	    defined('M_DIR') or define('M_DIR', APP_DIR."modules".DS);//应用程序服务目录,灵活地定义服务的位置为UI提供服务包括：action[ajax]\webservice
	    define('UPLOAD_DIR',ROOT_DIR."logs/"); //上传文件目录
	    defined('AUTH') or define('AUTH',true); //开启验证

	    define('DEVELOP', true);

	    preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $HOST = $_SERVER['HTTP_HOST'], $regs);//   "/[^\.\/]+\.[^\.\/]+$/"
	    defined('DN') or define('DN', '.'.$regs['domain']);//应用程序主域 link: .yetogame.com
    }

    static function config() {
	    $gmhost = [
	        'host' => '172.16.1.4',
	        'username' => 'root',
	        'password' => '1234'
	    ];
	    $sdkhost = [
	        'host' => '172.16.1.4',
	        'username' => 'root',
	        'password' => '1234',
	        'dbname' => 'yeto_sdk'
	    ];

	    $GLOBALS['DATABASE'] = [
	        'main' => array_merge($gmhost, ['dbname' => 'yeto_dbcenter']),
	        'analysis' => array_merge($gmhost, ['dbname' => 'yeto_analysis']),
	        'slave' => $sdkhost
	    ];

	    /** redis 配置 **/
	    $GLOBALS['REDISD']['main'] = [['host' => 'localhost', 'port' => 6379, 'password'=>'0p3vRIUIY6F$XDF']];
    }
}
init::initial();
?>
