<?php
class content_update {
	public $input;
	public $modelid;
	public $fields;
	public $data;
	public $id;

    function __construct($modelid,$id) {
		$this->input = pc_base::load_sys_class('input');
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid,'model');
		$this->id = $id;
    }
	function update($data) {
		$info = array();
		$this->data = $data;
		foreach($data as $field=>$value) {
			if(!isset($this->fields[$field])) continue;
			$func = $this->fields[$field]['formtype'];
			$info[$field] = method_exists($this, (string)$func) ? $this->$func($field, $value) : $value;
		}
	}
}?>