<?php
defined('IN_CMS') or exit('No permission resources.');
//模型原型存储路径
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin','admin',0);
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util');
class sitemodel_field extends admin {
	private $db,$model_db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$this->siteid = $this->get_siteid();
	}
	
	public function init() {
		$show_header = '';
		$modelid = $this->input->get('modelid');
		$this->cache_field($modelid);
		$datas = $this->db->select(array('modelid'=>$modelid),'*',100,'listorder ASC');
		$r = $this->model_db->get_one(array('modelid'=>$modelid));
		require MODEL_PATH.'fields.inc.php';
		include $this->admin_tpl('sitemodel_field_manage');
	}
	public function add() {
		if($this->input->post('dosubmit')) {
			$model_cache = getcache('model','commons');
			$info = $this->input->post('info');
			$modelid = $info['modelid'];
			if($modelid==-1) {
				$tablename = $this->db->db_tablepre.'category';
				$info['issystem'] = 1;
			} else if($modelid==-2) {
				$tablename = $this->db->db_tablepre.'page';
				$info['issystem'] = 1;
			} else if($modelid) {
				$model_table = $model_cache[$modelid]['tablename'];
				$tablename = $this->input->post('issystem') ? $this->db->db_tablepre.$model_table : $this->db->db_tablepre.$model_table.'_data';
			} else {
				$tablename = $this->db->db_tablepre.'site';
				$info['issystem'] = 1;
			}

			$field = $info['field'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			
			if(isset($this->input->post('setting')['fieldtype'])) {
				$field_type = $this->input->post('setting')['fieldtype'];
			}
			require MODEL_PATH.'add.sql.php';
			//附加属性值
			$info['setting'] = array2string($this->input->post('setting'));
			$info['siteid'] = $this->siteid;
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';
			$this->db->insert($info);
			$this->cache_field($modelid);
			showmessage(L('add_success'),'?m=content&c=sitemodel_field&a=init&modelid='.$modelid.'&menuid=59');
		} else {
			$show_header = $show_validator = $show_dialog = '';
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php';
			$modelid = $this->input->get('modelid');
			if (!$modelid) {
				$not_allow_fields = array('catid','typeid','title','keyword','posid','template','username','groupid','author','readpoint','downfile','copyfrom','pages','wxurl','word');
			} elseif ($modelid==-1) {
				$not_allow_fields = array('catid','typeid','title','keyword','posid','template','username','pages','wxurl','word');
			} elseif ($modelid==-2) {
				$not_allow_fields = array('catid','typeid','title','keyword','posid','template','username','pages');
			}
			$f_datas = $this->db->select(array('modelid'=>$modelid),'field,name',100,'listorder ASC');
			$m_r = $this->model_db->get_one(array('modelid'=>$modelid));
			$exists_field = array();
			foreach($f_datas as $_k=>$_v) {
				$exists_field[] = $_v['field'];
			}

			$all_field = array();
			foreach($fields as $_k=>$_v) {
				if(in_array($_k,$not_allow_fields) || in_array($_k,$exists_field) && in_array($_k,$unique_fields)) continue;
				$all_field[$_k] = $_v;
			}

			$modelid = $this->input->get('modelid');
			//角色缓存
			$roles = getcache('role','commons');
			$grouplist = array();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			include $this->admin_tpl('sitemodel_field_add');
		}
	}
	public function edit() {
		if($this->input->post('dosubmit')) {
			$model_cache = getcache('model','commons');
			$info = $this->input->post('info');
			$modelid = $info['modelid'];
			if($modelid==-1) {
				$tablename = $this->db->db_tablepre.'category';
			} else if($modelid==-2) {
				$tablename = $this->db->db_tablepre.'page';
			} else if($modelid) {
				$model_table = $model_cache[$modelid]['tablename'];
				$tablename = $this->input->post('issystem') ? $this->db->db_tablepre.$model_table : $this->db->db_tablepre.$model_table.'_data';
			} else {
				$tablename = $this->db->db_tablepre.'site';
			}

			$field = $info['field'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			
			if(isset($this->input->post('setting')['fieldtype'])) {
				$field_type = $this->input->post('setting')['fieldtype'];
			}
			$oldfield = $this->input->post('oldfield');
			require MODEL_PATH.'edit.sql.php';
			//附加属性值
			$info['setting'] = array2string($this->input->post('setting'));
			$fieldid = intval($this->input->post('fieldid'));
			
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';
			$this->db->update($info,array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
			$this->cache_field($modelid);
			showmessage(L('update_success'),'?m=content&c=sitemodel_field&a=init&modelid='.$modelid.'&menuid=59');
		} else {
			$show_header = $show_validator = $show_dialog = '';
			pc_base::load_sys_class('form','',0);
			$remote = getcache('attachment', 'commons');
			require MODEL_PATH.'fields.inc.php';
			$modelid = intval($this->input->get('modelid'));
			$fieldid = intval($this->input->get('fieldid'));

			
			$m_r = $this->model_db->get_one(array('modelid'=>$modelid));
			$r = $this->db->get_one(array('fieldid'=>$fieldid));
			extract($r);
			require MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'config.inc.php';
			
			$setting = string2array($setting);
			ob_start();
			include MODEL_PATH.$formtype.DIRECTORY_SEPARATOR.'field_edit_form.inc.php';
			$form_data = ob_get_contents();
			ob_end_clean();
			//角色缓存
			$roles = getcache('role','commons');
			$grouplist = array();
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			header("Cache-control: private");
			include $this->admin_tpl('sitemodel_field_edit');
		}
	}
	public function disabled() {
		$fieldid = intval($this->input->get('fieldid'));
		$disabled = $this->input->get('disabled') ? 0 : 1;
		$this->db->update(array('disabled'=>$disabled),array('fieldid'=>$fieldid,'siteid'=>$this->siteid));
		$modelid = $this->input->get('modelid');
		$this->cache_field($modelid);
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	public function delete() {
		$fieldid = intval($this->input->get('fieldid'));
		$r = $this->db->get_one(array('fieldid'=>$this->input->get('fieldid'),'siteid'=>$this->siteid));
		//必须放在删除字段前、在删除字段部分，重置了 tablename
		$this->db->delete(array('fieldid'=>$this->input->get('fieldid'),'siteid'=>$this->siteid));

		$model_cache = getcache('model','commons');
		$modelid = intval($this->input->get('modelid'));
		if($modelid==-1) {
			$tablename = 'category';
		} else if($modelid==-2) {
			$tablename = 'page';
		} else if($modelid) {
			$model_table = $model_cache[$modelid]['tablename'];
			$tablename = $r['issystem'] ? $model_table : $model_table.'_data';
		} else {
			$tablename = 'site';
		}
		$this->db->drop_field($tablename,$r['field']);
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	/**
	 * 排序
	 */
	public function listorder() {
		if($this->input->post('dosubmit')) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('fieldid'=>$id));
				}
			}
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}
	/**
	 * 检查字段是否存在
	 */
	public function public_checkfield() {
		$field = strtolower($this->input->get('field'));
		$oldfield = strtolower($this->input->get('oldfield'));
		if($field==$oldfield) exit('1');
		$modelid = intval($this->input->get('modelid'));
		if($modelid==-1) {
			$tablename = 'category';
		} else if($modelid==-2) {
			$tablename = 'page';
		} else if($modelid) {
			$model_cache = getcache('model','commons');
			$tablename = $model_cache[$modelid]['tablename'];
			$issystem = intval($this->input->get('issystem'));
		} else {
			$tablename = 'site';
		}
		if($modelid==-1) {
			$this->db->table_name = $this->db->db_tablepre.$tablename;
		} else if($modelid==-2) {
			$this->db->table_name = $this->db->db_tablepre.$tablename;
		} else if($modelid) {
			if($issystem) {
				$this->db->table_name = $this->db->db_tablepre.$tablename;
			} else {
				$this->db->table_name = $this->db->db_tablepre.$tablename.'_data';
			}
		} else {
			$this->db->table_name = $this->db->db_tablepre.$tablename;
		}
		$fields = $this->db->get_fields();
		
		if(array_key_exists($field,$fields)) {
			exit('0');
		} else {
			exit('1');
		}
	}
	/**
	 * 字段属性设置
	 */
	public function public_field_setting() {
		$remote = getcache('attachment', 'commons');
		$fieldtype = $this->input->get('fieldtype');
		require MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
		ob_start();
		include MODEL_PATH.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
		$data_setting = ob_get_contents();
		//$data_setting = iconv('gbk','utf-8',$data_setting);
		ob_end_clean();
		$settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
		echo json_encode($settings);
		return true;
	}
	/**
	 * 更新指定模型字段缓存
	 * 
	 * @param $modelid 模型id
	 */
	public function cache_field($modelid = 0) {
		$field_array = array();
		$fields = $this->db->select(array('modelid'=>$modelid,'disabled'=>$disabled),'*',100,'listorder ASC');
		foreach($fields as $_value) {
			$setting = string2array($_value['setting']);
			$_value = array_merge($_value,$setting);
			$field_array[$_value['field']] = $_value;
		}
		setcache('model_field_'.$modelid,$field_array,'model');
		return true;
	}
	/**
	 * 预览模型
	 */
	public function public_priview() {
		$catid = 0;
		pc_base::load_sys_class('form','',0);
		$show_header = $show_validator = $show_dialog = '';
		$modelid = intval($this->input->get('modelid'));
		require CACHE_MODEL_PATH.'content_form.class.php';
		$content_form = new content_form($modelid);
		$r = $this->model_db->get_one(array('modelid'=>$modelid));
		$forminfos = $content_form->get();
		include $this->admin_tpl('sitemodel_priview');
	}
}
?>