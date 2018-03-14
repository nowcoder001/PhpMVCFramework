<?php
class tri extends arr {
    // prefix default
    static public function fix($arr, $fix = "pre") {
        switch($fix) {
            case "in":
                return [$arr[1], $arr[0], $arr[2]];
            case "post":
                return [$arr[1], $arr[2], $arr[0]];
            case "pre":
                return $arr;
            default:
                return $arr;
        }
    }
}
?>
