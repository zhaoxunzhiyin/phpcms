	function downfile($field, $value, $fieldinfo) {
		$list_str = $str = '';
		extract(string2array($fieldinfo['setting']));
		if($value){
			$value_arr = explode('|',$value);
			$value = $value_arr['0'];
			$sel_server = $value_arr['1'] ? explode(',',$value_arr['1']) : '';
			$edit = 1;
		} else {
			$edit = 0;
		}
		$server_list = getcache('downservers','commons');
		if(is_array($server_list)) {
			$list_str .= "<div class='mt-checkbox-inline'>";
			foreach($server_list as $_k=>$_v) {
				if (in_array($_v['siteid'],array(0,$fieldinfo['siteid']))) {
					$checked = $edit ? ((is_array($sel_server) && in_array($_k,$sel_server)) ? ' checked' : '') : ' checked';
					$list_str .= "<label id='downfile{$_k}' class='mt-checkbox mt-checkbox-outline'><input type='checkbox' value='{$_k}' name='{$field}_servers[]' {$checked}>   {$_v['sitename']} <span></span> </label>";
				}
			}
			$list_str .= "</div>";
		}
	
		$string = '
		<fieldset class="blue pad-10">
        <legend>'.L('mirror_server_list').'</legend>';
		$string .= $list_str;
		$string .= '</fieldset>
		<div class="bk10"></div>
		';	
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$authkey = upload_key($this->input->get('siteid').",1,$upload_allowext,$upload_maxsize,$isselectimage,,,,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $this->input->get('siteid'),
			'file_upload_limit' => 1,
			'file_types_post' => $upload_allowext,
			'size' => $upload_maxsize,
			'allowupload' => $isselectimage,
			'thumb_width' => '',
			'thumb_height' => '',
			'watermark_enable' => '',
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		$string .= $str."<div class=\"row fileupload-buttonbar\" id=\"fileupload_{$field}\"><div class=\"col-lg-12\"><label><input type='hidden' name='info[$field]' id='$field' value='$value'><button type=\"button\" onclick=\"javascript:h5upload('".SELF."', '{$field}_downfield', '".L('attachment_upload')."','{$field}','submit_files','{$p}','content','$this->catid','{$authkey}',".SYS_EDITOR.")\" class=\"btn green btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('upload_soft')."</button></label> <label><button onclick=\"fileupload_file_remove('{$field}');\" type=\"button\" class=\"btn red btn-sm {$field}-delete\"".($value ? "" : " style=\"display:none\"")."><i class=\"fa fa-trash\"></i><span> ".L('delete')." </span></button></label></div></div><div id='dr_".$field."_files_row' class='file_row_html files_row'>".($value ? "<div class=\"files_row_preview preview\"><a href=\"javascript:preview('".dr_get_file($value)."');\"><img src=\"".(dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value)))."\"></a></div>" : "")."</div>";
		return $string;
	}
