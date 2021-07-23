function editor($field, $value) {
	$attachment_db = pc_base::load_model('attachment_model');
	$attachment_db->api_update($GLOBALS['downloadfiles'],'c-'.$this->data['catid'].'-'.$this->id,1);
	$value = str_replace(array('&lt;iframe', '&gt;&lt;/iframe&gt;'), array('<iframe', '></iframe>'), $value);
	return htmlspecialchars($value);
}