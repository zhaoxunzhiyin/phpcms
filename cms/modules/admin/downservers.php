<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form', '', 0);
class downservers extends admin {
	private $input,$db,$sites,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('downservers_model');
		$this->sites = pc_base::load_app_class('sites');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	public function init() {
		$show_header = true;
		if(IS_POST) {
			$info['siteurl'] = trim($this->input->post('info')['siteurl']);
			$info['sitename'] = trim($this->input->post('info')['sitename']);
			$info['siteid'] = intval($this->input->post('info')['siteid']);
			if(empty($info['sitename'])) dr_admin_msg(0,L('downserver_name').L('downserver_not_empty'), HTTP_REFERER);	
			if(empty($info['siteurl']) || !preg_match('/(\w+):\/\/(.+)[^\/]$/i', $info['siteurl'])) dr_admin_msg(0,L('downserver_error'), HTTP_REFERER);
			$insert_id = $this->db->insert($info,true);
			if($insert_id){
				$this->_set_cache();
				dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
			}
		} else {
			$infos = $sitelist = array();
			$current_siteid = get_siteid();
			$where = "`siteid`='$current_siteid' or `siteid`=''";
			$sitelists = $this->sites->get_list();
			if(cleck_admin(param::get_session('roleid'))) {
				foreach($sitelists as $key=>$v) $sitelist[$key] = $v['name'];
				$default = L('all_site');
			} else {
				$sitelist[$current_siteid] = $sitelists[$current_siteid]['name'];
				$default = '';
			}			
			$page = $this->input->get('page') ? $this->input->get('page') : '1';
			$infos = $this->db->listinfo($where, 'listorder DESC,id DESC', $page, SYS_ADMIN_PAGESIZE);
			$pages = $this->db->pages;						
			include $this->admin_tpl('downservers_list');
		}
	}
	
	public function edit() {
		if(IS_POST) {
			$info = $this->input->post('info');
			!$info['sitename'] && dr_json(0, L('downserver_name').L('downserver_not_empty'), array('field' => 'sitename'));
			if(empty($info['siteurl']) || !preg_match('/(\w+):\/\/(.+)[^\/]$/i', $info['siteurl'])) dr_json(0, L('downserver_error'), array('field' => 'siteurl'));
			$id = intval(trim($this->input->post('id')));
			$this->db->update($info,array('id'=>$id));
			$this->_set_cache();
			dr_admin_msg(1,L('operation_success'), '', '', 'edit');
		} else {
			$info = $sitelist = array();
			$default = '';
			$sitelists = $this->sites->get_list();
			if(cleck_admin(param::get_session('roleid'))) {
				foreach($sitelists as $key=>$v) $sitelist[$key] = $v['name'];
				$default = L('all_site');
			} else {
				$current_siteid = self::get_siteid();
				$sitelist[$current_siteid] = $sitelists[$current_siteid]['name'];
				$default = '';
			}			
			$info = $this->db->get_one(array('id'=>intval($this->input->get('id'))));
			extract($info);
			$show_validator = $show_header = true;
			include $this->admin_tpl('downservers_edit');
		}
	}
	
	public function delete() {
		$id = intval($this->input->get('id'));
		$this->db->delete(array('id'=>$id));
		$this->_set_cache();
		dr_admin_msg(1,L('downserver_del_success'), HTTP_REFERER);
	}	
	
	/**
	 * 排序
	 */
	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
				}
				$this->_set_cache();
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}	
	
	private function _set_cache() {
		$this->cache_api->cache('downservers');
	}
	
}
?>