<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
class Y extends YBase{
	public static $_objects = array();

    public static function set($name, $object) {
        self::$_objects[$name] =$object;
    }

    public static function get($name) {
        return self::$_objects[$name];
    }
}

/**
 * 设置对象的自动载入
 */
Y::registerAutoload();
?>
