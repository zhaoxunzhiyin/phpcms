<?php
//模型原型存储路径
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'formguide'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);

class formguide_field extends admin {
	private $input,$db,$model_db,$cache_api,$siteid;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
			$formid = intval($this->input->get('formid'));
			$this->cache_field($formid);
			$datas = $this->db->select(array('modelid'=>$formid),'*','',$this->input->get('order') ? $this->input->get('order') : 'disabled ASC,listorder ASC,fieldid ASC');
			$r = $this->model_db->get_one(array('modelid'=>$formid));
		} else {
			$data = $datas = array();
			$data = getcache('form_public_field_array', 'model');
			if (is_array($data)) {
				foreach ($data as $_k => $_v) {
					$datas[$_k] = $_v['info'];
				}
			}
		}
		$show_header = $show_validator = $show_dialog = true;
		require MODEL_PATH.'fields.inc.php';
		include $this->admin_tpl('formguide_field_list');
	}
	
	/**
	 * 添加字段，当没有formid时为添加公用字段
	 */
	public function add() {
		if(IS_AJAX_POST) {
			$info = $this->input->post('info', false);
			$setting = $this->input->post('setting', false);
			if (!$info['formtype']) dr_json(0, L('select_fieldtype'), array('field' => 'formtype'));
			if (!$info['name']) dr_json(0, L('field_nickname').L('empty'), array('field' => 'name'));
			if (!$info['field']) dr_json(0, L('fieldname').L('empty'), array('field' => 'field'));
			$info['issystem'] = 1;
			$field = $info['field'];
			$cname = $info['name'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			//附加属性值
			$info['setting'] = array2string($setting);
			$info['siteid'] = $this->siteid;
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';
			if (in_array($field, array('dataid', 'userid', 'username', 'datetime', 'ip'))) {
				dr_json(0, L('fieldname').'（'.$field.'）'.L('already_exist'), array('field' => 'field'));
			}
			
			if (is_file(MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php')) {
				require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php';
			}
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
				
			if(isset($setting['fieldtype'])) {
				$field_type = $setting['fieldtype'];
			}
			if (isset($info['modelid']) && !empty($info['modelid'])) {
				$formid = intval($info['modelid']);
				$forminfo = $this->model_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->siteid), 'tablename');
				$tablename = $this->db->db_tablepre.'form_'.$forminfo['tablename'];
				$model_field = $this->db->get_one(array('modelid'=>$formid, 'field'=>$field, 'siteid'=>$this->siteid));
				if (!$model_field) {
					$field_rs = $this->db->query('SHOW FULL COLUMNS FROM `'.$tablename.'`');
					foreach ($field_rs as $rs) {
						if ($rs['Field']==$field) {
							$model_field = 1;
						}
					}
				}
				if ($model_field) dr_json(0, L('fieldname').'（'.$field.'）'.L('already_exist'), array('field' => 'field'));
				require MODEL_PATH.'add.sql.php';
				
				$this->db->insert($info);
				$this->cache_field($formid);
			} else {
				$info['disabled'] = 0;
				$info['listorder'] = 0;
				$unrunsql = 1;
				$tablename = 'formguide_table';
				require MODEL_PATH.'add.sql.php';
				
				$form_public_field_array = getcache('form_public_field_array', 'model');
				!$form_public_field_array && $form_public_field_array = array();
				if (is_array($form_public_field_array) && array_key_exists($info['field'], $form_public_field_array)) {
					dr_json(0, L('fieldname').L('already_exist'), array('field' => 'field'));
				} else {
					$form_public_field_array[$info['field']] = array('info'=>$info, 'sql'=>$sql); 
					setcache('form_public_field_array', $form_public_field_array, 'model');	
				}
			}
			dr_json(1, L('add_success'));
		} else {
			$show_header = $show_validator = $show_dialog = true;
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php';
			$formid = intval($this->input->get('formid'));
			$f_datas = $this->db->select(array('modelid'=>$formid),'field,name',100,'listorder ASC');
			$m_r = $this->model_db->get_one(array('modelid'=>$formid));
			foreach($f_datas as $_k=>$_v) {
				$exists_field[] = $_v['field'];
			}

			$all_field = array();
			foreach($fields as $_k=>$_v) {
				$all_field[$_k] = $_v.'（'.$_k.'）';
			}

			$grouplist = array();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			$reply_url = '?m=formguide&c=formguide_field&a=init&formid='.$formid.'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('formguide_field_add');
		}
	}
	
	public function edit() {
		if (IS_AJAX_POST) {
			$info = $this->input->post('info', false);
			$setting = $this->input->post('setting', false);
			if (!$info['formtype']) dr_json(0, L('select_fieldtype'), array('field' => 'formtype'));
			if (!$info['name']) dr_json(0, L('field_nickname').L('empty'), array('field' => 'name'));
			if (!$info['field']) dr_json(0, L('fieldname').L('empty'), array('field' => 'field'));
			$field = $info['field'];
			$cname = $info['name'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			
			//附加属性值
			$info['setting'] = array2string($setting);
			$info['siteid'] = $this->siteid;
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';
			if (in_array($field, array('dataid', 'userid', 'username', 'datetime', 'ip'))) {
				dr_json(0, L('fieldname').'（'.$field.'）'.L('already_exist'), array('field' => 'field'));
			}
			
			if (is_file(MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php')) {
				require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php';
			}
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			
			if(isset($setting['fieldtype'])) {
				$field_type = $setting['fieldtype'];
			}
			$oldfield = $this->input->post('oldfield');
			if (isset($info['modelid']) && !empty($info['modelid'])) {
				$formid = intval($info['modelid']);
				$fieldid = intval($this->input->post('fieldid'));
				$forminfo = $this->model_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->siteid), 'tablename');
				$tablename = $this->db->db_tablepre.'form_'.$forminfo['tablename'];
				$model_field = $this->db->get_one(array('modelid'=>$formid, 'field'=>$field, 'fieldid<>'=>$fieldid, 'siteid'=>$this->siteid));
				if (!$model_field && $field!=$oldfield) {
					$field_rs = $this->db->query('SHOW FULL COLUMNS FROM `'.$tablename.'`');
					foreach ($field_rs as $rs) {
						if ($rs['Field']==$field) {
							$model_field = 1;
						}
					}
				}
				if ($model_field) dr_json(0, L('fieldname').'（'.$field.'）'.L('already_exist'), array('field' => 'field'));
				
				require MODEL_PATH.'edit.sql.php';
				$this->db->update($info,array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
			} else {
				$unrunsql = 1;
				$tablename = 'formguide_table';
				require MODEL_PATH.'add.sql.php';
				
				$form_public_field_array = getcache('form_public_field_array', 'model');
				!$form_public_field_array && $form_public_field_array = array();
				if ($oldfield) {
					if (isset($form_public_field_array[$oldfield]['info']['disabled'])) {
						$info['disabled'] = $form_public_field_array[$oldfield]['info']['disabled'];
					}
					if (isset($form_public_field_array[$oldfield]['info']['listorder'])) {
						$info['listorder'] = $form_public_field_array[$oldfield]['info']['listorder'];
					}
					if ($oldfield == $info['field']) {
						$form_public_field_array[$info['field']] = array('info'=>$info, 'sql'=>$sql);
					} else {
						if (is_array($form_public_field_array) && array_key_exists($info['field'], $form_public_field_array)) {
							dr_json(0, L('fieldname').L('already_exist'), array('field' => 'field'));
						}
						$new_form_field = $form_public_field_array;
						$form_public_field_array = array();
						foreach ($new_form_field as $name => $v) {
							if ($name == $oldfield) {
								$form_public_field_array[$info['field']] = array('info'=>$info, 'sql'=>$sql);
							} else {
								$form_public_field_array[$name] = $v;
							}
						}
					}
				}
				setcache('form_public_field_array', $form_public_field_array, 'model');	
			}
			$this->cache_field($formid);
			dr_json(1, L('update_success'));
		} else {
			$show_header = $show_validator = $show_dialog = true;
			if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
				pc_base::load_sys_class('form','',0);
				require MODEL_PATH.'fields.inc.php';
				$formid = intval($this->input->get('formid'));
				$fieldid = intval($this->input->get('fieldid'));
				$all_field = array();
				foreach($fields as $_k=>$_v) {
					$all_field[$_k] = $_v.'（'.$_k.'）';
				}
				$m_r = $this->model_db->get_one(array('modelid'=>$formid));
				$r = $this->db->get_one(array('fieldid'=>$fieldid));
				extract($r);
				if($unsetgroupids != '') $unsetgroupids = strpos($unsetgroupids, ',') ? explode(',', $unsetgroupids) : array($unsetgroupids);
				require MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'config.inc.php';
			} else {
				if (!$this->input->get('field') || empty($this->input->get('field'))) {
					dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
				}
				
				$form_public_field_array = getcache('form_public_field_array', 'model');
				if (!array_key_exists($this->input->get('field'), $form_public_field_array)) {
					dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
				}
				extract($form_public_field_array[$this->input->get('field')]);
				extract($info);
				if($unsetgroupids != '') $unsetgroupids = strpos($unsetgroupids, ',') ? explode(',', $unsetgroupids) : array($unsetgroupids);
				$setting = stripslashes($setting);
				$show_header = $show_validator = $show_dialog = true;
				pc_base::load_sys_class('form','',0);
				require MODEL_PATH.'fields.inc.php';
				$all_field = array();
				foreach($fields as $_k=>$_v) {
					$all_field[$_k] = $_v.'（'.$_k.'）';
				}
			}
			$setting = string2array($setting);
			ob_start();
			include MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'field_edit_form.inc.php';
			$form_data = ob_get_contents();
			ob_end_clean();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			$reply_url = '?m=formguide&c=formguide_field&a=init&formid='.$formid.'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('formguide_field_edit');
		}
	}
	
	/**
	 * 禁用、开启字段
	 */
	public function disabled() {
		$formid = intval($this->input->get('formid'));
		if ($formid) {
			$fieldid = intval($this->input->get('fieldid'));
			$r = $this->db->get_one(array('modelid'=>$formid,'fieldid'=>$fieldid,'siteid'=>$this->siteid));
			$value = $r['disabled'] ? 0 : 1;
			$this->db->update(array('disabled'=>$value),array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
			$this->cache_field($formid);
		} else {
			$field = $this->input->get('field');
			$form_public_field_array = getcache('form_public_field_array', 'model');
			$value = $form_public_field_array[$field]['info']['disabled'] ? 0 : 1;
			$form_public_field_array[$field]['info']['disabled'] = $value;
			setcache('form_public_field_array', $form_public_field_array, 'model');
		}
		dr_json(1, L(($value ? '禁' : '启').'用成功'), array('value' => $value));
	}
	
	/**
	 * 删除字段
	 */
	public function delete() {
		$formid = intval($this->input->get('formid'));
		if ($formid) {
			$ids = $this->input->get_post_ids();
			if (!$ids) {
				dr_json(0, L('你还没有选择呢'));
			}
			foreach ($ids as $id) {
				$r = $this->model_db->get_one(array('modelid'=>$formid), 'tablename');
				$rs = $this->db->get_one(array('fieldid'=>$id, 'siteid'=>$this->siteid), 'field');
				$this->db->delete(array('fieldid'=>$id, 'siteid'=>$this->siteid));
				if ($r) {
					$field = $rs['field'];
					$tablename = $this->db->db_tablepre.'form_'.$r['tablename'];
					require MODEL_PATH.'delete.sql.php';
				}
			}
			$this->cache_field($formid);
		} else {
			$ids = $this->input->post('ids');
			if (!$ids) {
				dr_json(0, L('你还没有选择呢'));
			}
			$form_public_field_array = getcache('form_public_field_array', 'model');
			foreach ($ids as $id) {
				if (array_key_exists($id, $form_public_field_array)) {
					unset($form_public_field_array[$id]);
				}
			}
			setcache('form_public_field_array', $form_public_field_array, 'model');
		}
		dr_json(1, L('operation_success'), ['ids' => $ids]);
	}
	
	/**
	 * 排序
	 */
	public function listorder() {
		$formid = intval($this->input->get('formid'));
		if ($formid) {
			$fieldid = intval($this->input->get('fieldid'));
			// 查询数据
			$r = $this->db->get_one(array('modelid'=>$formid,'fieldid'=>$fieldid,'siteid'=>$this->siteid));
			if (!$r) {
				dr_json(0, L('数据#'.$fieldid.'不存在'));
			}
			$value = (int)$this->input->get('value');
			$this->db->update(array('listorder'=>$value),array('modelid'=>$formid,'fieldid'=>$fieldid,'siteid'=>$this->siteid));
			$this->cache_field($formid);
		} else {
			$field = $this->input->get('field');
			$form_public_field_array = getcache('form_public_field_array', 'model');
			$value = (int)$this->input->get('value');
			$form_public_field_array[$field]['info']['listorder'] = $value;
			setcache('form_public_field_array', $form_public_field_array, 'model');
		}
		dr_json(1, L('operation_success'));
	}
	
	/**
	 * 字段属性设置
	 */
	public function public_field_setting() {
		$fieldtype = $this->input->get('fieldtype');
		if (!is_file(MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php')) {
			exit();
		}
		require MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
		ob_start();
		include MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
		$data_setting = ob_get_contents();
		//$data_setting = iconv('gbk','utf-8',$data_setting);
		ob_end_clean();
		$settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
		exit(dr_array2string($settings));
	}
	
	/**
	 * 更新指定表单向导的字段缓存
	 * 
	 * @param $formid 表单向导id
	 * @param $disabled 字段状态
	 */
	public function cache_field($formid = 0) {
		$this->cache_api->sitemodel_field($formid);
	}
}
?>