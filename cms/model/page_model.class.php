<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class page_model extends model {
	public $html;
	public $table_name = '';
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'page';
		parent::__construct();
	}
	public function create_html($catid) {
		$this->html = pc_base::load_app_class('html', 'content');
		$this->html->category($catid,1);
	}
}
?>