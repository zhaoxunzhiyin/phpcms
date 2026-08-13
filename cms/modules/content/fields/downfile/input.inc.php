	function downfile($field, $value) {
		//取得镜像站点列表
		$result = '';
		$server_list = dr_count($this->input->post($field.'_servers')) > 0 ? implode(',' ,$this->input->post($field.'_servers')) : '';
		$result = $value.'|'.$server_list;
		return $result;
	}
