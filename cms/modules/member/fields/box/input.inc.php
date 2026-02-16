	function box($field, $value) {
		if($this->fields[$field]['boxtype'] == 'checkbox') {
			if(!is_array($value) || empty($value)) return false;
			return dr_array2string($value);
		} elseif($this->fields[$field]['boxtype'] == 'multiple') {
			if(is_array($value) && dr_count($value)>0) {
				return dr_array2string($value);
			}
		} else {
			return $value;
		}
	}
