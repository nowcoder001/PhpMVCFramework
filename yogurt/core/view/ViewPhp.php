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
 * php view class of yogurt framework.
 * @filesource		yogurt/core/view/ViewPhp.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

class ViewPhp extends View {
	/**
	 * construct of View
	 * init smarty
	 */
    public function __construct() {
    	parent::View();    	
    }
  
    /**
	 * add value array
	 * @param mix $key
	 * @param mix $value
	 */
	public function addValue($key, $value = null) {
		if (is_array($key) && null == $value) {
			$this->valueArray = array_merge($this->valueArray, $key);
		} else {
			$this->valueArray[$key] = $value;
		}
	}
    
    /**
     * assign align setVar
     */
    public function assign($key, $value = null) {   	
    	$this->addValue($key, $value);   	
    }
    
    /**
     * fetch contents from view
     */
    public function fetch($y_view_tpl) {
    	ob_start();   	   	
    	extract($this->valueArray);  	   	 
		require_once ($y_view_tpl);
    	$contents = ob_get_contents();
    	ob_end_clean();
  	    return $contents;
    }
    
    /**
     * destruct of View
     */
    public function __destruct() {   	   	
    }
}
?>