<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class fclient_model extends model {
	function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'fclient';
		parent::__construct();
	} 
}
?>