	function linkfield($field, $value) {
		if($this->fields[$field]['link_type'] && $this->fields[$field]['insert_type'] == 'multiple_id') {
			if(!is_array($value) || empty($value)) return false;
			array_shift($value);
			$value = implode("|",$value);
			return $value;
		} else {
			if(!empty($value)) return $value;
		}
	}
