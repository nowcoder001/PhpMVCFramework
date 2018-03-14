<?php
class json {
    static public function encode($json) {
        return json_encode($json);
    }

    static public function decode($str, $flag = true) {
        if(!$str)
            return null;
        return json_decode($str, $flag);
    }
}
?>
