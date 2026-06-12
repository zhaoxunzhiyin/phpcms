	function wxurl($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		$bformattribute = str_replace('get_wxurl(','get_wxurl('.SYS_EDITOR.',\''.$field.'\',\''.WEB_PATH.'api.php?op=get_wxurl&module=content&catid='.$this->catid.'&isadmin='.$this->isadmin.'&userid='.$this->userid.'&groupid='.$this->groupid.'&siteid='.$this->siteid.'&is_esi='.$enablesaveimage.'&watermark='.$watermark.'&attachment='.$attachment.'&image_reduce='.$image_reduce.'&fieldname='.L($name).'\',',$setting['bformattribute']);
		if (dr_is_empty($value)) $value = $defaultvalue;
		//if (defined('IS_ADMIN') && IS_ADMIN) {
			return '<div class="input-group">
            <input type="text" name="info['.$field.']" id="'.$field.'" value="'.$value.'" class="form-control">
            <span class="input-group-btn"><button type="button" class="btn red" onclick="javascript:'.$bformattribute.';"><i class="fa fa-plus"></i> '.L('import_wxurl').'</button></span>
        </div>';
		//} else {
			//return L('import_wxurl_publish');
		//}
	}
