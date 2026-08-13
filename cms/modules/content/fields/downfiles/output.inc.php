	function downfiles($field, $value) {
		extract(string2array($this->fields[$field]['setting']));
		$list_str = array();
		$file_list = dr_get_files($value);
		if(is_array($file_list)) {
			foreach($file_list as $_k=>$_v) {	
				if($_v['url']){
					$filename = $_v['title'] ? $_v['title'] : L('click_to_down');
					if($downloadlink) {
						$a_k = urlencode(sys_auth("i=$this->id&s=&m=1&f=".$_v['url']."&d=$downloadtype&modelid=$this->modelid&catid=$this->catid", 'ENCODE', md5(PC_PATH.'down').SYS_KEY));
						$list_str[] = "<a href='".APP_PATH."index.php?m=content&c=down&a_k={$a_k}' target='_blank'>{$filename}</a>";
					} else {
						$list_str[] = "<a href='".$_v['url']."' target='_blank'>{$filename}</a>";
					}
				}
			}
		}
		return $list_str;
	}
