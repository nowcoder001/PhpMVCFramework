<?php
class linear {
    static function transpose($array) {
        $t_array = [];
        foreach ($array as $col_index => $row) {
            foreach ($row as $row_index => $cell) {
                $t_array[$row_index][$col_index] = $cell;
            }
        }
        return $t_array;
    }

    static function cartesian_product($x, $y) {
        $product = [];
        foreach($x as $xi) {
            foreach($y as $yi) {
                $product[] = [$xi, $yi];
            }
        }
        return $product;
    }
}
?>
