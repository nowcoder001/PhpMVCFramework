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
 * config class of yogurt framework.
 * yogurt运行的环境[框架]配置：1 常量的定义；2 配置文件定义；3 设置 php.ini
 * PHP versions 5
 *
 * Yogurt : MVC Development Framework with PHP<http://www.yogurt-framework.com/>
 * Copyright (c)2009-2010, rick <158672319@qq.com>
 * Licensed under The GNU License
 *
 * @filesource		yogurt/config/Config.php
 * @since			Yogurt v 1.1
 * @version			$1.0
 */
final class Config {
    ///////////////////////////////////////////////////
    // Application global seting
    //////////////////////////////////////////////////
    /** action separtor */
    const ACTION_SEPARATOR        = '!';//URL中模块名与动作名的分隔符
    /** action accessor */
    const DEFAULT_ACTION_ACCESSOR = 'action';
    /** application charset */
    const APP_CHARACTER   = "UTF-8";
    /** file suffix */
    const FILE_SUFFIX             = ".php";
    /** forward redirect wait time */
    const WAIT_TIME		  = 3;
    /** http request time*/
    const HTTP_REQUEST_TIME  = 30 ;
    /** home index **/
    const HOME_INDEX       ="admin!index" ;
    /** wildcard **/
    public static $WILDCARD = [":","#","."];

    ////////////////////////////////////////////////
    // Model seting
    ////////////////////////////////////////////////
    const DB_DEFAULT_DAO_TYPE = 'mysqli';

    const DB_DEFAULT_GAME_DAO_TYPE = 'mysqlGame';

    const DB_MYSQLI_ = 'Mysqli';

    /////////////////////////////////////////////////////
    // View seting
    ////////////////////////////////////////////////////
    /** template engine 'php' or 'smarty' or 'phplib' or 'smartTemplate' **/
    const VIEW_TEMPLATE_TYPE = 'php';
    /** file suffix */
    const VIEW_TPL_SUFFIX = '.html';

    /** MIME_NAME: 'html' 'xml' 'wml' 'pdf' 'excel' 'text' ... */
    //const VIEW_MIME_NAME = 'html';
    const VIEW_MIME_NAME = 'tpl';

    ///////////////////////////////////////////////////
    // Auth seting
    ///////////////////////////////////////////////////
    /** user login id */
    const AUTH_ID = 'username';
    /** privilege id, without authen */
    const AUTH_PRIVILEGE_ID = 'admin';
    /** privilege role  */
    //const AUTH_PRIVILEGE = 'PRIVILEGE';
    /** role from xml */
    const AUTH_ROLE_NAME_XML = 'member';//从xml读取的权限标记
    /** save auth type: mysql,mongodb,sqlserver.... */
    const AUTH_SAVE_TYPE = 'mysql';

    const SDK_LIST = "sdklist";

    ///////////////////////////////////////////////////
    //  Session seting
    ///////////////////////////////////////////////////
    /** type: mysql、redis、memcache、apc、file **/
    const SESS_SAVE_TYPE = '';  // ''空为php默认处理
    const SESS_NAME = 'YOGURT_SESSION';
    //  	const SESS_LIFE_TIME = 43200;//12*3600
    const SESS_LIFE_TIME = 1800; // 60*30分钟
    const SESS_OVERDUE_TIME = "SESS_OVERDUE_TIME"; // Session 过期时间点
    const SESS_GARBAGE = 10; //GC 机率 50/100 = 50%
    const SESS_ALLOW_AGENT = '';

    ////////////////////////////////////////////////
    // Rewrite type
    ////////////////////////////////////////////////
    const REWRITE_NO = 0;  //do not use rewrite
    const REWRITE_DIR = 1; // url like '/news/c/id/1.html'

    ////////////////////////////////////////////////
    // Excel
    ////////////////////////////////////////////////
    /** 导出最大数量限制  **/
    const EXECL_MAX_LIMIT = 65535;

    const DATE_FORMAT = "Y-m-d";
    const DATE_FORMAT_SP = "-";
    const DEFAULT_START_DATE = "-1 week";
    const DEFAULT_END_DATE = "-1 day";
    const LANG = "zh_CN";

    const TIMEZONE = 'Asia/Shanghai';
    const VERSION = '';
    const BRANCHES = "";
}
?>
