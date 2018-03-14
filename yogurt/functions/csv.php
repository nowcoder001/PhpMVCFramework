<?php
class csv {
    static function tocsv($matrix) {
        return arr::join("\n",
                       arr::map(
                           function($row) {
                               return arr::join(',', arr::map([__CLASS__, 'iconv'], $row));
                           },
                           $matrix));
    }

    static function iconv($string, $from = 'utf-8', $to = 'gbk') {
        return iconv($from, $to, $string);
    }

    static function read($filename) {
        return $filename;
    }

    static function parse($string) {
        return arr::map(function($row) {
            return arr::filter('strlen', str::split(',', trim($row)));
        }, arr::filter('strlen', str::split("\n", $string)));
    }

    function export($data,$filename) {
		if(!$filename)
			$filename = __CLASS__ . date('YmdHis');
		header("Content-type:text/csv");
		header("Content-Disposition:attachment;filename=".$filename.'.csv');
		header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
		header('Expires:0'); 
		header('Pragma:public');
		echo $data;
	}
}
?>
