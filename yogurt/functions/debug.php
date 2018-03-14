<?php
class debug {
    static public function dump() {
        echo "<pre>";
        arr::call("var_dump", func_get_args());
        echo "</pre>";
    }
}
?>
