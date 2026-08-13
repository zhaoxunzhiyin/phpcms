	function groupid($field, $value) {
		$datas = '';
		if(!empty($this->input->post($field)) && is_array($this->input->post($field))) {
			$datas = implode(',',$this->input->post($field));
		}
		return $datas;
	}
