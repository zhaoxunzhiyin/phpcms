<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class workflow extends admin {
	private $input,$db,$admin_db,$cache_api;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('workflow_model');
		$this->admin_db = pc_base::load_model('admin_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->siteid = $this->get_siteid();
	}
	
	public function init () {
		$datas = array();
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$result_datas = $this->db->listinfo(array('siteid'=>$this->siteid),'',$page,SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		foreach($result_datas as $r) {
			$datas[] = $r;
		}
		$this->cache();
		include $this->admin_tpl('workflow_list');
	}
	public function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$info['siteid'] = $this->siteid;
			$info['workname'] = safe_replace($info['workname']);
			dr_is_empty($info['workname']) && dr_json(0, L('input').L('workflow_name'), array('field' => 'workname'));
			if ($this->db->count(array('workname'=>$info['workname'], 'siteid'=>$info['siteid']))) {
				dr_admin_msg(0,L('workflow_name').L('exists'), array('field' => 'workname'));
			}
			$setting[1] = $this->input->post('checkadmin1');
			$setting[2] = $this->input->post('checkadmin2');
			$setting[3] = $this->input->post('checkadmin3');
			$setting[4] = $this->input->post('checkadmin4');
			$setting['nocheck_users'] = $this->input->post('nocheck_users');
			$setting = array2string($setting);
			$info['setting'] = $setting;
			
			$this->db->insert($info);
			$this->cache();
			dr_admin_msg(1,L('add_success'), '?m=content&c=workflow&a=init&menuid='.$this->input->get('menuid'));
		} else {
			$show_validator = '';
			$admin_data = array();
			$result = $this->admin_db->select();
			foreach($result as $_value) {
				if($_value['roleid']==1) continue;
				$admin_data[$_value['username']] = $_value['username'];
			}
			include $this->admin_tpl('workflow_add');
		}
	}
	public function edit() {
		if(IS_POST) {
			$workflowid = intval($this->input->post('workflowid'));
			$info = $this->input->post('info');
			$info['workname'] = safe_replace($info['workname']);
			dr_is_empty($info['workname']) && dr_json(0, L('input').L('workflow_name'), array('field' => 'workname'));
			if ($this->db->count(array('workflowid<>'=>$workflowid, 'workname'=>$info['workname'], 'siteid'=>$this->siteid))) {
				dr_admin_msg(0,L('workflow_name').L('exists'), array('field' => 'workname'));
			}
			$setting[1] = $this->input->post('checkadmin1');
			$setting[2] = $this->input->post('checkadmin2');
			$setting[3] = $this->input->post('checkadmin3');
			$setting[4] = $this->input->post('checkadmin4');
			$setting['nocheck_users'] = $this->input->post('nocheck_users');
			$setting = array2string($setting);
			$info['setting'] = $setting;
			$this->db->update($info,array('workflowid'=>$workflowid));
			$this->cache();
			dr_admin_msg(1,L('update_success'), '', '', 'edit');
		} else {
			$show_header = $show_validator = true;
			$workflowid = intval($this->input->get('workflowid'));
			$admin_data = array();
			$result = $this->admin_db->select();
			foreach($result as $_value) {
				if($_value['roleid']==1) continue;
				$admin_data[$_value['username']] = $_value['username'];
			}
			$r = $this->db->get_one(array('workflowid'=>$workflowid));
			extract($r);
			$setting = string2array($setting);

			$checkadmin1 = $this->implode_ids($setting[1]);
			$checkadmin2 = $this->implode_ids($setting[2]);
			$checkadmin3 = $this->implode_ids($setting[3]);
			$checkadmin4 = $this->implode_ids($setting[4]);
			$nocheck_users = $this->implode_ids($setting['nocheck_users']);
			
			include $this->admin_tpl('workflow_edit');
		}
	}
	public function view() {

			$show_header = true;
			$workflowid = intval($this->input->get('workflowid'));
			$admin_data = array();
			$result = $this->admin_db->select();
			foreach($result as $_value) {
				if($_value['roleid']==1) continue;
				$admin_data[$_value['username']] = $_value['username'];
			}
			$r = $this->db->get_one(array('workflowid'=>$workflowid));
			extract($r);
			$setting = string2array($setting);

			$checkadmin1 = $this->implode_ids($setting[1],'、');
			$checkadmin2 = $this->implode_ids($setting[2],'、');
			$checkadmin3 = $this->implode_ids($setting[3],'、');
			$checkadmin4 = $this->implode_ids($setting[4],'、');
			
			include $this->admin_tpl('workflow_view');
	}
	public function delete() {
		$workflowid = intval($this->input->get('workflowid'));
		$this->db->delete(array('workflowid'=>$workflowid));
		$this->cache();
		exit('1');
	}
	
	
	public function cache() {
		$this->cache_api->cache('workflow');
	}
	/**
	 * 用逗号分隔数组
	 */
	private function implode_ids($array, $flags = ',') {
		if(empty($array)) return true;
		$length = strlen($flags);
		$string = '';
		foreach($array as $_v) {
			$string .= $_v.$flags;
		}
		return substr($string,0,-$length);
	}
}
?>