<?php
class sql {
    static function bicond( $field, $value, $op = '=') {
        return ( $field === [] || $field === '' || $field === false || $value === [] || $value === '' || $value === false )?"":(is_array($value)?self::incond($field, $value):arr::join(' ', [$field, $op, self::value($value)] ));
    }

    static function between($field, $start, $end) {
        return arr::join(' ', [ $field, 'between', self::value($start), 'and', self::value($end)] );
    }

    static function value($value) {
        return str::q($value, "'");
    }

    static function field($field) {
        return str::q($field, '`');
    }
    
    static function incond($field, $value) {
        return (empty($value) || !is_array($value))?"":arr::join(' ', [$field, 'in', str::parenthese(arr::join(',', arr::map([__CLASS__, 'value'], $value)) ) ] );
    }

    static function like($field, $str) {
        return $str?"{$field} like '{$str}'":"";
    }

    static function group() {
        return arr::join(', ', arr::map([__CLASS__, 'field'], func_get_args()));
    }

    static function order($sort, $order = 'desc') {
        return self::field_esc($sort)." ".self::order_esc($order);
    }

    static function limit() {
        return arr::join(',' , arr::map('intval', array_slice(func_get_args(), 0, 2)));
    }

    static function page_limit($page = 1, $rows = 30) {
        $start = ($page - 1) * $rows;
        return self::limit($start, $rows);
    }

    static function wie($as, $alias = '') {
        $alias = $alias?$alias:$as;
        return arr::join(' as ', [$as, $alias]);
    }

    static function func($func, $field) {
        return $func.str::parenthese($field);
    }
    
    static function sum($sum) {
        return self::func('sum', $sum);
    }

    static function count($count) {
        return self::func('count', $count);
    }

    static function avg($avg) {
        return self::func('avg', $avg);
    }

    static function max($max) {
        return self::func('max', $max);
    }

    static function min($min) {
        return self::func('min', $min);
    }

    static function sum_as($sa) {
        return self::wie(self::sum($sa), $sa);
    }

    static function distinct($distinct) {
        return self::func('distinct', $distinct);
    }

    static function case_when($case, $t = 1, $f = false) {
        $f = $f?" else {$f} ":"";
        return "case when {$case} then {$t} {$f} end";
    }

    //   _   _
    // | * v *  |
    static function create_table_sql($name, $fields, $charset = "utf8", $engine = "InnoDB") {
        $charset = $charset?$charset:"utf8";
        $engine = $engine?$engine:"InnoDB";
        return "CREATE TABLE IF NOT EXISTS `$name` (".implode(', ', $fields).") ENGINE=$engine DEFAULT CHARSET=$charset;";
    }

    static function rqcond($fields = false, $data = false) {
        $fields = $fields?$fields:array_keys($_REQUEST);
        $data = $data?$data:$_REQUEST;
        return arr::map(function($field) use($data) {
            return sql::bicond($field, arr::gethash($data, $field));
        }, arr::filter(function($field) {return arr::gethash($data, $field);}, $fields));
    }

    static function tosql($arr) {
        $select = $arr['select']?$arr['select']:'*';
        $join = $arr['join']?" left join {$arr['join']}":'';
        $where = $arr['where']?" where {$arr['where']} ":"";
        $order = $arr['order']?" order by {$arr['order']} ":"";
        $having = $arr['having']?" having {$arr['having']}":"";
        $group = $arr['group']?" group by {$arr['group']} ":"";
        $limit = $arr['limit']?" limit {$arr['limit']} ":"";
        return "select {$select} from {$arr['from']} {$join} {$where} {$group} {$order} {$limit}";
    }

    static function quote_esc($str) {
	return str_replace(['`', "'", '"'], ['\`', "\'", '\\"'], $str);
    }

    static function slash_esc($str) {
	return str_replace(["\\"], [""], $str);
    }

    static function kw_esc($str, $addition = false) {
	$word_list = ['select', 'insert', 'delete', 'update', 'not', 'union', 'into', 'load_file', 'outfile', 'sleep', '--'];
	return str_ireplace($word_list, [], $str);
    }

    static function field_esc($str) {
	return self::quote_esc($str);
    }

    static function order_esc($str) {
	switch($str) {
	    case 'desc':
	    case 'asc':
		return $str;
	    default:
		return 'desc';
	}
    }

    static function cond_join($conds, $glue = 'and') {
        return arr::join(" {$glue} ", arr::filter("strlen", $conds));
    }
}
?>
