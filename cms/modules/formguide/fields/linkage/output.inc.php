	function linkage($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		return dr_linkagepos($setting['linkage'], $value, $setting['space']);
	}
