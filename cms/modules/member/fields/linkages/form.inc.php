	function linkages($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		$linkage = $setting['linkage'];
		$defaultvalue = $setting['defaultvalue'];
		$width = $setting['width'];
		$ck_child = $setting['ck_child'];
		$limit = intval($setting['limit']);
		if (dr_is_empty($value)) $value = $defaultvalue;
		return menu_linkage($linkage,$field,$value,$ck_child,1,$limit,$width);
	}
