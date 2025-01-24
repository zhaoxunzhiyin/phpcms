	function copyfrom($field, $value) {
		$field_data = $field.'_data';
		if($this->input->post($field_data)) {
			$value .= '|'.safe_replace($this->input->post($field_data));
		}
		return $value;
	}
