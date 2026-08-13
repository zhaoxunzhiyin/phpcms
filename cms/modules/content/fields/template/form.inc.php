	function template($field, $value, $fieldinfo) {
		return form::select_template(dr_site_info('default_style', $this->siteid),'content',$value,'name="info['.$field.']" id="'.$field.'"','show');
	}
