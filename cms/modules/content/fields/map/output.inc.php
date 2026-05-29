	function map($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$width = is_mobile() ? '100%' : ($setting['width'] ? $setting['width'] : 400);
		$height = $setting['height'] ? $setting['height'] : 200;
		return dr_baidu_map($value, (int)$level, $width, $height, '', 'form-control-static');
	}
