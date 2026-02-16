<?php
class format {
	/**
	 * 日期格式化
	 * 
	 * @param $timestamp
	 * @param $showtime
	 */
	public static function date($timestamp, $showtime = 0) {
		$times = intval($timestamp);
		if(!$times) return true;
		if(SYS_LANGUAGE == 'zh-cn') {
			$str = $showtime ? dr_date($times, 'Y-m-d H:i:s') : dr_date($times, 'Y-m-d');
		} else {
			$str = $showtime ? dr_date($times, 'm/d/Y H:i:s') : dr_date($times, 'm/d/Y');
		}
		return $str;
	}
	
	/**
	 * 获取当前星期
	 * 
	 * @param $timestamp
	 */
	public static function week($timestamp) {
		$times = intval($timestamp);
		if(!$times) return true;
		$weekarray = array(L('Sunday'),L('Monday'),L('Tuesday'),L('Wednesday'),L('Thursday'),L('Friday'),L('Saturday')); 
		return $weekarray[dr_date($timestamp, "w")]; 
	}
}
?>