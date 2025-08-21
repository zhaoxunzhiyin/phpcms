	function tabletexts($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$columns = explode("\n",$this->fields[$field]['column']);
	 
		$array = array();
		if(!empty($this->input->post($field.'_1'))) {
			foreach($this->input->post($field.'_1') as $key=>$val) {
				for ($x=1; $x<=dr_count($columns); $x++) {
					$array[$key][$field.'_'.$x] = $this->input->post($field.'_'.$x)[$key];
				}
			}
		}
		$array = array2string($array);
		return $array;
	}