function editor($field, $value) {
	$attachment_db = pc_base::load_model('attachment_model');
	$downloadfiles = pc_base::load_sys_class('cache')->get_data('downloadfiles-'.get_siteid());
	$attachment_db->api_update($downloadfiles,'c-'.$this->data['catid'].'-'.$this->id,1);
	pc_base::load_sys_class('cache')->clear('downloadfiles-'.get_siteid());
	$value = str_replace(' style=""', '', $value);
	return html2code($value);
}