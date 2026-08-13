<?php
class member_form {
	public $input,$data;
	public $modelid;
	public $fields;
	public $id;
	public $formValidator;

    function __construct($modelid) {
		$this->input = pc_base::load_sys_class('input');
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid,'model');
    }

	function get($data = array()) {
		$_groupid = param::get_cookie('_groupid');
		$this->data = $data;
		if(isset($data['id'])) $this->id = $data['id'];
		$info = array();
		if (is_array($this->fields)) {
			foreach($this->fields as $field=>$v) {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					if($v['disabled'] || $v['iscore'] || check_in(param::get_session('roleid'), $v['unsetroleids'])) continue;
				} else {
					if($v['disabled'] || $v['iscore'] || !$v['isadd'] || check_in($_groupid, $v['unsetgroupids'])) continue;
				}
				$func = $v['formtype'];
				$value = isset($data[$field]) ? $data[$field] : '';
				if($func=='pages' && isset($data['maxcharperpage'])) {
					$value = $data['paginationtype'].'|'.$data['maxcharperpage'];
				}
				if(!method_exists($this, (string)$func)) continue;
				$form = $this->$func($field, $value, $v);
				if($form !== false) {
					$star = $v['minlength'] || $v['pattern'] ? 1 : 0;
					$info[$field] = array('name'=>$v['name'], 'tips'=>$v['tips'], 'form'=>$form, 'star'=>$star, 'isbase'=>$v['isbase'],'isomnipotent'=>$v['isomnipotent'],'formtype'=>$v['formtype']);
				}
			}
		}
		return $info;
	}
}?>