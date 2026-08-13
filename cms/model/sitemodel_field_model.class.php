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
		$table_name = $this->db_tablepre.$tablename;
		$sql = "ALTER TABLE `$table_name` DROP `$field`;";
		if($this->field_exists($field, $table_name)) {
			if (!$issystem) {
				return sql_module($table_name, $sql);
			} else {
				return $this->query($sql);
			}
		} else {
			return false;
		}
	}
}
?>