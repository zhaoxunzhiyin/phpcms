	function number($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		if (dr_is_empty($value)) $value = $defaultvalue;
		return "<label><input type='text' name='info[$field]' id='$field' value='$value' class='form-control".(isset($css) && $css ? ' '.$css : '')."' style='width:".$width.(is_numeric($width) ? "px" : "").";' {$formattribute}></label>";
	}
