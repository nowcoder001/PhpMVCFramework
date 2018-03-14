<?php
/*
 * R for Request
 */
class R {
    static function get($field, $default = null) {
        return arr::gethash($_GET, $field, $default);
    }

    static function post($field, $default = null) {
        return arr::gethash($_POST, $field, $default);
    }

    static function rqst($field, $default = null) {
        return arr::gethash($_REQUEST, $field, $default);
    }

    static function gid($default = null) {
	$gid = self::rqst('gid', $default);
	return $gid?intval($gid):null;
    }

    static function sdkid($default = null) {
	$sdkid = self::rqst('sdkid', $default);
	return $sdkid?intval($sdkid):null;
    }

    static function serverid($default = null) {
	$serverid = self::rqst('serverid', $default);
	return $serverid?intval($serverid):null;
    }

    static function channel($default = null) {
	return sql::quote_esc(self::rqst('channel', $default));
    }

    static function sort($default = null) {
	return sql::quote_esc(self::rqst('sort', $default));
    }

    static function order($default = null) {
	return sql::quote_esc(self::rqst('order', $default));
    }

    static function page($default = 1) {
	return intval(self::rqst('page', $default));
    }

    static function rows($default = 30) {
	return intval(self::rqst('rows', $default));
    }
    
    static function setRqst($key, $value = "") {
	$_REQUEST[$key] = $value;
	return $value;
    }

    static function field($name, $nil = false, $default = null, $func = false) {
	$quote = function($item) {
	    return $time;
	};
	$func = $func?$quote:$func;
	return [$name, $nil, $default, $func, $field?$field:$name];
    }

    static function getAttr($field, $attr) {
	$list = [
	    "name" => 0,
	    "null" => 1,
	    "default" => 2,
	    "func" => 3
	];
	return arr::gethash($field, arr::gethash($list, $attr));
    }

    static function getName($field) {
	return self::getAttr($field, "name");
    }

    static function getNull($field) {
	return self::getAttr($field, "null");
    }

    static function getDefault($field) {
	return self::getAttr($field, "default");
    }

    static function getFunc($field) {
	return self::getAttr($field, "func");
    }
    
    static function getValue($field) {
	$func = self::getFunc($field);
	return $func(self::rqst(self::getName($field), self::getDefault($field)));
    }

    static function isNull($value) {
	return $value === null || $value === false || $value === "";
    }
    
    static function getParams($fields) {
	return !self::nullFilter($fields)?
	       null:
	       arr::combine(arr::column($fields, 0), arr::map([__CLASS__, "getValue"], $fields));
    }

    static function nullFilter($fields) {
	return arr::reduce(function($flag, $field) {
	    return $flag && (!self::isNull(self::getValue($field)) || self::getNull($field));
	}, $fields, true);
    }
}
?>
