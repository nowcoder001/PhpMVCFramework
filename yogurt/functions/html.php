<?php
/*
 * html
 */
class html extends xml {
    static function tohtml($arr = []) {
        return "<!DOCTYPE html>\n".self::toxml( $arr );
    }

    /* static function html($inner = "") {
     *     return self::toxml(['html', [], $inner]);
     * }
     */
    
    static function head($inner = "") {
        return self::toxml(['head', [], $inner]);
    }

    static function body($inner = "") {
        return self::toxml(['body', [], $inner]);
    }

    static function title($inner = "") {
        return self::toxml(['title', [], $inner]);
    }
    
    static function nbsp($repeat = 1) {
        return str_repeat("&nbsp;", $repeat);
    }

    static function script() {
        return self::node('script', ['type', 'text/javascript'], implode("\n", func_get_args()));
    }

    static function meta($key, $content, $name = 'name') {
        return self::node('meta', [
            [$name, $key],
            ['content', $content]
        ], null, true);
    }

    static function load_css($href) {
        return self::node('link', [
            ['rel', 'stylesheet'],
            ['type', 'text/css'],
            ['href', $href]
        ], null, true);
    }

    static function load_js($src) {
        return self::node('script', [
            ['type', 'text/javascript'],
            ['src', $src]
        ], null);
    }
}
?>
