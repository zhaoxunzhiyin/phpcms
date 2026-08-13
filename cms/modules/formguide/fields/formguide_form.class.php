<?php
class formguide_form {
	public $input,$siteid,$data,$no_allowed;
	public $formid;
	public $fields;
	public $id;
	public $formValidator;

    function __construct($formid, $no_allowed = 0) {
		$this->input = pc_base::load_sys_class('input');
		$this->formid = $formid;
		$this->no_allowed = $no_allowed ? 'disabled=""' : '';
		$this->fields = getcache('model_field_'.$formid, 'model');
		$this->siteid = get_siteid();
    }

	function get($data = array()) {
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$this->data = $data;
		if(isset($data['id'])) $this->id = $data['id'];
		$info = array();
		if (is_array($this->fields)) {
			foreach($this->fields as $field=>$v) {
				if(!isset($v) || check_in($_groupid, $v['unsetgroupids'])) continue;
				$func = $v['formtype'];
				$value = isset($data[$field]) ? $data[$field] : '';
				if($func=='pages' && isset($data['maxcharperpage'])) {
					$value = $data['paginationtype'].'|'.$data['maxcharperpage'];
				}
				if(!method_exists($this, (string)$func)) continue;
				$form = $this->$func($field, $value, $v);
				if($form !== false) {
					$star = $v['minlength'] || $v['pattern'] ? 1 : 0;
					$info[$field] = array('name'=>$v['name'], 'tips'=>$v['tips'], 'form'=>$form, 'star'=>$star,'isomnipotent'=>$v['isomnipotent'],'formtype'=>$v['formtype']);
				}
			}
		}
		return $info;
	}
}?>