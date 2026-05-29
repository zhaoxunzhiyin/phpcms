	function datetime($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		extract($setting);
		if($fieldtype=='int') {
			if($format) {
				$value = dr_date($value, 'Y-m-d H:i:s');
			} else {
				$value = dr_date($value, 'Y-m-d');
			}
			if(!$value) $value = $format ? dr_date(SYS_TIME, 'Y-m-d H:i:s') : dr_date(SYS_TIME, 'Y-m-d');
		} elseif($fieldtype=='varchar') {
			if(!$value) $value = $format2 ? dr_date(SYS_TIME, 'H:i:s') : dr_date(SYS_TIME, 'H:i');
		}
		return $value;
	}
