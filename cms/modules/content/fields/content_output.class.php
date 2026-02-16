<?php
class content_output {
	public $input,$modelid,$catid,$categorys,$id;
	public $fields;
	public $data;

	function __construct($modelid,$catid = 0,$categorys = array()) {
		$this->input = pc_base::load_sys_class('input');
		$this->modelid = $modelid;
		$this->catid = $catid;
		$this->categorys = $categorys;
		$this->fields = getcache('model_field_'.$modelid,'model');
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