<?php
/**
 * 管理员后台会员模型字段操作类
 */

defined('IN_CMS') or exit('No permission resources.');
//模型原型存储路径
define('MODEL_PATH',PC_PATH.'modules'.DIRECTORY_SEPARATOR.'member'.DIRECTORY_SEPARATOR.'fields'.DIRECTORY_SEPARATOR);
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_class('admin', 'admin', 0);

class member_modelfield extends admin {
	
	private $input,$db,$model_db,$cache_api,$siteid;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->model_db = pc_base::load_model('sitemodel_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->siteid = $this->get_siteid();
	}
	
	public function manage() {
		$show_header = $show_validator = $show_dialog = true;
		$modelid = $this->input->get('modelid');
		$this->cache_field($modelid);
		$datas = $this->db->select(array('modelid'=>$modelid),'*',100,$this->input->get('order') ? $this->input->get('order') : 'listorder ASC,fieldid ASC');
		$modelinfo = $this->model_db->get_one(array('modelid'=>$modelid));
		$big_menu = array('?m=member&c=member_modelfield&a=add&modelid='.$modelinfo['modelid'].'&menuid='.$this->input->get('menuid').'', L('member_modelfield_add'));
		include $this->admin_tpl('member_modelfield_list');
	}
	
	public function add() {
		if(IS_AJAX_POST) {
			$model_cache = getcache('member_model', 'commons');
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			if (!$info['formtype']) dr_json(0, L('select_fieldtype'), array('field' => 'formtype'));
			if (!$info['name']) dr_json(0, L('filed_nickname').L('empty'), array('field' => 'name'));
			if (!$info['field']) dr_json(0, L('fieldname').L('empty'), array('field' => 'field'));
			
			$modelid = intval($info['modelid']);
			$model_table = $model_cache[$modelid]['tablename'];
			$tablename = $this->db->db_tablepre.$model_table;

			$field = $info['field'];
			$cname = $info['name'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			$checkmobile = $this->db->get_one(array('modelid'=>$modelid, 'formtype'=>'checkmobile'));
			if ($checkmobile) dr_json(0, L('filedtype').'（短信验证）'.L('already_exist').'，只能一个短信验证', array('field' => 'formtype'));
			$model_field = $this->db->get_one(array('modelid'=>$modelid, 'field'=>$field));
			if (!$model_field) {
				$field_rs = $this->db->query('SHOW FULL COLUMNS FROM `'.$tablename.'`');
				foreach ($field_rs as $rs) {
					if ($rs['Field']==$field) {
						$model_field = 1;
					}
				}
			}
			if ($model_field) dr_json(0, L('fieldname').'（'.$field.'）'.L('already_exist'), array('field' => 'field'));
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			
			if(isset($setting['fieldtype'])) {
				$field_type = $setting['fieldtype'];
			}
			
			require MODEL_PATH.'add.sql.php';
			//附加属性值
			$info['setting'] = array2string($setting);
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';

			$this->db->insert($info);
			$this->cache_field($modelid);
			dr_json(1, L('operation_success'), array('url' => '?m=member&c=member_modelfield&a=manage&modelid='.$modelid.'&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			$show_header = $show_validator= $show_dialog = true;
			pc_base::load_sys_class('form', '', 0);
			require MODEL_PATH.'fields.inc.php'; 
			$modelid = $this->input->get('modelid');
			
			//角色缓存
			$roles = getcache('role','commons');
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			
			header("Cache-control: private");
			include $this->admin_tpl('member_modelfield_add');
		}
	}
	
	/**
	 * 修改
	 */
	public function edit() {
		if(IS_AJAX_POST) {
			$model_cache = getcache('member_model','commons');
			$info = $this->input->post('info');
			$setting = $this->input->post('setting');
			if (!$info['formtype']) dr_json(0, L('select_fieldtype'), array('field' => 'formtype'));
			if (!$info['name']) dr_json(0, L('filed_nickname').L('empty'), array('field' => 'name'));
			if (!$info['field']) dr_json(0, L('fieldname').L('empty'), array('field' => 'field'));
			$fieldid = intval($this->input->post('fieldid'));
			$modelid = intval($info['modelid']);
			$model_table = $model_cache[$modelid]['tablename'];

			$tablename = $this->db->db_tablepre.$model_table;

			$field = $info['field'];
			$cname = $info['name'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			
			require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			
			if(isset($setting['fieldtype'])) {
				$field_type = $setting['fieldtype'];
			}
			$oldfield = $this->input->post('oldfield');
			$model_field = $this->db->get_one(array('modelid'=>$modelid, 'field'=>$field, 'fieldid<>'=>$fieldid));
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
			//附加属性值
			$info['setting'] = array2string($setting);
			$info['unsetgroupids'] = $this->input->post('unsetgroupids') ? implode(',',$this->input->post('unsetgroupids')) : '';
			$info['unsetroleids'] = $this->input->post('unsetroleids') ? implode(',',$this->input->post('unsetroleids')) : '';
			$this->db->update($info,array('fieldid'=>$fieldid));
			$this->cache_field($modelid);
			
			//更新模型缓存
			pc_base::load_app_class('member_cache','','');
			member_cache::update_cache_model();
			
			dr_json(1, L('operation_success'), array('url' => '?m=member&c=member_modelfield&a=manage&modelid='.$modelid.'&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		} else {
			$show_header = $show_validator= $show_dialog = true;
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php'; 
			$modelid = intval($this->input->get('modelid'));
			$fieldid = intval($this->input->get('fieldid'));
			$r = $this->db->get_one(array('fieldid'=>$fieldid));
			extract($r);
			if($unsetgroupids != '') $unsetgroupids = strpos($unsetgroupids, ',') ? explode(',', $unsetgroupids) : array($unsetgroupids);
			if($unsetroleids != '') $unsetroleids = strpos($unsetroleids, ',') ? explode(',', $unsetroleids) : array($unsetroleids);
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
			include $this->admin_tpl('member_modelfield_edit');
		}
	}
	
	public function delete() {
		$fieldid = intval($this->input->get('fieldid'));
		$r = $this->db->get_one(array('fieldid'=>$fieldid));
		
		//删除模型字段
		$this->db->delete(array('fieldid'=>$fieldid));
		
		//删除表字段
		$model_cache = getcache('member_model', 'commons');
		
		$model_table = $model_cache[$r['modelid']]['tablename'];

		$this->db->drop_field($model_table, $r['field']);
		
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 *  禁用字段
	 */
	public function disable() {
		$fieldid = intval($this->input->get('fieldid'));
		$disabled = intval($this->input->get('disabled'));
		$this->db->update(array('disabled'=>$disabled), array('fieldid'=>$fieldid));
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 排序
	 */
	public function sort() {
		if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
			foreach($this->input->post('listorders') as $id => $listorder) {
				$this->db->update(array('listorder'=>$listorder),array('fieldid'=>$id));
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}
	
	/**
	 * 检查字段是否存在
	 */
	public function public_checkfield() {
		$field = strtolower($this->input->get('field'));
		$fieldid = intval($this->input->get('fieldid'));
		//$oldfield = strtolower($this->input->get('oldfield'));
		//if($field==$oldfield) exit('1');
		$modelid = intval($this->input->get('modelid'));
		/*$model_cache = getcache('member_model','commons');
		$tablename = $model_cache[$modelid]['tablename'];
		$this->db->table_name = $this->db->db_tablepre.$tablename;

		$fields = $this->db->get_fields();*/
		$where = 'modelid='.$modelid.' AND field=\''.$field.'\'';
		if ($fieldid) {
			$where .= ' AND fieldid<>'.$fieldid;
		}
		$fields = $this->db->get_one($where);
		
		if($fields) {
		//if(array_key_exists($field, $fields)) {
			exit('0');
		} else {
			exit('1');
		}
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
		ob_end_clean();
		$settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
		exit(dr_array2string($settings));
	}
	
	/**
	 * 更新指定模型字段缓存
	 * 
	 * @param $modelid 模型id
	 */
	public function cache_field($modelid = 0) {
		$this->cache_api->sitemodel_field($modelid);
	}
	
	/**
	 * 预览模型
	 */
	public function public_priview() {
		pc_base::load_sys_class('form','',0);
		$show_header = true;
		$modelid = intval($this->input->get('modelid'));

		require CACHE_MODEL_PATH.'content_form.class.php';
		$content_form = new content_form($modelid);
		$forminfos = $content_form->get();
		include $this->admin_tpl('sitemodel_priview');
	}
	/**
	 * 汉字转换拼音
	 */
	public function public_ajax_pinyin() {
		$pinyin = pc_base::load_sys_class('pinyin');
		$name = dr_safe_replace($this->input->get('name'));
		if (!$name) {
			exit('');
		}
		$py = $pinyin->result($name);
		if (strlen($py) > 12) {
			$sx = $pinyin->result($name, 0);
			if ($sx) {
				exit($sx);
			}
		}
		exit($py);
	}
}
?>