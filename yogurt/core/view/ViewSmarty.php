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
 * smarty view class of yogurt framework.
 * @filesource		yogurt/core/view/ViewSmarty.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

//include_once (LIB_DIR. 'Smarty-3.1.8/libs/Smarty.class.php'); 

class ViewSmarty extends View {
	public $smarty = null;
	
	/**
	 * construct of View
	 * init smarty
	 */
    public function __construct() {
        include_once('smarty/libs/Autoloader.php');

        Smarty_Autoloader::register();
        
    	parent::View();
    	$this->smarty = new Smarty();
		$this->smarty->template_dir    =   CACHE_DIR;    //TPL_DIR     
		$this->smarty->compile_dir     =   CACHE_DIR;    
		$this->smarty->cache_dir       =   CACHE_DIR;          
		$this->smarty->cache_lifetime  =   60 * 60 * 24;            
		$this->smarty->caching         =   false;                     
		$this->smarty->left_delimiter  =  '<%';                   
		$this->smarty->right_delimiter =  '%>';
		$this->smarty->debugging = false;
    }
    
    /**
     * get smarty class
     * @return class
     */
    public function getSmarty() {
    	return $this->smarty;
    }
    
    /**
     * assign tpl var
     * @param mix $varName
     * @param mix $value
     */
    public function assign($key, $value = null) {
    	if ('' != $value) {
    		$this->smarty->assign($key, $value);
    	} else {
    		$this->smarty->assign($key);
    	}
    }
    
    /**
     * fetch contents from view
     */
    public function fetch($tpl) {
    	$this->smarty->assign($this->valueArray);  	
    	 if($tpl==null){
    	 $this->smarty->template_dir = $this->getTplDir();
    	 return $this->smarty->fetch($this->getTpl());
    	 }else
    	 return $this->smarty->fetch($tpl);
    }
    
    //if you want, your can add more method to encapsulation smarty method
    ////////////////////////////////////////////
    public function append($var) {
    	$this->smarty->append($var);
    }
    
    public function clearAllAssign() {
    	$this->smarty->clear_all_assign();
    }
    
    public function clearAllCache($exp_time = null) {
    	$this->smarty->clear_all_cache($exp_time);
    }
    
    public function clearCompliedTpl($tpl_file = null, $compile_id = null, $exp_time = null) {
    	$this->smarty->clean_complied_tpl($tpl_file, $compile_id, $exp_time);
    }
    
    public function clearConfig($var = null) {
    	$this->smarty->clear_config();
    }
    
    public function configLoad ($file, $section = null, $scope = 'global') {
    	$this->smarty->config_load($file, $section, $scope);
    }
    
    public function isCached($tpl_file, $cache_id = null, $compile_id = null) {
    	return $this->smarty->is_cached($tpl_file, $cache_id, $compile_id);
    }
    
    public function loadFilter($type, $name) {
    	$this->smarty->load_filter($type, $name);
    }
    
    public function registerBlock($block, $block_impl, $cacheable=true, $cache_attrs=null) {
    	$this->smarty->register_block($block, $block_impl, $cacheable, $cache_attrs);
    }
    
    public function registerModifier($modifier, $modifier_impl) {
    	$this->smarty->register_modifier($modifier, $modifier_impl);
    }
    
    /**
     * destruct of View
     */
    public function __destruct() {   	
    	unset($this->smarty);
    }
}
?>
