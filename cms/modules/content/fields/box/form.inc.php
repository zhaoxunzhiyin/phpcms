	function box($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		if($value=='') $value = $this->fields[$field]['defaultvalue'];
		$options = explode("\n",$this->fields[$field]['options']);
		foreach($options as $_k) {
			$v = explode("|",$_k);
			$k = trim($v[1]);
			$option[$k] = $v[0];
		}
		switch($this->fields[$field]['boxtype']) {
			case 'radio':
				$string = form::radio($option,$value,"name='info[$field]' $fieldinfo[formattribute]",$setting['width'],$field);
			break;

			case 'checkbox':
				if ($value) {
					$values = dr_string2array($value);
				}
				if (!is_array($values)) {
					$value = strpos($value, ',') !== false ? explode(',', $value) : array($value);
				} else {
					$value = $values;
				}
				$string = form::checkbox($option,$value,"name='info[$field][]' $fieldinfo[formattribute]",'',$setting['width'],$field);
			break;

			case 'select':
				$string = form::select($option,$value,"name='info[$field]' id='$field' $fieldinfo[formattribute]");
			break;

			case 'multiple':
				if ($value) {
					$values = dr_string2array($value);
				}
				if (!is_array($values)) {
					$value = strpos($value, ',') !== false ? explode(',', $value) : array($value);
				} else {
					$value = $values;
				}
				$string = load_script('var bs_selectAllText = \'全选\';var bs_deselectAllText = \'全删\';var bs_noneSelectedText = \'没有选择\'; var bs_noneResultsText = \'没有找到 {0}\';');
				$string .= load_css(JS_PATH.'bootstrap-select/css/bootstrap-select.min.css');
				$string .= load_js(JS_PATH.'bootstrap-select/js/bootstrap-select.min.js');
				$string .= load_script('jQuery(document).ready(function(){$(\'.bs-select\').selectpicker();});');
				$string .= '';
				$string .= form::select($option,$value,"name='info[$field][]' id='$field' class='bs-select form-control' data-actions-box='true' multiple='multiple' $fieldinfo[formattribute]");
			break;
		}
		return $string;
	}
