<?php
class time {
    static function strtotime($time = 'now') {
        return php::call_user_func_array('strtotime', func_get_args());
    }

    static function strtomicrotime($time = 'now') {
        return php::call_user_func_array([__CLASS__, 'strtotime'], func_get_args()) * 1000;
    }

    static function microtime() {
        return round(microtime(true) * 1000);
    }

    static function strtotimestr($str, $format = false) {
        $format = $format?$format:Config::DATE_FORMAT;
        return date($format, strtotime($str));
    }
    static function start_of_date($date) {
        return self::strtomicrotime("{$date} 00:00:00");
    }

    static function end_of_date($date) {
        return self::strtomicrotime("{$date} 23:59:59");
    }
}
?>
