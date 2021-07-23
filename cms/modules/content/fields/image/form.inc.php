	function image($field, $value, $fieldinfo) {
		$setting = string2array($fieldinfo['setting']);
		extract($setting);
		if(!defined('IMAGES_INIT')) {
			if (pc_base::load_config('system', 'editor')) {
				$str = '<script type="text/javascript" src="'.JS_PATH.'h5upload/ckeditor.js"></script>';
			} else {
				$str = '<script type="text/javascript" src="'.JS_PATH.'h5upload/ueditor.js"></script>';
			}
			define('IMAGES_INIT', 1);
		}
		$html = '';
		if (defined('IN_ADMIN')) {
			$html = "<input type=\"button\" style=\"width: 66px;\" class=\"button\" onclick=\"crop_cut_".$field."($('#$field').val());return false;\" value=\"".L('cut_the_picture','','content')."\"><input type=\"button\" style=\"width: 66px;\" class=\"button\" onclick=\"$('#".$field."_preview').attr('src','".IMG_PATH."icon/upload-pic.png');$('#".$field."').val('');return false;\" value=\"".L('cancel_the_picture','','content')."\"><script type=\"text/javascript\">function crop_cut_".$field."(id){
	if (id=='') { Dialog.alert('".L('upload_thumbnails', '', 'content')."');return false;}
	var w = 770;
    var h = 510;
	if (is_mobile()) {w = h = '100%';}
	var diag = new Dialog({id:'crop',title:'".L('cut_the_picture','','content')."',url:'index.php?m=content&c=content&a=public_crop&module=content&catid='+catid+'&spec=2&picurl='+window.btoa(unescape(encodeURIComponent(id)))+'&input=$field&preview=".($show_type && defined('IN_ADMIN') ? $field."_preview" : '')."',width:w,height:h,modal:true});diag.onOk = function(){\$DW.dosbumit();return false;};diag.onCancel=function() {\$DW.close();};diag.show();
};</script>";
		}
		$authkey = upload_key("1,$upload_allowext,$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce");
		if($show_type && defined('IN_ADMIN')) {
			$preview_img = $value ? $value : IMG_PATH.'icon/upload-pic.png';
			return $str."<div class='upload-pic img-wrap'><input type='hidden' name='info[$field]' id='$field' value='$value'>
			<a href='javascript:void(0);' onclick=\"h5upload('{$field}_images', '".L('attachment_upload', '', 'content')."','{$field}','thumb_images','1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce','content','$this->catid','$authkey');return false;\">
			<img src='$preview_img' id='{$field}_preview' width='135' height='113' style='cursor:hand' /></a>".$html."</div>";
		} else {
			return $str."<input type='text' name='info[$field]' id='$field' value='$value' size='$size' class='input-text' />  <input type='button' class='button' onclick=\"h5upload('{$field}_images', '".L('attachment_upload', '', 'content')."','{$field}','submit_images','1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce','content','$this->catid','$authkey')\"/ value='".L('upload_pic', '', 'content')."'>".$html;
		}
	}
