	function wxurl($field, $value, $fieldinfo) {
		extract($fieldinfo);
		$setting = string2array($setting);
		$width = $setting['width'];
		if (pc_base::load_config('system', 'editor')) {
			$bformattribute = str_replace('get_wxurl(','get_wxurlckeditor(\''.$field.'\',\''.WEB_PATH.'api.php?op=get_wxurl&module=content&catid='.$this->catid.'&is_esi='.$enablesaveimage.'&watermark='.$watermark.'&attachment='.$attachment.'&image_reduce='.$image_reduce.'\',',$setting['bformattribute']);
		} else {
			$bformattribute = str_replace('get_wxurl(','get_wxurl(\''.$field.'\',\''.WEB_PATH.'api.php?op=get_wxurl&module=content&catid='.$this->catid.'&is_esi='.$enablesaveimage.'&watermark='.$watermark.'&attachment='.$attachment.'&image_reduce='.$image_reduce.'\',',$setting['bformattribute']);
		}
		if(!$value) $value = $defaultvalue;
		//if (defined('IN_ADMIN')) {
			return '<input type="text" name="info['.$field.']" id="'.$field.'" style="width:'.$width.'px;" value="'.$value.'" class="input-text" '.$formattribute.' '.$css.'><button type="button" class="button" onclick="javascript:'.$bformattribute.';"><i class="fa fa-plus"></i> '.L('import_wxurl').'</button>';
		//} else {
			//return L('import_wxurl_publish');
		//}
	}
