	function editor($field, $value, $fieldinfo) {
		//是否允许用户上传附件 ，后台管理员开启此功能
		extract($fieldinfo);
		extract(string2array($setting));
		$allowupload = defined('IN_ADMIN') ? 1 : 0;
		if(!$value) $value = $defaultvalue;
		if($minlength || $pattern) $allow_empty = '';
		if (pc_base::load_config('system', 'editor')) {
			if($minlength) $this->checkall .= 'if(CKEDITOR.instances.'.$field.'.getData()==""){
				Dialog.alert("'.$errortips.'",function(){editor.focus();})
				return false;
			}';
		} else {
			if($minlength) $this->checkall .= 'if(UE.getEditor("'.$field.'").getContent()==""){
				Dialog.alert("'.$errortips.'",function(){UE.getEditor("'.$field.'").focus();})
				return false;
			}';
		}
		return "<div id='{$field}_tip'></div>".'<textarea name="info['.$field.']" id="'.$field.'" boxid="'.$field.'">'.$value.'</textarea>'.form::editor($field,$toolbar,$toolvalue,'member','','',$allowupload,1,'',$height,'',$autofloat,$autoheight,$theme,$watermark,$attachment,$image_reduce,$div2p);
	}
