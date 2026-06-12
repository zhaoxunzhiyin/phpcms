	function images($field, $value) {
		$value = $this->input->post($field);
		$array = $temp = array();
		if ($value && is_array($value) && $value['id']) {
			foreach ($value['id'] as $id => $aid) {
				$temp['file'] = $aid ? $aid : (string)$value['file'][$id];
				$temp['title'] = trim((string)$value['title'][$id]);
				$temp['description'] = $value['description'][$id] ? trim($value['description'][$id]) : '';
				$array[$id] = $temp;
			}
		}
		return dr_array2string($array);
	}
