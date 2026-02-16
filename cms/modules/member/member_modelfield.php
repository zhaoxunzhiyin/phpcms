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
		$datas = $this->db->select(array('modelid'=>$modelid),'*','',$this->input->get('order') ? $this->input->get('order') : 'disabled ASC,listorder ASC,fieldid ASC');
		$modelinfo = $this->model_db->get_one(array('modelid'=>$modelid));
		include $this->admin_tpl('member_modelfield_list');
	}
	
	public function add() {
		if(IS_AJAX_POST) {
			$model_cache = getcache('member_model', 'commons');
			$info = $this->input->post('info', false);
			$setting = $this->input->post('setting', false);
			if (!$info['formtype']) dr_json(0, L('select_fieldtype'), array('field' => 'formtype'));
			if (!$info['name']) dr_json(0, L('filed_nickname').L('empty'), array('field' => 'name'));
			if (!$info['field']) dr_json(0, L('fieldname').L('empty'), array('field' => 'field'));
			
			$modelid = intval($info['modelid']);
			$model_table = $model_cache[$modelid]['tablename'];
			$tablename = $this->db->db_tablepre.$model_table;

			$info['issystem'] = 1;
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
			
			if (is_file(MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php')) {
				require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php';
			}
			
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
			dr_json(1, L('operation_success'));
		} else {
			$show_header = $show_validator= $show_dialog = true;
			pc_base::load_sys_class('form', '', 0);
			require MODEL_PATH.'fields.inc.php'; 
			$modelid = $this->input->get('modelid');

			$all_field = array();
			foreach($fields as $_k=>$_v) {
				$all_field[$_k] = $_v.'（'.$_k.'）';
			}
			
			//角色缓存
			$roles = getcache('role','commons');
			//会员组缓存
			$group_cache = getcache('grouplist','member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			
			header("Cache-control: private");
			$reply_url = '?m=member&c=member_modelfield&a=manage&modelid='.$modelid.'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('member_modelfield_add');
		}
	}
	
	/**
	 * 修改
	 */
	public function edit() {
		if(IS_AJAX_POST) {
			$model_cache = getcache('member_model','commons');
			$info = $this->input->post('info', false);
			$setting = $this->input->post('setting', false);
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
			
			if (is_file(MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php')) {
				require MODEL_PATH.$field_type.DIRECTORY_SEPARATOR.'edit_config.inc.php';
			}
			
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
			dr_json(1, L('operation_success'));
		} else {
			$show_header = $show_validator= $show_dialog = true;
			pc_base::load_sys_class('form','',0);
			require MODEL_PATH.'fields.inc.php'; 
			$modelid = intval($this->input->get('modelid'));
			$fieldid = intval($this->input->get('fieldid'));
			$all_field = array();
			foreach($fields as $_k=>$_v) {
				$all_field[$_k] = $_v.'（'.$_k.'）';
			}
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
			$reply_url = '?m=member&c=member_modelfield&a=manage&modelid='.$modelid.'&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
			include $this->admin_tpl('member_modelfield_edit');
		}
	}
	
	public function delete() {
		$modelid = intval($this->input->get('modelid'));
		$ids = $this->input->get_post_ids();
		if (!$ids) {
		    dr_json(0, L('你还没有选择呢'));
        }
		foreach ($ids as $id) {
			$r = $this->db->get_one(array('fieldid'=>$id));

			//删除模型字段
			$this->db->delete(array('fieldid'=>$id));

			//删除表字段
			$model_cache = getcache('member_model', 'commons');

			$model_table = $model_cache[$r['modelid']]['tablename'];

			$this->db->drop_field($model_table, $r['field']);
		}
		$this->cache_field($modelid);
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 *  禁用字段
	 */
	public function disable() {
		$modelid = intval($this->input->get('modelid'));
		$fieldid = intval($this->input->get('fieldid'));
		$r = $this->db->get_one(array('fieldid'=>$fieldid));
		$value = $r['disabled'] ? 0 : 1;
		$this->db->update(array('disabled'=>$value), array('fieldid'=>$fieldid));
		$this->cache_field($modelid);
		dr_json(1, L(($value ? '禁' : '启').'用成功'), array('value' => $value));
	}
	
	/**
	 *  必填字段
	 */
	public function public_isbase() {
		$modelid = intval($this->input->get('modelid'));
		$fieldid = intval($this->input->get('fieldid'));
		$r = $this->db->get_one(array('fieldid'=>$fieldid));
		$value = $r['isbase'] ? 0 : 1;
		$this->db->update(array('isbase'=>$value), array('fieldid'=>$fieldid));
		$this->cache_field($modelid);
		dr_json(1, L(($value ? '启' : '禁').'用成功'), array('value' => $value));
	}
	
	/**
	 * 排序
	 */
	public function sort() {
		$modelid = intval($this->input->get('modelid'));
		$fieldid = intval($this->input->get('fieldid'));
		// 查询数据
		$r = $this->db->get_one(array('modelid'=>$modelid,'fieldid'=>$fieldid));
		if (!$r) {
			dr_json(0, L('数据#'.$fieldid.'不存在'));
		}
		$value = (int)$this->input->get('value');
		$this->db->update(array('listorder'=>$value),array('modelid'=>$modelid,'fieldid'=>$fieldid));
		$this->cache_field($modelid);
		dr_json(1, L('操作成功'));
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
}
?>