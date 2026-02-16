<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);
class search_type extends admin {
	private $input,$db,$siteid,$model,$yp_model,$module_db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('type_model');
		$this->siteid = $this->get_siteid();
		$this->model = getcache('model','commons');
		$this->yp_model = getcache('yp_model','model');
		$this->module_db = pc_base::load_model('module_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	public function init() {
		$datas = array();
		$page = $this->input->get('page') && trim($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$result_datas = $this->db->listinfo(array('siteid'=>$this->siteid,'module'=>'search'),'listorder ASC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		foreach($result_datas as $r) {
			$r['modelname'] = $this->model[$r['modelid']]['name'];
			$datas[] = $r;
		}
		$this->cache();
		include $this->admin_tpl('type_list');
	}
	public function add() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$info['siteid'] = $this->siteid;
			$info['module'] = 'search';
			!$info['name'] && dr_admin_msg(0,L('input').L('type_name'), array('field' => 'name'));
			if ($this->db->count(array('name'=>$info['name'], 'siteid'=>$info['siteid'], 'module'=>$info['module']))) {
				dr_admin_msg(0,L('type_name').L('exists'), array('field' => 'name'));
			}
			if($this->input->post('module')=='content') {
				$info['modelid'] = intval($info['modelid']);
				$info['typedir'] = $this->input->post('module');
			} elseif($this->input->post('module')=='yp') {
				$info['modelid'] = intval($info['yp_modelid']);
				$info['typedir'] = $this->input->post('module');				
			} else {
				$info['typedir'] = $this->input->post('module');
				$info['modelid'] = 0;
			}
			
			//删除黄页模型变量无该字段
			unset($info['yp_modelid']);

			$this->db->insert($info);
			dr_admin_msg(1,L('add_success'), '', '', 'add');
		} else {
			$show_header = $show_validator = true;
			
			foreach($this->model as $_key=>$_value) {
				if($_value['siteid']!=$this->siteid) continue;
				$model_data[$_key] = $_value['name'];
			}
			if(is_array($this->yp_model)){
				foreach($this->yp_model as $_key=>$_value) {
					if($_value['siteid']!=$this->siteid) continue;
					$yp_model_data[$_key] = $_value['name'];
				}	
			}
					

			$module_data = array('special' => L('special'),'content' => L('content').L('module'),'yp'=>L('yp'));

			include $this->admin_tpl('type_add');
		}
	}
	public function edit() {
		if(IS_POST) {
			$typeid = intval($this->input->post('typeid'));
			$info = $this->input->post('info');
			!$info['name'] && dr_admin_msg(0,L('input').L('type_name'), array('field' => 'name'));
			if ($this->db->count(array('typeid<>'=>$typeid, 'name'=>$info['name'], 'siteid'=>$this->siteid, 'module'=>'search'))) {
				dr_admin_msg(0,L('type_name').L('exists'), array('field' => 'name'));
			}
			if($this->input->post('module')=='content') {
				$info['modelid'] = intval($info['modelid']);
				$info['typedir'] = $this->input->post('module');
			} elseif($this->input->post('module')=='yp') {
				$info['modelid'] = intval($info['yp_modelid']);
				$info['typedir'] = $this->input->post('module');				
			} else {
				$info['typedir'] = $this->input->post('typedir');
				$info['modelid'] = 0;
			}
				
			//删除黄页模型变量无该字段
			unset($info['yp_modelid']);
	
			$this->db->update($info,array('typeid'=>$typeid));
			dr_admin_msg(1,L('update_success'), '', '', 'edit');
		} else {
			$show_header = $show_validator = true;
			$typeid = intval($this->input->get('typeid'));
			foreach($this->model as $_key=>$_value) {
				if($_value['siteid']!=$this->siteid) continue;
				$model_data[$_key] = $_value['name'];
			}
			foreach($this->yp_model as $_key=>$_value) {
				if($_value['siteid']!=$this->siteid) continue;
				$yp_model_data[$_key] = $_value['name'];
			}
						
			$module_data = array('special' => L('special'),'content' => L('content').L('module'),'yp'=>L('yp'));
			$r = $this->db->get_one(array('typeid'=>$typeid));

			extract($r);
			include $this->admin_tpl('type_edit');
		}
	}
	public function delete() {
		$typeid = intval($this->input->get('typeid'));
		$this->db->delete(array('typeid'=>$typeid));
		dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
	}
	
	/**
	 * 排序
	 */
	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('typeid'=>intval($id)));
				}
			}
			dr_admin_msg(1,L('operation_success'));
		} else {
			dr_admin_msg(0,L('operation_failure'));
		}
	}
	
	public function cache() {
		$this->cache_api->cache('type', 'search');
		return true;
	}
}
?>