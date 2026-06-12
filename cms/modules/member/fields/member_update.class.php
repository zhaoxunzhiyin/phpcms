<?php
class member_update {
	public $input,$db,$db_pre;
	public $modelid;
	public $fields;
	public $data;

    function __construct($modelid) {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->db_pre = $this->db->db_tablepre;
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid,'model');
    }
}?>