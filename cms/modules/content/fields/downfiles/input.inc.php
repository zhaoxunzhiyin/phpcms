	function downfiles($field, $value) {
		$files = $this->input->post($field.'_fileurl');
		$files_alt = $this->input->post($field.'_filename');
		$array = $temp = array();
		if(!empty($files)) {
			foreach($files as $key=>$file) {
					$temp['fileurl'] = $file;
					$temp['filename'] = $files_alt[$key];
					$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
