<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class formguide_info extends admin {
	
	private $input, $cache, $db, $f_db, $tablename, $form, $sitemodel, $form_cache, $field, $list_field;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('sitemodel_field_model');
		$this->f_db = pc_base::load_model('sitemodel_model');
		if ($this->input->get('formid') && !empty($this->input->get('formid'))) {
			$formid = intval($this->input->get('formid'));
			$this->form = $this->f_db->get_one(array('modelid'=>$formid));
			$this->sitemodel = $this->cache->get('sitemodel');
			$this->form_cache = $this->sitemodel[$this->form['tablename']];
			$this->field = $this->form_cache['field'];
			$this->list_field = $this->form_cache['setting']['list_field'];
			$f_info = $this->f_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
			$this->tablename = 'form_'.$f_info['tablename'];
			$this->db->change_table($this->tablename);
		}
	}
	
	/**
	 * 用户提交表单信息列表
	 */
	public function init() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
		$formid = intval($this->input->get('formid'));
		if (!$this->tablename) {
			$f_info = $this->f_db->get_one(array('modelid'=>$formid, 'siteid'=>$this->get_siteid()), 'tablename');
			$this->tablename = 'form_'.$f_info['tablename'];
			$this->db->change_table($this->tablename);
		}
		$field = $this->field;
		$list_field = $this->list_field;
		$page = max(intval($this->input->get('page')), 1);
		$total = $this->db->count();;
		$this->f_db->update(array('items'=>$total), array('modelid'=>$formid));
		$pages = pages($total, $page, 20);
		$offset = ($page-1)*20;
		$datas = $this->db->select(array(), '*', $offset.', 20', $this->input->get('order') ? $this->input->get('order') : '`dataid` DESC');
		$big_menu = array('javascript:artdialog(\'add\',\'?m=formguide&c=formguide&a=add\',\''.L('formguide_add').'\',700,500);void(0);', L('formguide_add'));
		include $this->admin_tpl('formguide_info_list');
	}
	
	/**
	 * 查看
	 */
	public function public_view() {
		if (!$this->tablename || !$this->input->get('did') || empty($this->input->get('did'))) dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		$did = intval($this->input->get('did'));
		$formid = intval($this->input->get('formid'));
		$info = $this->db->get_one(array('dataid'=>$did));
		pc_base::load_sys_class('form', '', '');
		define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
		require CACHE_MODEL_PATH.'formguide_output.class.php';
		$formguide_output = new formguide_output($formid);
		$forminfos_data = $formguide_output->get($info);
		$fields = $formguide_output->fields;
		include $this->admin_tpl('formguide_info_view');
	}
	
	/**
	 * 删除
	 */
	public function public_delete() {
		$formid = intval($this->input->get('formid'));
		if ($this->input->get('did') && !empty($this->input->get('did'))) {
			$did = intval($this->input->get('did'));
			$this->db->delete(array('dataid'=>$did));
			$this->f_db->update(array('items'=>'-=1'), array('modelid'=>$formid));
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else if(is_array($this->input->post('did')) && !empty($this->input->post('did'))) {
			foreach ($this->input->post('did') as $did) {
				$did = intval($did);
				$this->db->delete(array('dataid'=>$did));
				$this->f_db->update(array('items'=>'-=1'), array('modelid'=>$formid));
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('illegal_operation'), HTTP_REFERER);
		}
	}
}
?>