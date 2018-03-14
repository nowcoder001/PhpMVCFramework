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
 * QuickForm class of yogurt framework.
 * @filesource		yogurt/utils/QuickForm.class.php
 * @since			YOGURT v 0.9
 * @version			$3.0
 */

require_once ($qf='HTML/QuickForm.php');
class QuickForm extends HTML_QuickForm {
	public $_jsPrefix = '您所输入的表单信息无效.';
	public $_jsPostfix = '请重新检查表单中的字段.';
	public $_maxFileSize = 2007152; // 1 Mb = 1048576
	public $_requiredNote = '<span style="font-size:80%; color:#ff0000;">*</span><span style="font-size:80%;"> 表示必填字段</span>';
	
	/**
	 * construct of QuickForm
	 */
    public function QuickForm($formName='', $method='post', $action='', $target='', $attributes=null, $trackSubmit = false) {
    	$action = ('' != $action) ? $action : Url::getUrl();
    	
    	parent::HTML_QuickForm($formName, $method, $action, $target, $attributes, $trackSubmit);
    }
    
    /**
     * set js prefix
     * @param string $jsPrefix
     */
    public function setJsPrefix($jsPrefix = '') {
    	$this->_jsPostfix = ('' != $jsPrefix) ? $jsPrefix : $this->_jsPrefix;
    }
    
    /**
     * get jsPrefix
     */
    public function getJsPrefix() {
    	return $this->_jsPostfix;
    }
    
    /**
     * set js postfix
     * @param string $JsPostfix
     */
    public function setJsPostfix($jsPostfix = '') {
    	$this->_jsPostfix = ('' != $jsPostfix) ? $jsPostfix : $this->_jsPostfix;
    }
    
    /**
     * get js postfix
     * @return string
     */
    public function getJsPostfix() {
    	return $this->_jsPostfix;
    }
    
    /**
     * set max file size
     * @param int $fileMaxSzie
     */
    public function setMaxFileSize($fileMaxSize = 1048576) {
    	$this->_maxFileSize = $fileMaxSize;	
    }
    
    /**
     * get max file size
     * @return int
     */
    public function getMaxFileSize() {
    	return $this->_maxFileSize;
    }
    
    /**
     * set required note
     * @param string $requiredNote
     */
    public function setRequiredNote($requiredNote = '') {
    	$this->_requiredNote = ('' != $requiredNote) ? $requiredNote : $this->_requiredNote;
    }
    
    /**
     * get required note
     * @param string
     */
    public function getRequiredNote() {
    	return $this->_requiredNote;
    }
    
    /**
     * get html string
     * @return string form html code
     */
    public function getHtml() {
    	return $this->toHtml();
    }
    
    /**
     * get js string
     * @return string client validate script
     */
    public function getValidateScript() {
    	return $this->getValidationScript();
    }
    
    /**
     * get form element array
     * @return array form element array
     */
    public function getArray() {
    	return $this->toArray();
    }
}
?>