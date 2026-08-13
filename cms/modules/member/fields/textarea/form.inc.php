	function textarea($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		extract($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		// 表单高度设置
		$height = $height ? $height : '100';
		if (dr_is_empty($value)) $value = $defaultvalue;
		$allow_empty = 'empty:true,';
		if($minlength || $pattern) $allow_empty = '';
		if($errortips && $this->fields[$field]['isbase']) $this->formValidator .= '$("#'.$field.'").formValidator({'.$allow_empty.'onshow:"'.$errortips.'",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		return "<textarea name='info[{$field}]' id='$field' class='form-control".(isset($css) && $css ? ' '.$css : '')."' style='width:{$width}".(is_numeric($width) ? 'px' : '').";height:{$height}px;' $formattribute>".$value."</textarea>";
	}
