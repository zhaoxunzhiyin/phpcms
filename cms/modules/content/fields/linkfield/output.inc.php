	/*function linkfield($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$get_db = pc_base::load_model("get_model");
		$sel_tit=$setting['select_title']?$setting['select_title']:'*';
		$set_id=$setting['set_id']?$setting['set_id']:'';
		$set_title=$setting['set_title']?$setting['set_title']:'';
		$value = str_replace('|',',',$value);
		$sql = "SELECT ".$sel_tit." FROM `".$setting['table_name']."` WHERE ".$set_id." in(".$value.")";
		$r= $get_db->query($sql);
		$i = 0;
		$data = '';
		while(($s = $get_db->fetch_next()) != false) {
			if ($i==0) {
				$data = $s[$set_title];
			} else {
				$data .= ','.$s[$set_title];
			}
			$i++;
		}
		return $data;
	}*/
	function linkfield($field, $value) {
		return $value;
	}
