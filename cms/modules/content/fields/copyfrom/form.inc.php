	function copyfrom($field, $value, $fieldinfo) {
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		$value_data = '';
		if($value && strpos($value,'|')!==false) {
			$arr = explode('|',$value);
			$value = $arr[0];
			$value_data = $arr[1];
		}
		$copyfrom_array = getcache('copyfrom','admin');
		$copyfrom_datas = array(L('copyfrom_tips'));
		if(!empty($copyfrom_array)) {
			foreach($copyfrom_array as $_k=>$_v) {
				if($this->siteid==$_v['siteid']) $copyfrom_datas[$_k] = $_v['sitename'];
			}
		}
		return "<label><input type='text' name='info[$field]' value='$value' style='width:".$width.(is_numeric($width) ? "px" : "").";' class='form-control'></label> ".form::select($copyfrom_datas,$value_data,"name='{$field}_data' ");
	}
