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
 *  封装Memcache类，其实 Memcache够简单了
 */
if ( !extension_loaded('memcache') ) {
       throw new Exception('memcache is not enable!');  
}
class MCUtils extends Memcache{

    function MCUtils($schema='cache') {
    	global $MEMCACHED;
    	foreach ($MEMCACHED[$schema] AS $server) {
    		$this->addServer($server['host'], $server['port']);
    	}
    }   
}
?>