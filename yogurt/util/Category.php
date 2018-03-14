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
 * file utils class of yogurt framework.
 * @filesource		yogurt/utils/Category.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */

class Category
{
    var $catData = array();
    var $catTree = array();

    function Category($data=null) {
    	error_reporting(0); 
        if ( !empty($data) ) {
            $this->catData = $data; }
        foreach ( $this->catData as $cat ) {
            if ( $cat['Category']['parent_id'] != 0 ) {
                $parentKey = $this->_findParent($cat['Category']['parent_id']);
                $this->catData[$parentKey]['SubCategories'] = array(); }}
        
        $this->catTree = $this->_tree();

    }

    function generateList() {
        return $this->_list($this->catTree); }

    function generatePlain() {
        return $this->_plain($this->catTree); }

    function generateOptions($selected=null) {
        return $this->_options($this->catTree, $selected); }

    function generateArray() {
        $_array = explode("\n", $this->_array($this->catTree));
        $end = end($_array);
        $cats = array();
        if ( empty($end) ) {
            array_pop($_array); }
        foreach ( $_array as $cat ) {
            eval('$cats[]["Category"] = ' . $cat); }
        return $cats; }

    function generateIdIndexedArray() {
        $_array = explode("\n", $this->_idIndexedArray($this->catTree));
        $end = end($_array);
         $cats = array();
        if ( empty($end) ) {
            array_pop($_array); }
        foreach ( $_array as $cat ) {
            eval('$cats' . $cat . ';'); }
        return $cats; }

//  pvirate

    function _list($data) {
        $orderedCats = "\n<ul>";

        foreach ( $data as $cat ) {
            $orderedCats .= "\n<li id=\"Category_{$cat['Category']['id']}\">" . $cat['Category']['name'] . "</li>";
            if ( $cat['SubCategories'] ) {
                $orderedCats .= $this->_list($cat['SubCategories']); }}

        $orderedCats .= "\n</ul>";
        return $orderedCats; }

    function _plain($data, $indent=0, $isSub=false) {
        $orderedCats = '';
        for ( $i = 0,       $indentText = '';
              $i < $indent;
              $i++,         $indentText .= '    ' );

        foreach ( $data as $cat ) {
            $orderedCats .= $indentText
                         . $cat['Category']['name']
                         . "\n";
            if ( $cat['SubCategories'] ) {
                $orderedCats .= $this->_plain($cat['SubCategories'], $indent+1, true); }}

        return $orderedCats; }

    function _options($data, $selected=null, $indent=0, $isSub=false) {
        $orderedCats = $isSub ? '' : '<option value="0">一级菜单</option>';
        for ( $i = 0,       $indentText = '';
              $i < $indent;
              $i++,         $indentText .= '&nbsp;&nbsp;&nbsp;&nbsp;' );
         
        foreach ( $data as $cat ) {
            $orderedCats .= "\n"
                         . '<option value="' . $cat['Category']['id'] . '"'
                         . ($cat['Category']['id'] == $selected ? ' selected="selectd"' : '')
                         . '>'
                         . $indentText
                         . $cat['Category']['name']
                         . '</option>';
            if ( $cat['SubCategories'] ) {
                $orderedCats .= $this->_options($cat['SubCategories'], $selected, $indent+1, true); }}

        return $orderedCats; }

    function _array($data, $indent=0, $isSub=false) {
        $orderedCats = '';

        foreach ( $data as $cat ) {
            $orderedCats .= "array("
                         . "'id' => '{$cat['Category']['id']}',"
                         . "'indent' => '$indent',"
                         . "'name' => '" . str_replace("'", "\'", $cat['Category']['name']) . "'"
                         . ");\n";
            if ( $cat['SubCategories'] ) {
                $orderedCats .= $this->_array($cat['SubCategories'], $indent+1, true); }}

        return $orderedCats; }

    function _idIndexedarray($data, $indent=0, $isSub=false) {
        $orderedCats = '';
        for ( $i = 0,       $indentText = '';
              $i < $indent;
              $i++,         $indentText .= '&nbsp;&nbsp;&nbsp;&nbsp;' );

        foreach ( $data as $cat ) {
            $orderedCats .= "[{$cat['Category']['id']}]="
                         . "'" .$indentText
                         . str_replace("'", "\'", $cat['Category']['name']) . "'"
                         . "\n";
            if ( $cat['SubCategories'] ) {
                $orderedCats .= $this->_idIndexedarray($cat['SubCategories'], $indent+1, true); }}

        return $orderedCats; }



//  Basic algorithm

    function _findParent($parent_id) {
        foreach ( $this->catData as $key => $cat ) {
            if ( $cat['Category']['id'] == $parent_id ) {
                return $key; }}
        return false;
		}

    function _findChildren($id) {
        $children = array();
        foreach ( $this->catData as $cat ) {
            if ( $cat['Category']['parent_id'] == $id ) {
                $children[] = $cat; }}
        return $children; }

    function _tree($cat=null) {
        if ( $cat ) {
            $offset = $this->_findChildren($cat['id']);
            for ( $i = 0; $i < count($offset); $i++ ) {
                if ( isset($offset[$i]['SubCategories']) ) {
                    $offset[$i]['SubCategories'] = $this->_tree($offset[$i]['Category']); }}
            return $offset; }

        for ( $i = 0; $i < count($this->catData); $i++ ) {
            if ( $this->catData[$i]['Category']['parent_id'] == 0 ) {
                $tree[$i] = $this->catData[$i];
                if ( isset($this->catData[$i]['SubCategories']) ) {
                $tree[$i]['SubCategories'] = $this->_tree($this->catData[$i]['Category']); }}}

        return $tree; }
}

?>