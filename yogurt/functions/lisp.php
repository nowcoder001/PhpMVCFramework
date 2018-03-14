<?php
class lisp {
    static public function oddp($int) {
        //return true;
        return abs($int)%2 == 1;
    }
    
    static public function evenp($int) {
        return abs($int)%2 == 0;
    }
    
    static public function atomp($var) {
        return !php::is_array($var);
    }

    static public function car($cons) {
        return current($cons);
    }

    static public function cdr($cons) {
        //return array_slice($cons, 1);
        return $cons[1];
    }

    static public function cons($car, $cdr) {
        return [$car, $cdr];
    }

    static public function consp($cons) {
        return php::is_array($cons) && count($cons) == 2;
    }

    static public function quote() {
        return count(func_get_args()) > 1?func_get_args():func_get_arg(0);
    }
}
?>
