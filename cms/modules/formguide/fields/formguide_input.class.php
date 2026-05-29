<?php
class formguide_input {
	public $input,$siteid,$download;
	public $formid;
	public $fields;
	public $data;

	function __construct($formid) {
		$this->input = pc_base::load_sys_class('input');
		$this->formid = $formid;
		$this->fields = getcache('model_field_'.$formid, 'model');
		$this->siteid = get_siteid();
		//初始化附件类
		pc_base::load_sys_class('download','',0);
		$this->siteid = param::get_cookie('siteid');
		$this->download = new download('formguide','0',$this->siteid);
	}

	function get($data,$isimport = 0) {
		$_groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$this->data = $data;
		$info = array();
		if (is_array($data)) {
			foreach($data as $field=>$value) {
				if(!isset($this->fields[$field]) || check_in($_groupid, $this->fields[$field]['unsetgroupids'])) continue;
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
					if($isimport) {
						return false;
					} else {
						if (IS_ADMIN) {
							dr_admin_msg(0, $name.' '.L('not_less_than').' '.$minlength.L('characters'), $jscode);
						} else {
							dr_msg(0, $name.' '.L('not_less_than').' '.$minlength.L('characters'), $jscode);
						}
					}
				}
				if($maxlength && $length > $maxlength) {
					if($isimport) {
						$value = str_cut($value,$maxlength,'');
					} else {
						if (IS_ADMIN) {
							dr_admin_msg(0, $name.' '.L('not_more_than').' '.$maxlength.L('characters'), $jscode);
						} else {
							dr_msg(0, $name.' '.L('not_more_than').' '.$maxlength.L('characters'), $jscode);
						}
					}
				} elseif($maxlength) {
					$value = str_cut($value,$maxlength,'');
				}
				if($pattern && $length && !preg_match($pattern, $value) && !$isimport) {
					if (IS_ADMIN) {
						dr_admin_msg(0, $errortips, $jscode);
					} else {
						dr_msg(0, $errortips, $jscode);
					}
				}
				$func = $this->fields[$field]['formtype'];
				if(method_exists($this, (string)$func)) $value = $this->$func($field, $value);
				$info[$field] = $value;
				//颜色选择为隐藏域 在这里进行取值
				if ($this->input->post('style_color')) $info['style'] = $this->input->post('style_color');
				if($this->input->post('style_font_weight')) $info['style'] = $info['style'].';'.clearhtml($this->input->post('style_font_weight'));
			}
		}
		return $info;
	}
}?>