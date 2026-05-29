	function linkages($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$str = '';
		$values = dr_string2array($value);
		if ($values) {
			foreach ($values as $value) {
				$str.= '<div class="'.($setting['css'] ? $setting['css'] : 'form-control-static').'">'.dr_linkagepos($setting['linkage'], $value, $setting['space']).'</div>';
			}
		}
		return $str;
	}
