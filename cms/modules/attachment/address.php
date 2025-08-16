<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class address extends admin {
	
	private $input;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
	}
	
	public function init() {
		if(IS_AJAX_POST) {
			set_time_limit(120);
			$old_attachment_path = $this->input->post('old_attachment_path') && trim($this->input->post('old_attachment_path')) ? trim($this->input->post('old_attachment_path')) : dr_admin_msg(0,L('old_attachment_address_empty'));
			$new_attachment_path = $this->input->post('new_attachment_path') && trim($this->input->post('new_attachment_path')) ? trim($this->input->post('new_attachment_path')) : dr_admin_msg(0,L('new_attachment_address_empty'));
			//获取数据表列表
			$db = pc_base::load_model('site_model');
			$r = $db->query("show tables");
			$r = $db->fetch_array($db_list);
			foreach ($r as $k=>$v) {
				$v = array_pop($v);
				if (strpos($v, $db->db_tablepre)===false) continue;
				$table_name = str_replace($db->db_tablepre, '', $v);
				//获取每个表的数据表结构
				if (!$modle_table_db = pc_base::load_model($table_name.'_model')) {
					$modle_table_db = $db;
				}
				$s = $modle_table_db->get_fields($table_name);
				if ($s) {
					$sql = '';
					foreach ($s as $key=>$val) {
						//对数据表进行过滤，只有CHAR、TEXT或mediumtext类型的字段才可以保存下附件的地址。
						if (preg_match('/(char|text|mediumtext)+/i', $val)) {
							$sql .= !empty($sql) ? ", `$key`=replace(`$key`, '$old_attachment_path', '$new_attachment_path')" : "`$key`=replace(`$key`, '$old_attachment_path', '$new_attachment_path')";
						}
					}
					if (!empty($sql)) $modle_table_db->query("UPDATE ".$db->db_tablepre.$table_name." SET $sql");
				}
			}
			dr_json(1, L('operation_success'));
		}
		include $this->admin_tpl('address');
	}
}
