<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
 
class MongoClientUtils extends MongoClient{
    
    /**
	 * construct of MongoUtils
	 */
	public function __construct($schema) {
		global $CONF;
		return parent::__construct("mongodb://".$CONF["mongo"][$schema]);
	}
	 
    
}
?>