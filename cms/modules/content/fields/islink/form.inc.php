	function islink($field, $value, $fieldinfo) {
		if($value) {
			$url = $this->data['url'];
			$checked = 'checked';
			$info['islink'] = 1;
		} else {
			$disabled = 'disabled';
			$url = $checked = '';
			$info['islink'] = 0;
		}
		$size = isset($fieldinfo['size']) && $fieldinfo['size'] ? $fieldinfo['size'] : 25;
		return '<input type="hidden" name="info[islink]" value="0"><label><input type="text" name="linkurl" id="linkurl" value="'.$url.'" size="'.$size.'" maxlength="255" '.$disabled.' class="form-control"></label> <label><div class="mt-checkbox-inline"><label class="mt-checkbox mt-checkbox-outline"><input name="info[islink]" type="checkbox" id="islink" value="1" onclick="ruselinkurl();" '.$checked.'> <font color="red">'.L('islink_url').'</font><span></span></label></div></label>';
	}
