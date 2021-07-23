<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class workflow extends admin {
	private $db,$admin_db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('workflow_model');
		$this->admin_db = pc_base::load_model('admin_model');
		$this->siteid = $this->get_siteid();
	}
	
	public function init () {
		$datas = array();
		$result_datas = $this->db->listinfo(array('siteid'=>$this->siteid));
		foreach($result_datas as $r) {
			$datas[] = $r;
		}
		$this->cache();
		include $this->admin_tpl('workflow_list');
	}
	public function add() {
		if($this->input->post('dosubmit')) {
			$info = $this->input->post('info');
			$info['siteid'] = $this->siteid;
			$info['workname'] = safe_replace($info['workname']);
			$setting[1] = $this->input->post('checkadmin1');
			$setting[2] = $this->input->post('checkadmin2');
			$setting[3] = $this->input->post('checkadmin3');
			$setting[4] = $this->input->post('checkadmin4');
			$setting['nocheck_users'] = $this->input->post('nocheck_users');
			$setting = array2string($setting);
			$info['setting'] = $setting;
			
			$this->db->insert($info);
			$this->cache();
			showmessage(L('add_success'), '?m=content&c=workflow&a=init&menuid='.$this->input->get('menuid'));
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
		if($this->input->post('dosubmit')) {
			$workflowid = intval($this->input->post('workflowid'));
			$info = $this->input->post('info');
			$info['workname'] = safe_replace($info['workname']);
			$setting[1] = $this->input->post('checkadmin1');
			$setting[2] = $this->input->post('checkadmin2');
			$setting[3] = $this->input->post('checkadmin3');
			$setting[4] = $this->input->post('checkadmin4');
			$setting['nocheck_users'] = $this->input->post('nocheck_users');
			$setting = array2string($setting);
			$info['setting'] = $setting;
			$this->db->update($info,array('workflowid'=>$workflowid));
			$this->cache();
			showmessage(L('update_success'), '', '', 'edit');
		} else {
			$show_header = $show_validator = '';
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

			$show_header = '';
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
		$datas = array();
		$workflow_datas = $this->db->select(array('siteid'=>$this->siteid),'*',1000);
		foreach($workflow_datas as $_k=>$_v) {
			$datas[$_v['workflowid']] = $_v;
		}
		setcache('workflow_'.$this->siteid,$datas,'commons');
		return true;
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