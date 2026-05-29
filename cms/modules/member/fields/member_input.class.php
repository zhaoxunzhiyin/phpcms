<?php
class member_input {
	public $input,$db,$db_pre,$siteid,$download;
	public $modelid;
	public $fields;
	public $data;

	function __construct($modelid) {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->db_pre = $this->db->db_tablepre;
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid,'model');

		//初始化附件类
		pc_base::load_sys_class('download','',0);
		$this->siteid = param::get_cookie('siteid');
		$this->download = new download('content','0',$this->siteid);

	}

	function get($data) {
		$_groupid = param::get_cookie('_groupid');
		$this->data = $data;
		$model_cache = getcache('member_model', 'commons');
		$this->db->table_name = $this->db_pre.$model_cache[$this->modelid]['tablename'];

		$info = array();
		$debar_filed = array('catid','title','style','thumb','status','islink','description');
		if(is_array($data)) {
			foreach($data as $field=>$value) {
				if(defined('IS_ADMIN') && IS_ADMIN) {
					if($this->fields[$field]['disabled'] || $this->fields[$field]['iscore'] || check_in(param::get_session('roleid'), $this->fields[$field]['unsetroleids'])) continue;
				} else {
					if($this->fields[$field]['disabled'] || $this->fields[$field]['iscore'] || !$this->fields[$field]['isadd'] || check_in($_groupid, $this->fields[$field]['unsetgroupids'])) continue;
				}
				if($data['islink']==1 && !in_array($field,$debar_filed)) continue;
				$field = safe_replace($field);
				$name = $this->fields[$field]['name'];
				$minlength = $this->fields[$field]['minlength'];
				$maxlength = $this->fields[$field]['maxlength'];
				$pattern = $this->fields[$field]['pattern'];
				$errortips = $this->fields[$field]['errortips'];
				if(dr_is_empty($errortips)) $errortips = $name.' '.L('not_meet_the_conditions');
				$length = dr_is_empty($value) ? 0 : (is_string($value) ? mb_strlen($value) : dr_strlen($value));
				if (SYS_EDITOR && $this->fields[$field]['formtype']=='editor') {
					$jscode = array('field' => $field, 'jscode' => 'CKEDITOR.instances.'.$field.'.focus();');
				} else {
					$jscode = array('field' => $field);
				}
				if($minlength && $length < $minlength) {
					if (IS_ADMIN) {
						dr_admin_msg(0, $name.' '.L('not_less_than').' '.$minlength.L('characters'), $jscode);
					} else {
						dr_msg(0, $name.' '.L('not_less_than').' '.$minlength.L('characters'), $jscode);
					}
				}
				if ($field!='userid' && !array_key_exists($field, $this->fields)) {
					if (IS_ADMIN) {
						dr_admin_msg(0, '模型中不存在'.$field.'字段', $jscode);
					} else {
						dr_msg(0, '模型中不存在'.$field.'字段', $jscode);
					}
				}
				if($maxlength && $length > $maxlength) {
					if (IS_ADMIN) {
						dr_admin_msg(0, $name.' '.L('not_more_than').' '.$maxlength.L('characters'), $jscode);
					} else {
						dr_msg(0, $name.' '.L('not_more_than').' '.$maxlength.L('characters'), $jscode);
					}
				} elseif($maxlength) {
					$value = str_cut($value,$maxlength,'');
				}
				if($pattern && $length && !preg_match($pattern, $value)) {
					if (IS_ADMIN) {
						dr_admin_msg(0, $errortips, $jscode);
					} else {
						dr_msg(0, $errortips, $jscode);
					}
				}
				if($this->fields[$field]['isunique']) {
					if (dr_is_empty($value)) {
						if (IS_ADMIN) {
							dr_admin_msg(0, $name.' '.L('empty'), $jscode);
						} else {
							dr_msg(0, $name.' '.L('empty'), $jscode);
						}
					}
					if ($this->db->table_exists($this->db->table_name)) {
						if ($this->db->field_exists($field)) {
							$isunique_value = $this->db->count(array($field=>$value,'userid<>'=>(int)$data['userid']));
						} else {
							log_message('error', "字段唯一性验证失败：表".$this->db->table_name."中字段".$field."不存在！");
						}
					} else {
						log_message('error', "字段唯一性验证失败：数据表不存在！");
					}
					if($isunique_value) {
						if (IS_ADMIN) {
							dr_admin_msg(0, $name.' '.L('the_value_must_not_repeat'), $jscode);
						} else {
							dr_msg(0, $name.' '.L('the_value_must_not_repeat'), $jscode);
						}
					}
				}
				$func = $this->fields[$field]['formtype'];
				if(method_exists($this, (string)$func)) $value = $this->$func($field, $value);

				$field!='userid' && $info[$field] = $value;
			}
		}
		return $info;
	}
}?>