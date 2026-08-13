	function keyword($field, $value, $fieldinfo) {
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		extract($fieldinfo);
		return "<input type='text' name='info[$field]' id='$field' value='$value' class='form-control".(isset($css) && $css ? ' '.$css : '')."' style='width:".$width.(is_numeric($width) ? "px" : "").";' {$formattribute}>";
	}
