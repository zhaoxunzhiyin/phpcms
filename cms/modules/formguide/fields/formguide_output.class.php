<?php
class formguide_output {
	public $input,$id,$formid,$siteid;
	public $fields;
	public $data;

	function __construct($formid) {
		$this->input = pc_base::load_sys_class('input');
		$this->formid = $formid;
		$this->fields = getcache('model_field_'.$formid, 'model');
		$this->siteid = get_siteid();
    }
	function get($data) {
		$this->data = $data;
		$this->id = $data['id'];
		$info = array();
		if (is_array($this->fields)) {
			foreach($this->fields as $field=>$v) {
				if(!isset($data[$field])) continue;
				$func = $v['formtype'];
				$value = $data[$field];
				$result = method_exists($this, (string)$func) ? $this->$func($field, $data[$field]) : $data[$field];
				if($result !== false) $info[$field] = $result;
			}
		}
		return $info;
	}
}?>