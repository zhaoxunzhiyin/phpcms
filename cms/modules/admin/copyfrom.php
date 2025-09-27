<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class copyfrom extends admin {
	private $input,$db,$cache_api;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('copyfrom_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		pc_base::load_sys_class('form', '', 0);
		$this->siteid = $this->get_siteid();
	}
	
	/**
	 * 来源管理列表
	 */
	public function init () {
		$datas = array();
		$datas = $this->db->listinfo(array('siteid'=>$this->siteid),'listorder ASC',$this->input->get('page'),SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;

		$this->public_cache();
		include $this->admin_tpl('copyfrom_list');
	}
	
	/**
	 * 添加来源
	 */
	public function add() {
		if(IS_POST) {
			$info = $this->check($this->input->post('info'));
			$this->db->insert($info);
			dr_admin_msg(1, L('add_success'), '', '', 'add');
		} else {
			$show_header = $show_validator = true;
			include $this->admin_tpl('copyfrom_add');
		}
	}
	
	/**
	 * 管理来源
	 */
	public function edit() {
		if(IS_POST) {
			$id = intval($this->input->post('id'));
			$info = $this->check($this->input->post('info'));
			$this->db->update($info,array('id'=>$id));
			dr_admin_msg(1, L('update_success'), '', '', 'edit');
		} else {
			$show_header = $show_validator = true;
			$id = intval($this->input->get('id'));
			if (!$id) dr_admin_msg(0, L('illegal_action'));
			$r = $this->db->get_one(array('id'=>$id, 'siteid'=>$this->siteid));
			if (empty($r)) dr_admin_msg(0, L('illegal_action'));
			extract($r);
			include $this->admin_tpl('copyfrom_edit');
		}
	}
	
	/**
	 * 删除来源
	 */
	public function delete() {
		$id = intval($this->input->get('id'));
		if (!$id) dr_admin_msg(0, L('illegal_action'));
		$this->db->delete(array('id'=>$id, 'siteid'=>$this->siteid));
		exit('1');
	}
	
	/**
	 * 检查POST数据
	 * @param array $data 前台POST数据
	 * @return array $data
	 */
	private function check($data = array()) {
		if (empty($data['sitename'])) dr_admin_msg(0, L('input').L('copyfrom_name'), array('field' => 'sitename'));
		if (!preg_match('/^((http|https):\/\/)?([^\/]+)/i', $data['siteurl'])) dr_admin_msg(0, L('input').L('copyfrom_url'), array('field' => 'siteurl'));
		if ($data['thumb'] && !preg_match('/^((http|https):\/\/)?([^\/]+)/i', $data['thumb'])) dr_admin_msg(0, L('copyfrom_logo').L('format_incorrect'));
		$data['siteid'] = $this->siteid;
		return $data;
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
			}
			dr_admin_msg(1, L('operation_success'),HTTP_REFERER);
		} else {
			dr_admin_msg(0, L('operation_failure'));
		}
	}

	/**
	 * 生成缓存
	 */
	public function public_cache() {
		$this->cache_api->cache('copyfrom');
 	}
}
?>