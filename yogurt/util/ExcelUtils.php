<?php
/*
 *  处理excel输出的工具类
 */
class ExcelUtils 
{
	/**
	*将数组转换成csv格式的字符串，自动过滤','
	*/
	public static function getCsvStr($rows=array(),$keys=array())
	{
		global $BATTLE;
		global $BATTLE_RESULT;
		global $SOURCE_TYPE_ARRAY;
        global $MODULE_ARRAY;
        
		$str = "";

		foreach($rows as $row)
		{
			$first = true;
			foreach($keys as $key)
			{
				if($first)
				{
					$first = false;
				}
				else{
					$str .= ",";
				}
				
				if($key == "player_name" || $key == "dun_name" || $key == "item_name" || $key == "name" || $key == "goods_name")
				{
					$tmp = iconv('utf-8','gb2312',$row[$key]);
					$tmp = str_replace(",", "", $tmp);
					$str .= $tmp;
				}
				elseif($key == "event_time" || $key == "create_time" || $key == "time" || $key == "createTime" || $key == "lastOnTime" || $key == "lastDownTime")
				{
                    //$str .= $row[$key];
					$str .= $row[$key] > 0?date('Y-m-d H:i:s',$row[$key]/1000):"";
				}
				elseif($key == "battle_type")
				{
					$tmp = iconv('utf-8','gb2312',$BATTLE[$row[$key]]);
					$str .= $tmp;
				}
				elseif($key == "result")
				{
					$tmp = iconv('utf-8','gb2312',$BATTLE_RESULT[$row[$key]]);
					$str .= $tmp;
				}
				elseif($key == "source_type" || $key == 'sourceType')
				{
					$tmp = iconv('utf-8','gb2312', $SOURCE_TYPE_ARRAY[$row[$key]]);
					$str .= $tmp;
				}
				elseif($key == "state")
				{
					$str .= $row[$key] == 1 ? '成功':'失败';
				}
                elseif($key == "module_id")
                {
                    $str .= iconv('utf-8', 'gb2312', $MODULE_ARRAY[$row[$key]]);
                    //$str .= "module";
                }
                elseif($key == "gangName")
                {
                    $str .= iconv('utf-8', 'gb2312', $row[$key]);
                }
				else
				{
					$tmp = str_replace(",", "", $row[$key]);
					$str .= $tmp;
				}
			}
			$str .= "\n";
		}
		return $str;
	}
}
?>
