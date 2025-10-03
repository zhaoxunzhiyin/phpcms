	function editor($field, $value, $fieldinfo) {
		$grouplist = getcache('grouplist','member');
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		if ($_groupid) {
			$grouplist = $grouplist[$_groupid];
		}
		//是否允许用户上传附件 ，后台管理员开启此功能
		extract($fieldinfo);
		extract(string2array($setting));
		// 表单宽度设置
		$width = is_mobile() ? '100%' : ($width ? $width : '100%');
		// 表单高度设置
		if(!$height) $height = 300;
		$allowupload = defined('IS_ADMIN') && IS_ADMIN ? 1 : (isset($grouplist['allowattachment']) && $grouplist['allowattachment'] && $allowupload ? 1: 0);
		$value = code2html((string)(dr_strlen($value) ? $value : $defaultvalue));
		//if(!$toolvalue) $toolvalue = '\'Source\',\'Bold\', \'Italic\', \'Underline\'';
		if($minlength || $pattern) $allow_empty = '';
		return "<div id='{$field}_tip'></div>".'<textarea class="dr_ueditor dr_ueditor_'.$field.'" name="info['.$field.']" id="'.$field.'">'.$value.'</textarea>'.form::editor($field,$toolbar,'member','',$color,$allowupload,$isselectimage,$upload_allowext,$height,0,$upload_number,$this->modelid,$toolvalue,$autofloat,$autoheight,$theme,$language,$watermark,$attachment,$image_reduce,$chunk,$div2p,$anchorduiqi,$imageduiqi,$videoduiqi,$musicduiqi,$enter,$enablesaveimage,$width,$upload_maxsize);
	}
