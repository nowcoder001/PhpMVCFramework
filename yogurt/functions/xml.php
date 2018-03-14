<?php
class xml
{
    static protected $tab = 0;

    static public function node($name, $attr = null, $inner = '', $open = false, $tab = 0)
    {
        return $open?
               self::tab($tab)."<$name ".self::attrs($attr)." />":
               self::tab($tab)."<$name ".self::attrs($attr).">\n".self::tab($tab+ 1)."$inner\n".self::tab($tab)."</$name>";
    }

    static public function tab($tab = 0)
    {
        //return str_repeat("\t", $tab);
        return "";
    }

    static public function toxml($arr)
    {
        $name = $arr[0];
        $attr = $arr[1];
        $inner = $arr[2];
        $open = $arr[3];

        $tab = self::$tab++;
        $inner = php::is_array($inner)?
                 (php::is_array($inner[0])?
                  arr::map_implode("\n", function ($arr) {return self::toxml($arr);}, $inner):
                  self::toxml($inner)):
                 $inner;

        return is_array($arr)?
               ($name?
                self::node($name, $attr, $inner, $open, $tab) :
                "") :
               $arr;
    }

    static public function attr($key, $value)
    {
        $value = php::is_array($value)?self::array_attr($value):$value;
        return $key?str::fix(["=", $key, str::q($value)]):"";
    }

    static public function attrs($array)
    {
        return php::is_array($array[0])?
               arr::map_implode(" ", function ($arr) {return self::attr($arr[0], $arr[1]);}, $array):
               self::attr($array[0], $array[1]);
    }

    static public function array_attr($arr) {
        return arr::map_implode(";", function($key, $value) {return "$key:$value";}, array_keys($arr), $arr);
    }
}

?>
