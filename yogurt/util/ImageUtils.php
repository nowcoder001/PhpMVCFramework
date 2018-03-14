<?php
/**
 *  Yogurt : MVC Development Framework with PHP<http://www.yogurtframework.com/>
 *
 * @author          rick <158672319@qq.com>
 * @copyright		Copyright (c)2009-2013  
 * @link			http://www.yogurtframework.com
 * @license         http://www.yogurtframework.com/license/
 */
/**
 * Captcha class. yogurt framework.
 * @filesource		yogurt/utils/ImageUtils.php
 * @since			Yogurt v 0.9
 * @version			$3.0
 */
if (!function_exists('gd_info')) {
               throw new Exception('GD is not enable!');
}

class ImageUtils {
    private $width = 180;
	private $height = 60;
	private $noise = true;
	private $text = '';
	
	/**
	 * set width
	 * @param int $width
	 */
	public function setWidth($width = 0) {
		$this->width = $width;
	}
	
	/**
	 * get width
	 * @return int
	 */
	public function getWidth() {
		return $this->width;
	}
	
	/**
	 * set height
	 * @param int $height
	 */
	public function setHeight($height = 0) {
		$this->height = $height;
	}
	
	/**
	 * get height
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}
	
	/**
	 * get noise
	 * @return boolean
	 */
	public function getNoise() {
		return $this->noise;
	}
	
	/**
	 * set noice
	 * @param boolean $noise
	 */
	public function setNoice($noise = true) {
		$this->noise = $noise;
	}
	
	/**
	 * show image
	 * @param string $code
	 * @param int $seed
	 */
	public function captcha($code, $seed = 0) {
		$stats = gd_info();
		if (0 == $seed) {
			list($usec, $sec) = explode(' ', microtime());
    		$seed = (float) $sec + ((float) $usec * 100000);
		}
		
		//var_dump($stats);
		$bundled = (substr($stats['GD Version'], 0, 7) === 'bundled') ? true : false;
        $version=array();
		preg_match('/[\\d.]+/', $stats['GD Version'], $version);
		$gd_version = (version_compare($version[0], '2.0.1', '>=')) ? 2 : 1;

		// create the image, stay compat with older versions of GD
		if ($gd_version === 2) {
			$func1 = 'imagecreatetruecolor';
			$func2 = 'imagecolorallocate';
		} else {
			$func1 = 'imagecreate';
			$func2 = 'imagecolorclosest';
		}
		
		$image = $func1($this->width, $this->height);

		if ($bundled) {
			imageantialias($image, true);
		}

		// seed the random generator
		mt_srand($seed);

		// set background color
		$back =  imagecolorallocate($image,255,255,255 /*mt_rand(224, 255), mt_rand(224, 255), mt_rand(224, 255)*/);
		imagefilledrectangle($image, 0, 0, $this->width, $this->height, $back);
		
		// allocates the 216 websafe color palette to the image
		if ($gd_version === 1) {
			for ($r = 0; $r <= 255; $r += 51) {
				for ($g = 0; $g <= 255; $g += 51) {
					for ($b = 0; $b <= 255; $b += 51) {
						imagecolorallocate($image, $r, $g, $b);
					}
				}
			}
		}
		
		// fill with noise or grid
		if ($this->noise) {
			$chars_allowed = array_merge(range('1', '9'), range('A', 'Z'));
			// random characters in background with random position, angle, color
			for ($i = 0 ; $i < 72; $i++) {
				$size	= mt_rand(8, 23);
				$angle	= mt_rand(0, 360);
				$x		= mt_rand(0, 360);
				$y		= mt_rand(0, (int)($this->height - ($size / 5)));
				$color	= $func2($image, mt_rand(160, 224), mt_rand(160, 224), mt_rand(160, 224));
				$text	= $chars_allowed[mt_rand(0, sizeof($chars_allowed) - 1)];
				imagettftext($image, $size, $angle, $x, $y, $color, $this->get_font(), $text);
			}
			unset($chars_allowed);
		} else 	{
			// generate grid
			for ($i = 0; $i < $this->width; $i += 13) {
				$color	= $func2($image, mt_rand(160, 224), mt_rand(160, 224), mt_rand(160, 224));
				imageline($image, $i, 0, $i, $this->height, $color);
			}

			for ($i = 0; $i < $this->height; $i += 11) {
				$color	= $func2($image, mt_rand(160, 224), mt_rand(160, 224), mt_rand(160, 224));
				imageline($image, 0, $i, $this->width, $i, $color);
			}
		}
		
		$len = strlen($code);

		for ($i = 0, $x = mt_rand(/*20,40*/2, 4); $i < $len; $i++) {
			$text	= strtoupper($code[$i]);
			$angle	= mt_rand(-30, 30);
			$size	= mt_rand(/*20, 40*/12,16);		 
			//$y		= mt_rand((int)($size * 1.5), (int)($this->height - ($size / 7)));
			$y		=  ($this->height - $size)/2
                                 + $size;
			$color	= $func2($image, mt_rand(0, 127), mt_rand(0, 127), mt_rand(0, 127));
			$shadow = $func2($image, mt_rand(127, 254), mt_rand(127, 254), mt_rand(127, 254));
			$font = $this->get_font();
            
			imagettftext($image, $size, $angle, $x + (int)($size / 15), $y, $shadow, $font, $text);
			imagettftext($image, $size, $angle, $x, $y - (int)($size / 15), $color, $font, $text);

			$x += $size + 4;
		}
   		// var_dump($image);die;
		// Output image
	
		header('Content-Type: image/png');
		header('Cache-control: no-cache, no-store');
		imagepng($image);
		imagedestroy($image);
		
		
	}
	
	  /**
	     * 创建图片的缩略图 
	     * 1 取得GD版本 2 取图片的信息 3 设置图片大小 4 cteate图片源 5 填充颜色 6 拷贝图像并调整大小
	     * 7 图像输出  8 删除图片源
	     * @access  public
	     * @param   string      $img    原始图片的路径
	     * @param   int         $thumb_width  缩略图宽度
	     * @param   int         $thumb_height 缩略图高度
	     * @return  mix         如果成功返回缩略图的路径，失败则返回失败的代码
	     */
	public function thumb($img, $thumb_width = 0, $thumb_height = 0) {
		$gd=2;
        //$gd = $this->getGdVersion(); 
		// 获得原始文件的信息
		$org_info= $this->getImageInfo($img);		

		/* 检查缩略图宽度和高度是否合法 */
		/* 返回原图 */
		if ($thumb_width == 0 && $thumb_height == 0) {
			return str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', realpath($img)));
		}
		/* 原始图片以及缩略图的尺寸比例 */
		$scale_org = $org_info['w'] / $org_info['h'];
		/* 处理只有缩略图宽和高有一个为0的情况，这时背景和缩略图一样大 */
		if ($thumb_width == 0) {
			$thumb_width = $thumb_height * $scale_org;
		}
		if ($thumb_height == 0) {
			$thumb_height = $thumb_width / $scale_org;
		}
		
		/* 按照原始图片的尺寸比例缩放后的尺寸 */
		if ($scale_org > 1) {
			/* 原始图片比较宽，这时以宽度为准 */
			$lessen_width = $thumb_width;
			$lessen_height = $thumb_width / $scale_org;
		}else {
			/* 原始图片比较高，则以高度为准 */
			$lessen_width = $thumb_height * $scale_org;
			$lessen_height = $thumb_height;
		}

		$dst_x = ($thumb_width - $lessen_width) / 2;
		$dst_y = ($thumb_height - $lessen_height) / 2;
		
		$func = 'imagecreatefrom'.$org_info['func'];		
		$img_org =$func($img);
        /* 创建缩略图的标志符 */		
		if ($gd == 2) {
			$img_thumb = imagecreatetruecolor($thumb_width, $thumb_height);
		}else {
			$img_thumb = imagecreate($thumb_width, $thumb_height);
		}
		/* 背景颜色 */
		$clr = imagecolorallocate($img_thumb, 255, 255, 255);
		imagefilledrectangle($img_thumb, 0, 0, $thumb_width, $thumb_height, $clr);	
		/* 将原始图片进行缩放处理 */
		if ($gd == 2) {
			imagecopyresampled($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info['w'], $org_info['h']);
		}else {
			imagecopyresized($img_thumb, $img_org, $dst_x, $dst_y, 0, 0, $lessen_width, $lessen_height, $org_info['w'], $org_info['h']);
		}		
		
		$files=pathinfo($img);
		$target=$files['dirname'].'/'.$files['filename']."_thumb.".$files['extension'];
     
		/* 生成文件 */	
        $func1='image'.$org_info['func'];
        $func1($img_thumb, $target);	
		imagedestroy($img_thumb);
		imagedestroy($img_org);
		 
		//文件是否生成	
		return file_exists($target);
	}
	
	/*
	*加水印 ,支持文本与图像两种方式
	*$fileName 源文件名
	*$waterMarkFileName 水印文件
	*$place 水印位置
	*/
	public function waterMark($img, $waterMark = null) {
			// 文件是否存在
	/*if ((!file_exists($fileName)) || (!is_file($fileName))) {
			echo "<script>";
			echo "javascript:alert('文件不存在')";
			echo "</script>";
			return false;
		}*/
		 
		// 水印文件是否存在
	/*	$waterMarkFileName = (null!=$waterMarkFileName)?$waterMarkFileName:ImageConfig::WATERIMG ;
		$waterMarkFileName =APP_DIR.$waterMarkFileName;
			if (!file_exists($waterMarkFileName)) {
				echo "<script>";
				echo "javascript:alert('水印图不存在!')";
				echo "</script>";
			}*/
		$gd=2;
        //$gd = $this->getGdVersion(); 
		// 获得原始文件的信息
		$org_info= $this->getImageInfo($img);
		$func = 'imagecreatefrom'.$org_info['func'];		
		$sourceHandle =$func($img);
		//$sourceHandle = $this->img_resource($fileName, $sourceInfo[2]);
		$waterMarkType = $this->getWaterMarkType();
		//文本水印
		if ("text" == self :: $waterMarkType) {
			if (preg_match("/([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i", self::$text['color'], $color = array ())) {
				$red = hexdec($color[1]);
				$green = hexdec($color[2]);
				$blue = hexdec($color[3]);
				$wm_text_color = imagecolorallocate($sourceHandle, $red, $green, $blue);
			}
			else {
				$wm_text_color = imagecolorallocate($sourceHandle, 255, 255, 255);
			}
            $sourceInfo= $waterMarkFileName=null;
			$dstArray = $this->getPos($sourceInfo, $waterMarkFileName);
			// print_r($dstArray);die;
			$x = $dstArray["dst_x"];
			$y = $dstArray["dst_y"];

			imagettftext($sourceHandle, self::$text['size'], self::$text['angle'], $x, $y, $wm_text_color, self::$text['font'], self::$text['content']);
		}

		if ("image" == self :: $waterMarkType) {

			$waterMarkInfo = getimagesize($waterMarkFileName);
			$waterMarkHandle = $this->img_resource($waterMarkFileName, $waterMarkInfo[2]);

			$dstArray = $this->getPos($sourceInfo, $waterMarkFileName);
			// print_r($dstArray);die;
			$x = $dstArray["dst_x"];
			$y = $dstArray["dst_y"];

			imagecopymerge($sourceHandle, $waterMarkHandle, $x, $y, 0, 0, $waterMarkInfo[0], $waterMarkInfo[1], $this->alpha);
		}

		// 输出

		$dir = $_SERVER['DOCUMENT_ROOT'].'/'.$this->markFolder.'/'.date("Ym");

		if (null != $this->markFolder) {
			if (!file_exists($this->markFolder)) {
				if (!($this->folder->mkdirr($this->markFolder))) {

					echo "<script>";
					echo "javascript:alert('创建目录失败')";
					echo "</script>";
					return false;
				}
			}
		}

		if (!($this->folder->mkdirr($dir))) {
			/* 创建目录失败 */
			echo "<script>";
			echo "javascript:alert('目录创建失败')";
			echo "</script>";
		}

		$fileName = basename($fileName);
		$fileArray = explode('.', $fileName);
		$last = count($fileArray);
		$len = strlen($fileName) - strlen($fileArray[$last -1]) - 1;
		$newFileName = substr($fileName, 0, $len);

		$target = $dir.'/'.$newFileName."_mark";
		//header("Content-type: image/{$sourceInfo['mime']}");
		switch ($sourceInfo['mime']) {
			case "image/jpeg" || 'image/pjpeg' :
				header("Content-type: image/gif");
				$target .= '.jpg';
				imagejpeg($sourceHandle, $target);
				break;

			case "image/png" || 'image/x-png' :
				$target .= '.png';
				imagepng($sourceHandle, $target);
				break;

			case "image/gif" :
				$target .= '.gif';
				imagegif($sourceHandle, $target);
				break;
		}

		imagedestroy($sourceHandle);
		//imagedestroy($waterMarkHandle);
	}
	
	
	
	public function getImageInfo($img){
		$org_info = getimagesize($img);

		if (!$org_info) {
			echo "<script>";
			echo "javascript:alert('不能获取文件的信息')";
			echo "</script>";
			return false;
		}

         switch ($org_info[2]) {
			case 1 :
				//$img_org = imagecreatefromgif($img);
				$func = 'gif';
				break;
			case 2 :
				//$img_org = imagecreatefromjpeg($img);
				$func = 'jpeg';
				break;
			case 3 :
				//$img_org = imagecreatefrompng($img);
				$func = 'png';
				break;
			default :
				return FALSE;
		}
		return array('w'=>$org_info[0],'h'=>$org_info[1],"func"=>$func);
	}
	
	/**
	 * get allow font
	 */
	private function get_font() {
		return Y_DIR . 'util/fonts/arial.ttf';
	    //return LIB_DIR . 'fonts/arial.ttf';
		/*if (!sizeof($fonts)) {
			 
			$dr = opendir(LIB_DIR . 'fonts');
			if (!$dr)
	            {
 	                trigger_error('Unable to open fonts directory.', E_USER_ERROR);
 	            }
 	            
			while (false !== ($entry = readdir($dr))) {
				if (strtolower(pathinfo($entry, PATHINFO_EXTENSION)) == 'ttf') {					
					$fonts[] = LIB_DIR . 'fonts/' . $entry;
				}
			}
			closedir($dr);
		}*/
        //var_dump($fonts[mt_rand(0, sizeof($fonts) - 1)]);die;      
		//return $fonts[mt_rand(0, sizeof($fonts) - 1)];
	}
	
}

?>