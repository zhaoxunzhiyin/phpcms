	function image($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		extract($setting);
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$authkey = upload_key($this->input->get('siteid').",1,$upload_allowext,$upload_maxsize,$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce,$chunk");
		$p = dr_authcode(array(
			'siteid' => $this->input->get('siteid'),
			'file_upload_limit' => 1,
			'file_types_post' => $upload_allowext,
			'size' => $upload_maxsize,
			'allowupload' => $isselectimage,
			'thumb_width' => $images_width,
			'thumb_height' => $images_height,
			'watermark_enable' => $watermark,
			'attachment' => $attachment,
			'image_reduce' => $image_reduce,
			'chunk' => $chunk,
		), 'ENCODE');
		if($show_type) {
			$preview_img = $value ? dr_get_file($value) : IMG_PATH.'icon/upload-pic.png';
			return $str."<div class='upload-pic img-wrap'><div class=\"row fileupload-buttonbar\" id=\"fileupload_{$field}\"><div class=\"col-lg-12\"><input type='hidden' name='info[$field]' id='$field' value='$value'>
			<p><a href='javascript:;' onclick=\"javascript:h5upload('".SELF."', '{$field}_images', '".L('attachment_upload')."','{$field}','thumb_images','{$p}','member','','{$authkey}',".SYS_EDITOR.")\">
			<img src='$preview_img' id='{$field}_preview' width='135' height='113' style='cursor:hand' /></a></p><label><button type=\"button\" onclick=\"$('#".$field."_preview').attr('src','".IMG_PATH."icon/upload-pic.png');$('#fileupload_".$field."').find('.".$field."-delete').hide();$('#".$field."').val('');return false;\" class=\"btn red btn-sm ".$field."-delete\"".($value ? "" : " style=\"display:none\"")."> <i class=\"fa fa-trash\"></i> ".L('cancel_the_picture','','content')."</button></label></div></div></div>";
		} else {
			return $str."<div class=\"row fileupload-buttonbar\" id=\"fileupload_{$field}\"><div class=\"col-lg-12\"><label><input type='hidden' name='info[$field]' id='$field' value='$value'><button type=\"button\" $this->no_allowed onclick=\"javascript:h5upload('".SELF."', '{$field}_images', '".L('attachment_upload')."','{$field}','submit_images','{$p}','member','','{$authkey}',".SYS_EDITOR.")\" class=\"btn green btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('image_upload')."</button></label> <label><button onclick=\"fileupload_file_remove('{$field}');\" type=\"button\" class=\"btn red btn-sm {$field}-delete\"".($value ? "" : " style=\"display:none\"")."><i class=\"fa fa-trash\"></i><span> ".L('delete')." </span></button></label></div></div><div id='dr_".$field."_files_row' class='file_row_html files_row'>".($value ? "<div class=\"files_row_preview preview\"><a href=\"javascript:preview('".dr_get_file($value)."');\"><img src=\"".(dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value)))."\"></a></div>" : "")."</div>";
		}
	}
