	function posid($field, $value) {
		$number = dr_count($value);
		$value = $number==1 ? 0 : 1;
		return $value;
	}
