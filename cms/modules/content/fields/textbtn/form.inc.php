	function textbtn($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		// 风格
		$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
		// 按钮颜色
		$color = $color ? $color : 'default';
		// 函数
		$func = $func ? $func : 'dr_diy_func';
		if (dr_is_empty($value)) $value = $defaultvalue;
		$errortips = $this->fields[$field]['errortips'];
		if($errortips || $minlength) $this->formValidator .= '$("#'.$field.'").formValidator({onshow:"",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		return '
		 <div class="input-group"'.$style.'>
				<input class="form-control'.(isset($css) && $css ? ' '.$css : '').'" type="text" name="info['.$field.']" id="dr_'.$field.'" value="'.$value.'" />
				<span class="input-group-btn">
					<a class="btn btn-success " style="border-color:'.$color.';background-color:'.$color.'" href="javascript:'.$func.'(\''.$field.'\');"><i class="'.dr_icon($icon).'" /></i> '.$icon_name.'</a>
				</span>
			</div>
		';
	}
