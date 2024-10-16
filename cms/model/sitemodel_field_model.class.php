<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
class sitemodel_field_model extends model {
	public $table_name = '';
	public function __construct() {
		$this->db_config = pc_base::load_config('database');
		$this->db_setting = 'default';
		$this->table_name = 'model_field';
		parent::__construct();
	}
	/**
	 * 删除字段
	 * 
	 */
	public function drop_field($tablename,$field,$issystem = 1) {
		$this->table_name = $this->db_tablepre.$tablename;
		$fields = $this->get_fields();
		$sql = "ALTER TABLE `$this->table_name` DROP `$field`;";
		if(in_array($field, array_keys($fields))) {
			if (!$issystem) {
				return sql_module($this->table_name, $sql);
			} else {
				return $this->db->query($sql);
			}
		} else {
			return false;
		}
	}
	
	/**
	 * 改变数据表
	 */
	public function change_table($tablename = '') {
		if (!$tablename) return false;
		
		$this->table_name = $this->db_tablepre.$tablename;
		return true;
	}
}
?>