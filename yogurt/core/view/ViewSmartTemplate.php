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
 * @filesource		yogurt/core/view/ViewSmartTemplate.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
include_once (LIB_DIR.'smarttemplate-1.2.1/class.smarttemplate.php');  

class ViewSmartTemplate extends View {
	private $smart=null;

    public function __construct() {
    	parent::View();
    	$this->smart = new SmartTemplate; 
    	$this->smart->template_dir    =   CACHE_DIR;       //TPL_DIR 
		$this->smart->temp_dir        =   CACHE_DIR;     
		$this->smart->cache_dir       =   CACHE_DIR;
    	$this->smart->cache_lifetime  =   60 * 60 * 24;     
    }
       /**
     * assign tpl var
     * @param mix $varName
     * @param mix $value
     */
    public function assign($key,$value=''){
    if(''!=$value){
      $this->smart->assign($key,$value);
    }else{
      $this->smart->assign($key);
    }
  
    }
     /**
     * fetch contents from view
     */
    public function fetch($tpl){
    	$this->smart->assign($this->valueArray);  	
    	 /*if($tpl!=null){// 由于smarttemplate 不像smarty 那样 直接传递模板路径，故重组路径     
 	      $pot = strrpos($tpl, "/");
          $tplDir = substr($tpl,0,$pot);  
          $tplName = substr($tpl,$pot+1,strlen($tpl));     
          $this->smart->template_dir =$tplDir ; 
    	  $this->smart->set_templatefile ($tplName)  ;
    	 }else{
    	 	
    	 }
     */
          $this->smart->template_dir = $this->getTplDir();  
    	  $this->smart->set_templatefile ($this->getTplName())  ;
    	 return  $this->smart->output(); 
    }
    
    
   public function append($name,$value){
   $this->smart->append($name,$value);
   }
   
   public function __destruct(){
         unset($this->smart);
   }
}
?>