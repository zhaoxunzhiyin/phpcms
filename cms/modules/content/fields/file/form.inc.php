function file($field, $value, $fieldinfo) {
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
		$authkey = upload_key("1,$upload_allowext,$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce");
		return $str."<input type='text' name='info[$field]' id='$field' value='$value' size='$size' class='input-text' />  <input type='button' class='button' onclick=\"h5upload('{$field}_downfield', '".L('attachment_upload', '', 'content')."','{$field}','submit_attachment','1,{$upload_allowext},$isselectimage,$images_width,$images_height,$watermark,$attachment,$image_reduce','content','$this->catid','$authkey')\"/ value='".L('attachment_upload')."'>";
	}