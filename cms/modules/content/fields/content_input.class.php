<?php
class content_input {
	public $input,$cache,$db,$db_pre,$siteid,$isadmin,$userid,$groupid,$rid,$download,$sitedb;
	public $modelid;
	public $fields;
	public $data;

	function __construct($modelid) {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->db_pre = $this->db->db_tablepre;
		$this->modelid = $modelid;
		$this->fields = getcache('model_field_'.$modelid,'model');
		//初始化附件类
		pc_base::load_sys_class('download','',0);
		$this->siteid = param::get_cookie('siteid');
		$this->isadmin = IS_ADMIN && param::get_session('roleid') ? 1 : 0;
		$this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
		$this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		$this->download = new download('content','0',$this->siteid);
		$this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().intval($this->userid));
	}

	function get($data,$isimport = 0) {
		$this->data = $data;
		$info = array();
		if (is_array($data)) {
			foreach($data as $field=>$value) {
				if(!isset($this->fields[$field]) && !check_in($field,'paytype,paginationtype,maxcharperpage,id')) continue;
				if(defined('IS_ADMIN') && IS_ADMIN) {
					if(check_in(param::get_session('roleid'), $this->fields[$field]['unsetroleids'])) continue;
				} else {
					if(check_in($this->groupid, $this->fields[$field]['unsetgroupids'])) continue;
				}
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
				if(isset($this->input->post('info')['islink']) && $this->input->post('info')['islink']==1 && !$this->input->post('linkurl')) {
					if($isimport) {
						return false;
					} else {
						if (IS_ADMIN) {
							dr_admin_msg(0, L('islink_url').L('empty'), array('field' => 'linkurl'));
						} else {
							dr_msg(0, L('islink_url').L('empty'), array('field' => 'linkurl'));
						}
					}
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
				if ($this->modelid && $this->modelid!=-1 && $this->modelid!=-2) {
					if($this->fields[$field]['isunique']) {
						if (dr_is_empty($value)) {
							if (IS_ADMIN) {
								dr_admin_msg(0, $name.' '.L('empty'), $jscode);
							} else {
								dr_msg(0, $name.' '.L('empty'), $jscode);
							}
						}
						if (!$this->fields[$field]['issystem']) {
							$MODEL = getcache('model', 'commons');
							$this->db->table_name = $this->db_pre.$MODEL[$this->modelid]['tablename'];
							for ($i = 0;; $i ++) {
								$this->db->table_name = $this->db_pre.$MODEL[$this->modelid]['tablename'].'_data_'.$i;
								$this->db->query("SHOW TABLES LIKE '".$this->db->table_name."'");
								$table_exists = $this->db->fetch_array();
								if (!$table_exists) {
									break;
								}
								$isunique_value = $this->db->get_one(array($field=>$value,'id<>'=>(int)$data['id']),$field);
								$this->db->table_name = $this->db_pre.$MODEL[$this->modelid]['tablename'];
							}
						} else {
							if ($this->db->table_exists($this->db->table_name)) {
								if ($this->db->field_exists($field)) {
									$isunique_value = $this->db->count(array($field=>$value,'id<>'=>(int)$data['id']));
								} else {
									log_message('error', "字段唯一性验证失败：表".$this->db->table_name."中字段".$field."不存在！");
								}
							} else {
								log_message('error', "字段唯一性验证失败：数据表不存在！");
							}
						}
						if($isunique_value) {
							if (IS_ADMIN) {
								dr_admin_msg(0, $name.' '.L('the_value_must_not_repeat'), $jscode);
							} else {
								dr_msg(0, $name.' '.L('the_value_must_not_repeat'), $jscode);
							}
						}
					}
				}
				$func = $this->fields[$field]['formtype'];
				if(method_exists($this, (string)$func)) $value = $this->$func($field, $value);
				if($this->fields[$field]['issystem']) {
					$info['system'][$field] = $value;
				} else {
					$info['model'][$field] = $value;
				}
				//颜色选择为隐藏域 在这里进行取值
				$info['system']['style'] = $this->input->post('style_color') && preg_match('/^#([0-9a-z]+)/i', $this->input->post('style_color')) ? $this->input->post('style_color') : '';
				if($this->input->post('style_font_weight')=='bold') $info['system']['style'] = $info['system']['style'].';'.clearhtml($this->input->post('style_font_weight'));
			}
		}
		return $info;
	}
}?>