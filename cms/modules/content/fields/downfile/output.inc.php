	function downfile($field, $value) {
		extract(string2array($this->fields[$field]['setting']));
		$list_str = array();
		if($value){
			$value_arr = explode('|',$value);
			$fileinfo = get_attachment($value_arr['0']);
			$fileurl = dr_get_file($value_arr['0']);
			$remote = get_cache('attachment', $fileinfo['remote']);
			if ($remote) {
				$fileurl = $remote['url'] ? str_replace($remote['url'], '/', $fileurl) : str_replace(SYS_UPLOAD_URL, '/', $fileurl);
			} else {
				$fileurl = str_replace(SYS_UPLOAD_URL, '/', $fileurl);
			}
			if($fileurl) {
				$sel_server = $value_arr['1'] ? explode(',',$value_arr['1']) : '';
				$server_list = getcache('downservers','commons');
				if(is_array($server_list)) {
					foreach($server_list as $_k=>$_v) {
						if($value && is_array($sel_server) && in_array($_k,$sel_server)) {
							$downloadurl = $_v['siteurl'].$fileurl;
							if($downloadlink) {
								$a_k = urlencode(sys_auth("i=$this->id&s=".$_v['siteurl']."&m=1&f=$fileurl&d=$downloadtype&modelid=$this->modelid&catid=$this->catid", 'ENCODE', md5(PC_PATH.'down').SYS_KEY));
								$list_str[] = "<a href='".APP_PATH."index.php?m=content&c=down&a_k={$a_k}' target='_blank'>{$_v['sitename']}</a>";
							} else {
								$list_str[] = "<a href='{$downloadurl}' target='_blank'>{$_v['sitename']}</a>";
							}
						}
					}
				}	
				return $list_str;
			}
		} 
	}
