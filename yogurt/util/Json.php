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
 * json class of yogurt framework.
 * @filesource		yogurt/utils/Json.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

//if php version < 5.2, use Service_Json class
if (!function_exists('json_encode') || !function_exists('json_decode')) {	
     require_once ( LIB_DIR. 'tools/JSON.php');
	define('SERVICES_JSON', 1);
}

class Json {
	/**
	 * construct of Json
	 */
	public function Json() {
	}
	
	/**
	 * encode value
	 * @param mix $value
	 * @return string
	 */
	public static function encode($value) {
		if (!defined('SERVICES_JSON')) {
			return json_encode($value);
		}
		
		$json =  new Services_JSON();
		$result = $json->encode($value);
		
		if (Services_JSON::isError($result)) {
			throw new Exeception('json can\'t encode this value!');
		}
		
		return $result;
	}
	
	/**
	 * decode json value
	 * @param string $value
	 * @return mix
	 */
	public static function decode($value) {
		if (!defined('SERVICES_JSON')) {
			return json_decode($value);
		}
		$json =  new Services_JSON();
		$result = $json->decode($value);
		
		if (Services_JSON::isError($result)) {
			throw new Exeception('json can\'t encode this value!');
		}
		
		return $result;
	}
}
?>