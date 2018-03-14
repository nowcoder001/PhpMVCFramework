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
 * base view class of yogurt framework.
 * @filesource		yogurt/core/view/View.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
/*
 * View 功能 ：
 * 1 模板视图{php、phplib、smarty....}
 * 2 UI组件{树形结构、标签[Tag]等，这部分内容也可结合js实现，如easyui}
 * 3 cache
 * 4 交互{ajax、vaildator等；采用js(jquery)或是如xajax、Rspa 等实现}
 * 5 国际化
 */

abstract class View extends Object {
    private static $I18NArray = array();
    protected $mimeName = Config::VIEW_MIME_NAME;
    private $tplDir;
    private $tplName;
    protected $valueArray = array();

    /**
     * construts of view
     * @param string $viewType
     */
    public function View() {
	parent::Object();
    }

    /**
     * get mime type
     * @return string
     */
    public function getMimeName() {
    	return $this->mimeName;
    }

    /**
     * set mime type
     */
    public function setMimeName($mimeName = 'html') {
    	$this->mimeName = $mimeName;
    }

    /**
     * set tpl dir
     * @param $dir
     */
    public function setTplDir($dir = './') {
    	$this->tplDir = $dir;
    }

    /**
     * get tpl dir
     * @return string
     */
    public function getTplDir() {
    	return $this->tplDir;
    }

    /**
     * set tpl name
     * @param string $tplName
     */
    public function setTplName($tplName = '') {
    	$this->tplName = $tplName;
    }

    /**
     * get tpl name
     * @return string
     */
    public function getTplName() {
    	return $this->tplName;
    }

    /**
     * get cache type
     * @return string
     */
    public function getCacheType() {
    	return $this->cacheType;
    }

    /**
     * set cache type
     * @param string $cacheType
     */
    public function setCacheType($cacheType) {
    	$this->cacheType = $cacheType;
    }

    /**
     * set isCached
     */
    public function setCacheTime($cacheTime) {
    	$this->cacheTime = $cacheTime;
    }

    /**
     * display tpl page
     */
    public function display($tpl=null) {
	$mimeType = Mime::getMimeType($this->mimeName);
	header('Content-type: ' . strtolower($mimeType) . '; charset='.Config::APP_CHARACTER);

	if(is_null($tpl)) {
	    $tpl=$this->getTplDir() . '/' .  $this->getTplName();
	}

	if (!is_readable($tpl)) {
            Y::errors(404,"Tpl File \"$tpl\" was can\'t loaded!");
        }
        $value = $this->fetch($tpl);
	

        echo $value;
    }

    abstract public function assign($key, $value = null);
    /**
     * abstract fetch contents from view
     */
    abstract public function fetch($tpl);
}
?>
