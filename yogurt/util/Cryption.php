<?php
/**
 * file utils class of yogurt framework.
 * 
 * PHP versions 5
 *
 * Yogurt : MVC Development Framework with PHP<http://www.yogurt-framework.com/>
 * Copyright (c)2009-2010, rick <158672319@qq.com>
 * Licensed under The GNU License
 *
 * @filesource		utils/Cryption.php
 * @copyright		Copyright (c)2009-2010, rick <158672319@qq.com> 2009-8-21
 * @link			http://www.yogurtframework.com/download/
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
class Cryption {

   public static function encrypt($data,$key)
    {
        $key    =   md5($key);
        $data   =   base64_encode($data);
        $x=0;
		$len = strlen($data);
		$l = strlen($key);
        for ($i=0;$i< $len;$i++)
        {
            if ($x== $l) $x=0;
            $char   .=substr($key,$x,1);
            $x++;
        }
        for ($i=0;$i< $len;$i++)
        {
            $str    .=chr(ord(substr($data,$i,1))+(ord(substr($char,$i,1)))%256);
        }
        return $str;
    }
    
      public static function decrypt($data,$key)
    {
        $key    =   md5($key);
        $x=0;
		$len = strlen($data);
		$l = strlen($key);
        for ($i=0;$i< $len;$i++)
        {
            if ($x== $l) $x=0;
            $char   .=substr($key,$x,1);
            $x++;
        }
        for ($i=0;$i< $len;$i++)
        {
            if (ord(substr($data,$i,1))<ord(substr($char,$i,1)))
            {
                $str    .=chr((ord(substr($data,$i,1))+256)-ord(substr($char,$i,1)));
            }
            else
            {
                $str    .=chr(ord(substr($data,$i,1))-ord(substr($char,$i,1)));
            }
        }
        return base64_decode($str);
    }
}
?>
