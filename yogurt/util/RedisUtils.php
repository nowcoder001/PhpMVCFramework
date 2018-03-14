<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */

if (!extension_loaded('redis')) {
    throw new Exception('redis is not enable!');
}
class RedisUtils extends Redis {

    function RedisUtils($schema='main') {
    	global $REDISD;
    	parent::__construct();

        $server = $REDISD[$schema];

    	$this->connect($server['host'], $server['port']);
    	$this->auth('');

    	return $this;
    }
}
?>
