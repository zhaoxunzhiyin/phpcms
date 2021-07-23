<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class copyfrom extends admin {
	private $db;
	public $siteid;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('copyfrom_model');
		pc_base::load_sys_class('form', '', 0);
		parent::__construct();
		$this->siteid = $this->get_siteid();
	}
	
	/**
	 * 来源管理列表
	 */
	public function init () {
		$datas = array();
		$datas = $this->db->listinfo(array('siteid'=>$this->siteid),'listorder ASC',$this->input->get('page'));
		$pages = $this->db->pages;

		$big_menu = array('javascript:artdialog(\'add\',\'?m=admin&c=copyfrom&a=add\',\''.L('add_copyfrom').'\',580,240);void(0);', L('add_copyfrom'));
		$this->public_cache();
		include $this->admin_tpl('copyfrom_list');
	}
	
	/**
	 * 添加来源
	 */
	public function add() {
		if($this->input->post('dosubmit')) {
			$info = $this->check($this->input->post('info'));
			$this->db->insert($info);
			showmessage(L('add_success'), '', '', 'add');
		} else {
			$show_header = $show_validator = '';
			
			include $this->admin_tpl('copyfrom_add');
		}
	}
	
	/**
	 * 管理来源
	 */
	public function edit() {
		if($this->input->post('dosubmit')) {
			$id = intval($this->input->post('id'));
			$info = $this->check($this->input->post('info'));
			$this->db->update($info,array('id'=>$id));
			showmessage(L('update_success'), '', '', 'edit');
		} else {
			$show_header = $show_validator = '';
			$id = intval($this->input->get('id'));
			if (!$id) showmessage(L('illegal_action'));
			$r = $this->db->get_one(array('id'=>$id, 'siteid'=>$this->siteid));
			if (empty($r)) showmessage(L('illegal_action'));
			extract($r);
			include $this->admin_tpl('copyfrom_edit');
		}
	}
	
	/**
	 * 删除来源
	 */
	public function delete() {
		$id = intval($this->input->get('id'));
		if (!$id) showmessage(L('illegal_action'));
		$this->db->delete(array('id'=>$id, 'siteid'=>$this->siteid));
		exit('1');
	}
	
	/**
	 * 检查POST数据
	 * @param array $data 前台POST数据
	 * @return array $data
	 */
	private function check($data = array()) {
		if (!is_array($data) || empty($data)) return array();
		if (!preg_match('/^((http|https):\/\/)?([^\/]+)/i', $data['siteurl'])) showmessage(L('input').L('copyfrom_url'));
		if (empty($data['sitename'])) showmessage(L('input').L('copyfrom_name'));
		if ($data['thumb'] && !preg_match('/^((http|https):\/\/)?([^\/]+)/i', $data['thumb'])) showmessage(L('copyfrom_logo').L('format_incorrect'));
		$data['siteid'] = $this->siteid;
		return $data;
	}
	
	/**
	 * 排序
	 */
	public function listorder() {
		if($this->input->post('dosubmit')) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
				}
			}
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			showmessage(L('operation_failure'));
		}
	}

	/**
	 * 生成缓存
	 */
	public function public_cache() {
		$infos = $this->db->select('','*','','listorder DESC','','id');
		setcache('copyfrom', $infos, 'admin');
		return true;
 	}
}
?>