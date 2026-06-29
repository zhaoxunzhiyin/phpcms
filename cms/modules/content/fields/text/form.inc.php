	function text($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		if (dr_is_empty($value)) $value = $defaultvalue;
		$type = $ispassword ? 'password' : 'text';
		$errortips = $this->fields[$field]['errortips'];
		if($errortips || $minlength) $this->formValidator .= '$("#'.$field.'").formValidator({onshow:"",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		return '<input type="'.$type.'" name="info['.$field.']" id="'.$field.'" class="form-control'.(isset($css) && $css ? ' '.$css : '').'" style="width:'.$width.(is_numeric($width) ? 'px' : '').';" value="'.$value.'" 	'.$formattribute.'>';
	}
