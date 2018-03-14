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
 * phplib view class of yogurt framework.
 * @filesource		yogurt/core/view/PhplibView.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

require_once (LIB_DIR. 'phplib-7.2c/php/template.php'); 

class ViewPhplib extends View {
	
	private  $phplibTemplate;
	private  $contents;
	
	/**
	 * construct of View
	 * init smarty
	 */
    public function __construct() {
    	parent::View();  	
    	$this->phplibTemplate = new Template('./');	
    }
    
    /**
     * get phplib template class
     * @return class
     */
    public function getPhplibTemplate() {
    	return $this->phplibTemplate;
    }
   
    /**
     * set file for phplib template
     *
     * @param string $handle
     * @param string $file
     */
    public function setFile($handle, $file = '') {
    	$this->phplibTemplate->set_file($handle, $file);
    }
    
    /**
     * set block for phplib template
     *
     * @param string $parent
     * @param string $handle
     * @param string $name
     */
    public function setBlock($parent, $handle, $name = '') {
    	$this->phplibTemplate->set_block($parent, $handle, $name);
    }
    
    /**
     * parse form form phplib template
     *
     * @param string $target
     * @param string $handle
     * @param string $append
     */
    public function parse($target, $handle, $append = false) {
    	$this->phplibTemplate->parse($target, $handle, $append);
    }
    
    /**
     * get var for phplib template
     *
     * @param string $varName
     * @return mix
     */
    public function getVar($varName) {
    	return $this->phplibTemplate->get_var($varName);
    }
    
    /**
     * get var for phplib template
     *
     * @param string $varName
     * @return mix
     */
    public function get($varName) {
    	return $this->phplibTemplate->get($varName);
    }
    
    /**
     * get vars for phplib template
     *
     * @return array
     */
    public function getVars() {
    	return $this->phplibTemplate->getVars();
    }
    
    /**
     * p function for phplib template
     *
     * @param string $varName
     * @return unknown
     */
    public function p($varName) {
    	ob_start();
    	$this->phplibTemplate->p($varName);
    	$this->contents = ob_get_contents();
    	ob_end_clean();
    }
    
    /**
     * finish function for phplib template
     * @return string
     */
    public function finish($str) {
    	return $this->phplibTemplate->finish($str);
    }
    
    /**
     * setVar function for phplib template
     *
     * @param unknown_type $varname
     * @param unknown_type $value
     */
    public function setVar($varname, $value = '') {
    	$this->phplibTemplate->set_var($varname, $value);
    }
    
    /**
     * assign align setVar
     */
    public function assign($key, $value=null) {
    	$this->setVar($key, $value);
    }
    /**
     * pparse function for phplib template
     *
     * @param string $target
     * @param string $handle
     * @param string $append
     * @return boolean
     */
    function pparse($target, $handle, $append = false) {
    	ob_start();
    	$this->phplibTemplate->pparse($target, $handle, $append);
    	$this->contents = ob_get_contents();
    	ob_end_clean();
    	
    	return false;
  	}
    
    /**
     * fetch contents from view
     */
    public function fetch($tpl) {
		if (is_array($this->valueArray)||is_array($this->getI18NArray())) {
			$this->phplibTemplate->set_var($this->valueArray);
			$this->phplibTemplate->set_var($this->getI18NArray());
		}	
		if($tpl==null){
			$this->phplibTemplate->set_root($this->getTplDir());	
		}else{
			//echo $tpl;die;
		  $this->phplibTemplate->set_root($tpl);
		}	
		
    	return $this->contents;
    }
    
    /**
     * destruct of View
     */
    public function __destruct() {
    	
    	unset($this->phplibTemplate);
    }
}
?>