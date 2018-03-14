<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *  功能性函数
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */

// 取得IP
function get_ip() {
    $ip = (empty($_SERVER['HTTP_CLIENT_IP']) ? (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_X_FORWARDED_FOR']) : $_SERVER['HTTP_CLIENT_IP']);
    return long2ip(ip2long($ip));
}

// echo "Random Password ".rand_str(10,true);
function rand_str($passwordlength = 10, $pwd = false) {
    // add numbers,alphabets and special chars
    $random_chars = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if ($pwd)
        $random_chars .= "!@#$%";
    $getstring = "";
    $password = "";
    // While loop will execute until the length of password reaches the $passwordlength
    while (strlen($password) < $passwordlength) {
        // returns a sigle character from the set of random_chars
        $getstring = substr($random_chars, mt_rand(0, strlen($random_chars) - 1), 1);
        // Avoid already existed character from the password.
        if (! strstr($password, $getstring)) {
            // append the generated value to password
            $password .= $getstring;
        }
    }
    return ($password);
}

/*
 * 连续建目录
 * string $dir 目录字符串
 * int $mode 权限数字
 * 返回：顺利创建或者全部已建返回true，其它方式返回false
 */
function data2token($data = array(), $key = YOGURT_KEY) {
    $data["t"] = time();
    $sign = md5(md5($data["uid"] . $data["t"]) . $key);
    return base64_encode("sign=" . $sign . "&data=" . json_encode($data));
}

function token2data($token, $key = YOGURT_KEY) {
    $tmp = array();
    parse_str(base64_decode($token), $tmp);
    $data = json_decode($tmp["data"], true);
    if (md5(md5($data['uid'] . $data['t']) . $key) != $tmp['sign']) {
        exit(json_encode(array(
            'errCode' => - 1,
            'errMsg' => 'sign error.'
        )));
    }
    return $data;
}

function uploadfile($path = 'upload', $exts = ['gif', 'jpeg', 'jpg', 'png', 'log']) {
    $result = array_combine(
	    array_keys($_FILES),
	    array_map(function($files) use($path, $exts) {
	        return single_file_handler($files, $path, $exts);
	    }, $_FILES));
    return $result;
}

function single_file_handler($files, $path, $exts = ['gif', 'jpeg', 'jpg', 'png', 'log']) {
    if($files['error'] >　0) {
	    return code_msg('5', 'upload error');
    }
    if(!is_dir($path)) {
	    exit(code_msg("4", "no such directory:$path"));
    }
    $explode = explode(".", $files['name']);
    if(!in_array(end($explode), $exts )) {
	    exit(code_msg("3", "wrong file type"));
    }

    $destination = $path.'/'.$files['name'];
    return move_uploaded_file($files['tmp_name'], $destination);
}

global $LANG_MAP;
function load_lang_map() {
    $session = Session::getInstance();

    $lang = $session->getValue('lang');

    $lang_list = ['zh_CN', 'ko_KR', 'en_US'];
    $lang = in_array($lang, $lang_list)?$lang:'zh_CN';

    $grandMap = Model::getDao()->table('lang')->findAll("id, type, {$lang}");

    $nested = [];
    foreach($grandMap as $row) {
        $nested[$row['type']][$row['id']] = $row[$lang];
    }

    return $nested;
}

$LANG_MAP = load_lang_map();

function Lgenerator($type = 'lang') {
    return function() use($type) {
        $session = Session::getInstance();
        $glue = in_array($session->getValue('lang'),['en_US','ko_KR'])?' ':'';
        return str_replace(
            ["\n"],
            [''],
            implode($glue, arr::map(function($word) use ($type) {
                global $LANG_MAP;
                $word = strtolower($word);
                $map = $LANG_MAP[$type];
                
                $lang = arr::gethash($map, $word, false);

                if(!isset($map[$word])) {
                    try {
                        Model::getDao()->table('lang')->insert(['id' => $word, 'type' => $type, 'zh_CN' => $word, 'ko_KR' => $word, 'en_US' => $word]);
                    }
                    catch (Exception $e) {
                        return $word;
                    }
                    return $word;
                }

                return $lang?$lang:$word;
            }, func_get_args())));
    };
}

function L() {
    $func = Lgenerator('lang');
    return call_user_func_array($func, func_get_args());
}
?>
