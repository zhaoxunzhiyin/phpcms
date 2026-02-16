	function textarea($field, $value) {
		if(!$this->fields[$field]['enablehtml']) $value = clearhtml($value);
		return $value;
	}
