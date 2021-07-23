	function images($field, $value) {
		//取得图片列表
		$pictures = $this->input->post($field.'_url');
		//取得图片说明
		$pictures_alt = $this->input->post($field.'_alt') ? $this->input->post($field.'_alt') : array();
		$array = $temp = array();
		if(!empty($pictures)) {
			foreach($pictures as $key=>$pic) {
				$temp['url'] = $pic;
				$temp['alt'] = str_replace(array('"',"'"),'`',$pictures_alt[$key]);
				$array[$key] = $temp;
			}
		}
		$array = array2string($array);
		return $array;
	}
