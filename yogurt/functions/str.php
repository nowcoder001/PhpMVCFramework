<?php
class str {
    static function quote($string, $q = '"') {
        return $q.$string.$q;
    }

    static function q($string, $q = '"') {
        return $q.$string.$q;
    }

    static function bq($string) {
        return self::q($string, '`');
    }

    static function backquote($string) {
        return self::bq($string);
    }

    static function map_implode() {
        return php::call_user_func_array(['arr', 'map_implode'], func_get_args());
    }

    static function split($glue, $str) {
        return explode($glue, $str);
    }

    static function explode($glue, $str) {
        return self::split($glue, $str);
    }

    static function fix($arr, $fix = "in") {
        return implode("", tri::fix($arr, $fix));
    }

    static function instr($search, $str, $glue = ",") {
        return in_array($search, explode($glue, $str));
    }

    static function parenthese($inner, $parenthese = ['(', ')']) {
        return $parenthese[0].$inner.$parenthese[1];
    }
}
?>
