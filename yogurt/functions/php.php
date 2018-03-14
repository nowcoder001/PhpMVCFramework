<?php
class php {
    static public function is_array($var) {
        return is_array($var);
    }

    static public function call_user_func_array($callable, $param_arr) {
        $param_arr = $param_arr?$param_arr:[];
        return call_user_func_array($callable, $param_arr);
    }

    static public function call_user_func() {
        return call_user_func_array("call_user_func", func_get_args());
    }

    static public function array_keys($array) {
        return self::is_array($array) && !empty($array) ? array_keys($array) : [];
    }

    static public function array_map() {
        $args = func_get_args();
        $first = $args[1];
        return count($first)?array_combine(self::array_keys($first), self::call_user_func_array("array_map", $args)):[];
    }

    static public function array_merge() {
        return self::call_user_func_array("array_merge", func_get_args());
    }

    static public function devide($devide = 0, $by = 1) {
        return $by == 0? 0 : ($devide / $by);
    }

    static public function strtomicrotime($str) {
        return strtotime($str) * 1000;
    }

    /* !warning!*/
    static public function printf($var) {
        return print(self::is_array($var)?arr::toString($var):$var);
    }
    /* */

    static public function array_filter() {
        $args = func_get_args();
        $func = $args[0];
        $arr = $args[1];

        $bool = self::call_user_func_array([__CLASS__, 'array_map'], $args);

        $filtered = [];
        foreach($bool as $key => $filter) {
            if($filter)
                $filtered[$key] = $arr[$key];
        }
        return $filtered;
    }

    static public function in_array() {
        return call_user_func_array('in_array', func_get_args());
    }

    static public function microtime() {
        return round(microtime(true) * 1000);
    }
}
?>
