<?php
class arr {
    static function arrayp($arr) {
        return php::is_array($arr);
    }

    static function is_array() {
        return count(func_get_args()) > 1?
               self::map(['php', 'is_array'], func_get_args()):
               php::is_array(func_get_arg(0));
    }

    static function map() {
        return self::call(['php', 'array_map'], func_get_args());
    }

    static function merge() {
        $args = arr::filter(function($arr) {
            return self::is_array($arr) && count($arr);
        }, func_get_args());
        return count($args)?self::call(['php', 'array_merge'], $args):[];
    }

    static function call() {
        return call_user_func_array('call_user_func_array', func_get_args());
    }

    static function keys($array) {
        return php::array_keys($array);
    }

    static function is_assoc($var) {
        return array_diff_assoc(php::array_keys($var), range(0, sizeof($var))) ? TRUE : FALSE;
    }

    static function implode($glue, $arr) {
        return self::join($glue, $arr);
    }

    static function split($glue, $str) {
        return explode($glue, $str);
    }

    static function join($glue, $arr) {
        return self::is_array($arr)?implode($glue, $arr):[];
    }

    static function map_join() {
        return self::call([__CLASS__, 'map_implode'], func_get_args());
    }

    static function map_implode() {
        $args = func_get_args();
        $glue = array_shift($args);

        return self::implode($glue, self::call(['php', 'array_map'], $args));
    }

    static function reindex($array) {
        return array_values($array);
    }

    static function gethash($map, $key, $default = null) {
        return self::haskey($key, $map)? $map[$key] : $default;
    }

    static function haskey($key, $map) {
        return self::arrayp($map)? array_key_exists($key, $map) : false;
    }

    static function toString($arr) {
        return json::encode($arr);
    }

    static function filter($func, $arr, $flag = 0) {
        return array_filter($arr, $func, $flag);
    }

    static function combine() {
        return php::call_user_func_array('array_combine', func_get_args());
    }
    
    static function column() {
        return self::call('array_column', func_get_args());
    }

    static function combine_column($arr, $kcol, $vcol) {
        return self::combine(self::column($arr, $kcol), self::column($arr, $vcol));
    }
    
    static function columns($arr, $cols) {
        return self::combine($cols, self::map(function($col) use ($arr) {
            return self::gethash($arr, $col);
        }, $cols));
    }
    
    static function keys_array($assoc, $keys) {
        $filtered = php::array_filter(function($assoc, $key) use($keys) {
            return php::in_array($key, $keys);
        }, $assoc, array_keys($assoc));
        
        $sorted = [];
        foreach($keys as $key) {
            $sorted[$key] = $filtered[$key];
        }
        return $sorted;
    }

    static function push($array, $push) {
        return self::rpush($array, $push);
    }

    static function rpush($array, $push) {
        array_push($array, $push);
        return $array;
    }

    static function reduce($func, $arr, $initial = null) {
	return array_reduce($arr, $func, $initial);
    }

    static function lpush($array, $element) {
        array_unshift($array, $element);
        return $array;
    }

    /**
     * 将一个平面的二维数组按照指定的字段转换为树状结构
     *
     * 当 $returnReferences 参数为 true 时，返回结果的 tree 字段为树，refs 字段则为节点引用。
     * 利用返回的节点引用，可以很方便的获取包含以任意节点为根的子树。
     *
     * @param array $array
     *            原始数据
     * @param string $fid
     *            节点ID字段名
     * @param string $fparent
     *            节点父ID字段名
     * @param string $fchildrens
     *            保存子节点的字段名
     * @param boolean $returnReferences
     *            是否在返回结果中包含节点引用
     *
     *            return array
     */    
    static function tree($array = array(), $fid, $fparent = 'parent_id', $fchildrens = 'childrens', $returnReferences = false) {
        $pkvRefs = array();
        foreach ($array as $offset => $row) {
            $pkvRefs[$row[$fid]] = & $array[$offset];
        }

        $tree = [];
        foreach ($array as $offset => $row) {
            $parentId = $row[$fparent];
            if ($parentId) {
                if (! isset($pkvRefs[$parentId]))
                    continue;

                $parent = & $pkvRefs[$parentId];
                $parent[$fchildrens][] = & $array[$offset];
            }
            else {
                $tree[] = & $array[$offset];
            }
        }

        if ($returnReferences) {
            return [ 'tree' => $tree, 'refs' => $pkvRefs ];
        }
        else {
            return $tree;
        }
    }
}
?>
