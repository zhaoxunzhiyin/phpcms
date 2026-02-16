<?php
defined('IN_CMS') or exit('No permission resources.');
class custom_tag {
 	private $input,$custom_db;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->custom_db = pc_base::load_model('custom_model');
 	}

	public function content($data) {
		
		$id = intval($data['id']);
		$siteid = isset($data['siteid']) && intval($data['siteid']) ? intval($data['siteid']) : get_siteid();
		$where = array('siteid'=>$siteid,'id'=>$id);			
		$r = $this->custom_db->select($where, 'content');
		return $r;
	}
}