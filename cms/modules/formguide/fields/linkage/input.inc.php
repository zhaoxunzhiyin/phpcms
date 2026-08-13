	function linkage($field, $value) {
		$setting = string2array($this->fields[$field]['setting']);
		$value = intval($value);
		if ($value) {
			$link = dr_linkage($setting['linkage'], $value);
			if (!$link) {
				if (IS_ADMIN) {
					dr_admin_msg(0, L('选项无效'), array('field' => $field));
				} else {
					dr_msg(0, L('选项无效'), array('field' => $field));
				}
			} elseif ($this->fields[$field]['minlength'] && $setting['ck_child'] && $link['child']) {
				if (IS_ADMIN) {
					dr_admin_msg(0, L('需要选择下级选项'), array('field' => $field));
				} else {
					dr_msg(0, L('需要选择下级选项'), array('field' => $field));
				}
			}
		}
		return $value;
	}
