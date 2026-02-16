	function image($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		extract($setting);
		$str = load_js(JS_PATH.'h5upload/h5editor.js');
		$html = "<label><button type=\"button\" onclick=\"$('#".$field."_preview').attr('src','".IMG_PATH."icon/upload-pic.png');$('#".$field."').val('');".($show_type && defined('IS_ADMIN') && IS_ADMIN ? "" : "$('#dr_".$field."_files_row').html('');")."$('#fileupload_".$field."').find('.".$field."-delete').hide();".($show_type && defined('IS_ADMIN') && IS_ADMIN ? "$('#fileupload_".$field."').find('.mpreview').html('');" : "")."return false;\" class=\"btn red btn-sm ".$field."-delete\"".($value ? "" : " style=\"display:none\"")."> <i class=\"fa fa-trash\"></i> ".L('cancel_the_picture','','content')."</button></label>";
		if (defined('IS_ADMIN') && IS_ADMIN) {
			$html .= "<input type='hidden' name=\"crop_".$field."\" id=\"crop_".$field."\" class=\"hide\"><script type=\"text/javascript\">function crop_cut_".$field."(id){
	if (id=='') { Dialog.alert('".L('upload_thumbnails', '', 'content')."');return false;}
	var w = 770;
	var h = 510;
	if (is_mobile()) {w = h = '100%';}
	var diag = new Dialog({id:'crop',title:'".L('cut_the_picture','','content')."',url:'".SELF."?m=content&c=content&a=public_crop&module=content&catid='+(typeof catid != \"undefined\" && catid ? catid : 0)+'&spec=2&aid='+id+'&input=$field&preview=".($show_type && defined('IS_ADMIN') && IS_ADMIN ? $field."_preview" : '')."&files_row=".($show_type && defined('IS_ADMIN') && IS_ADMIN ? '' : "dr_".$field."_files_row")."',width:w,height:h,modal:true});diag.onOk = function(){\$DW.dosbumit();return false;};diag.onCancel=function() {\$DW.close();};diag.show();
};</script>";
		}
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
		if($show_type && defined('IS_ADMIN') && IS_ADMIN) {
			$preview_img = $value ? (dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value))) : IMG_PATH.'icon/upload-pic.png';
			return $str."<div class='upload-pic img-wrap'><div class=\"row fileupload-buttonbar\" id=\"fileupload_{$field}\"><div class=\"col-lg-12\"><input type='hidden' name='info[$field]' id='$field' value='$value'>
			<p><a href='javascript:void(0);' onclick=\"h5upload('".SELF."', '{$field}_images', '".L('attachment_upload', '', 'content')."','{$field}','thumb_images','{$p}','content','$this->catid','$authkey',".SYS_EDITOR.");return false;\">
			<img src='$preview_img' id='{$field}_preview' width='135' height='113' style='cursor:hand' /></a><div class=\"mpreview\">".(is_numeric($value) && dr_is_image(dr_get_file($value)) ? "<a href=\"javascript:crop_cut_".$field."($('#$field').val());\"><i class=\"fa fa-cut\"></i></a>" : "")."</div></p>".$html."</div></div></div>";
		} else {
			return $str."<div class=\"row fileupload-buttonbar\" id=\"fileupload_{$field}\"><div class=\"col-lg-12\"><label><input type='hidden' name='info[$field]' id='$field' value='$value'><button type=\"button\" onclick=\"h5upload('".SELF."', '{$field}_images', '".L('attachment_upload', '', 'content')."','{$field}','submit_images','{$p}','content','$this->catid','$authkey',".SYS_EDITOR.")\" class=\"btn green btn-sm\"> <i class=\"fa fa-plus\"></i> ".L('upload_pic', '', 'content')."</button></label> ".$html."</div></div><div id='dr_".$field."_files_row' class='file_row_html files_row'>".($value ? "<div class=\"files_row_preview preview\"><a href=\"javascript:preview('".dr_get_file($value)."');\"><img src=\"".(dr_is_image(dr_get_file($value)) ? dr_get_file($value) : WEB_PATH."api.php?op=icon&fileext=".fileext(dr_get_file($value)))."\"></a></div>".(defined('IS_ADMIN') && IS_ADMIN && is_numeric($value) && dr_is_image(dr_get_file($value)) ? "<div class=\"mpreview\"><a href=\"javascript:crop_cut_".$field."($('#$field').val());\"><i class=\"fa fa-cut\"></i></a></div>" : "") : "")."</div>";
		}
	}
