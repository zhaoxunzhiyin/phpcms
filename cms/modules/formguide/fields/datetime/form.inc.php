	function datetime($field, $value, $fieldinfo) {
		extract(string2array($fieldinfo['setting']));
		$isdatetime = 0;
		if($fieldtype=='int') {
			if($format) {
				$value = dr_date($value, 'Y-m-d H:i:s');
				$isdatetime = 1;
			} else {
				$value = dr_date($value, 'Y-m-d');
				$isdatetime = 0;
			}
			if(!$value) $value = $format ? dr_date(SYS_TIME, 'Y-m-d H:i:s') : dr_date(SYS_TIME, 'Y-m-d');
		} elseif($fieldtype=='varchar') {
			if($format2) {
				$isdatetime = 2;
			} else {
				$isdatetime = 3;
			}
			if(!$value) $value = $format2 ? dr_date(SYS_TIME, 'H:i:s') : dr_date(SYS_TIME, 'H:i');
		}
		return form::date("info[$field]",$value,$isdatetime,1,'true',0,$this->modelid,1,$is_left,$color,$width);
	}
