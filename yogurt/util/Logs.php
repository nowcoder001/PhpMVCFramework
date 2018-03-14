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
  * log  log4php[主要用于apps]
  * log4php : levels->Appenders->Layouts
  * 类型:  apps 、php解析  
  * 级别: WARN、DEBUG
  * 输出: text、file、DB、email
  */
require_once(LIB_DIR.'apache-log4php-2.3.0/src/main/php/Logger.php');		      	  
class Logs extends Logger{

    function Logs() {
    }
    
    //$level="debug"
    function init(){
    	parent::configure(C_DIR.'log4php.properties');
    	return parent::getRootLogger(); //parent::getLogger('main');
    }
    
}
?>