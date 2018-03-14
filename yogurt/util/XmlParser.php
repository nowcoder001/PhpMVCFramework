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
 * xml parser for yogurt framework.
 * @filesource		yogurt/utils/XmlParser.class.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
 
@include_once($xp='XML/Parser.php');

if (!class_exists('XML_Parser')) { 	
	require_once (LIB_DIR . 'xmlParser1.28/Parser.php');
}

class XmlParser extends XML_Parser {
	
	/**
	 * constuct of XmlParser
	 */
    public function XmlParser($srcenc = null, $mode = 'event', $tgtenc = null) {
    	parent::__construct($srcenc, $mode, $tgtenc);
    }
}
?>