	function color($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($setting['width'] ? $setting['width'] : '100%');
		// 风格
		$style = ' style="width:'.$width.(is_numeric($width) ? 'px' : '').';"';
		if (dr_is_empty($value)) $value = $defaultvalue;

		// 加载js
		$str = load_css(JS_PATH.'jquery-minicolors/jquery.minicolors.css');
		$str .= load_js(JS_PATH.'jquery-minicolors/jquery.minicolors.min.js');

		$default = '';
		if ($setting['fieldname'] && $value) {
			$default = '$("#'.$setting['fieldname'].'").css("color", "'.$value.'");';
		}

		$str .= '<script type="text/javascript">
		$(function(){
			$("#dr_'.$field.'").minicolors({
				control: $("#dr_'.$field.'").attr("data-control") || "hue",
				defaultValue: $("#dr_'.$field.'").attr("data-defaultValue") || "",
				inline: "true" === $("#dr_'.$field.'").attr("data-inline"),
				letterCase: $("#dr_'.$field.'").attr("data-letterCase") || "lowercase",
				opacity: $("#dr_'.$field.'").attr("data-opacity"),
				position: $("#dr_'.$field.'").attr("data-position") || "bottom left",
				change: function(t, o) {
					t && (o && (t += ", " + o), "object" == typeof console && console.log(t));
					'.($setting['fieldname'] ? '$("#'.$setting['fieldname'].'").css("color", $("#dr_'.$field.'").val());' : '').'
				},
				theme: "bootstrap"
			});
			'.$default.'
		});
		</script>';
		return '<input type="text" class="form-control color'.(isset($css) && $css ? ' '.$css : '').'" name="info['.$field.']" id="dr_'.$field.'"'.$style.' value="'.$value.'" >'.$str;
	}
