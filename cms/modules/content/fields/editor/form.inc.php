	function editor($field, $value, $fieldinfo) {
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid');
		$grouplist = $grouplist[$_groupid];
		extract($fieldinfo);
		extract(string2array($setting));
		$disabled_page = isset($disabled_page) ? $disabled_page : 0;
		if(!$height) $height = 300;
		$allowupload = defined('IN_ADMIN') ? 1 : ($grouplist['allowattachment'] ? 1: 0);
		if(!$value) $value = $defaultvalue;
		//if(!$toolvalue) $toolvalue = '\'Source\',\'Bold\', \'Italic\', \'Underline\'';
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
		return "<div id='{$field}_tip'></div>".'<textarea name="info['.$field.']" id="'.$field.'" boxid="'.$field.'">'.$value.'</textarea>'.form::editor($field,$toolbar,$toolvalue,'content',$this->catid,$color,$allowupload,1,'',$height,$disabled_page,$autofloat,$autoheight,$theme,$watermark,$attachment,$image_reduce,$div2p,$enablesaveimage);
	}
