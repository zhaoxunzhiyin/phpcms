	function textselect($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		// 风格
		$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
		if (dr_is_empty($value)) $value = $defaultvalue;
		$errortips = $this->fields[$field]['errortips'];
		if($errortips || $minlength) $this->formValidator .= '$("#'.$field.'").formValidator({onshow:"",onfocus:"'.$errortips.'"}).inputValidator({min:1,onerror:"'.$errortips.'"});';
		$str = load_css(JS_PATH.'jquery.editable-select/jquery.editable-select.min.css');
		$str .= load_js(JS_PATH.'jquery.editable-select/jquery.editable-select.min.js');
		return $str.'
		<select id="editable-select-'.$field.'" class="form-control es-input'.(isset($css) && $css ? ' '.$css : '').'" name="info['.$field.']" id="dr_'.$field.'" value="'.$value.'"'.$style.'></select>
		<script type="text/javascript">
		$(function () {
            var data = '.json_encode(dr_format_option_array($options)).';
            $.each(data, function (i, r) {
                $("#editable-select-'.$field.'").append(\'<option>\' + r + \'</option>\');
            });
            $(\'#editable-select-'.$field.'\').editableSelect({
                effects: \'slide\',//点击的时候，下拉框的过渡效果  有default，slide，fade三个值，默认是default
                filter: false,//选择option以后，是否过滤  默认 true
                duration: \'fast\',//下拉选项框展示的过度动画速度
            });
        });
        </script>
		';
	}
