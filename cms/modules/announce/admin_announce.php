<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class admin_announce extends admin {

	private $input,$db;
	public $username;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->username = param::get_cookie('admin_username');
		$this->db = pc_base::load_model('announce_model');
	}
	
	public function init() {
		//公告列表
		$sql = '';
		$status = $this->input->get('status') ? intval($this->input->get('status')) : 1;
		$sql = '`siteid`=\''.$this->get_siteid().'\'';
		switch($this->input->get('s')) {
			case '1': $sql .= ' AND `passed`=\'1\' AND (`endtime` >= \''.date('Y-m-d').'\' or `endtime`=\'0000-00-00\')'; break;
			case '2': $sql .= ' AND `passed`=\'0\''; break;
			case '3': $sql .= ' AND `passed`=\'1\' AND `endtime`!=\'0000-00-00\' AND `endtime` <\''.date('Y-m-d').'\' '; break;
		}
		$page = max(intval($this->input->get('page')), 1);
		$data = $this->db->listinfo($sql, '`aid` DESC', $page, SYS_ADMIN_PAGESIZE);
		include $this->admin_tpl('announce_list');
	}
	
	/**
	 * 添加公告
	 */
	public function add() {
		if(IS_POST) {
			$announce = $this->check($this->input->post('announce') ,'add');
			if($this->db->insert($announce)) dr_admin_msg(1,L('announcement_successful_added'), HTTP_REFERER, '', 'add');
		} else {
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$siteid = $this->get_siteid();
			$template_list = template_list($siteid, 0);
			$site = pc_base::load_app_class('sites','admin');
			$info = $site->get_by_id($siteid);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_header = $show_validator = $show_scroll = true;
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('announce_add');
		}
	}
	
	/**
	 * 修改公告
	 */
	public function edit() {
		$aid = intval($this->input->get('aid'));
		if(!$aid) dr_admin_msg(0,L('illegal_operation'));
		if(IS_POST) {
			$announce = $this->check($this->input->post('announce'), 'edit');
			if($this->db->update($announce, array('aid' => $aid))) dr_admin_msg(1,L('announced_a'), HTTP_REFERER, '', 'edit');
		} else {
			$where = array('aid' => $this->input->get('aid'));
			$an_info = $this->db->get_one($where);
			pc_base::load_sys_class('form', '', 0);
			//获取站点模板信息
			pc_base::load_app_func('global', 'admin');
			$template_list = template_list($this->siteid, 0);
			foreach ($template_list as $k=>$v) {
				$template_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
				unset($template_list[$k]);
			}
			$show_header = $show_validator = $show_scroll = true;
			include $this->admin_tpl('announce_edit');
		}
	}
	
	/**
	 * 批量修改公告状态 使其成为审核、未审核状态
	 */
	public function public_approval($aid = 0) {
		if((!$this->input->post('aid') || empty($this->input->post('aid'))) && !$aid) {
			dr_admin_msg(0,L('illegal_operation'));
		} else {
			if(is_array($this->input->post('aid')) && !$aid) {
				array_map(array($this, 'public_approval'), $this->input->post('aid'));
				dr_admin_msg(1,L('announce_passed'), HTTP_REFERER);
			} elseif($aid) {
				$aid = intval($aid);
				$this->db->update(array('passed' => $this->input->get('passed')), array('aid' => $aid));
				return true;
			}
		}
	}
	
	/**
	 * 批量删除公告
	 */
	public function delete($aid = 0) {
		if((!$this->input->post('aid') || empty($this->input->post('aid'))) && !$aid) {
			dr_admin_msg(0,L('illegal_operation'));
		} else {
			if(is_array($this->input->post('aid')) && !$aid) {
				array_map(array($this, 'delete'), $this->input->post('aid'));
				dr_admin_msg(1,L('announce_deleted'), HTTP_REFERER);
			} elseif($aid) {
				$aid = intval($aid);
				$this->db->delete(array('aid' => $aid));
			}
		}
	}
	
	/**
	 * 验证表单数据
	 * @param  		array 		$data 表单数组数据
	 * @param  		string 		$a 当表单为添加数据时，自动补上缺失的数据。
	 * @return 		array 		验证后的数据
	 */
	private function check($data = array(), $a = 'add') {
		if(!$data['title']) dr_admin_msg(0,L('title_cannot_empty'), array('field' => 'title'));
		if(!$data['content']) dr_admin_msg(0,L('announcements_cannot_be_empty'), (SYS_EDITOR ? array('field' => 'content', 'jscode' => 'CKEDITOR.instances.content.focus();') : $jscode = array('field' => 'content')));
		if (!$data['style']) dr_admin_msg(0, L('select_style'), array('field' => 'style'));
		$r = $this->db->get_one(array('title' => $data['title']));
		if (strtotime($data['endtime'])<strtotime($data['starttime'])) {
			$data['endtime'] = '';
		}
		if ($a=='add') {
			if (is_array($r) && !empty($r)) {
				dr_admin_msg(0,L('announce_exist'), array('field' => 'title'));
			}
			$data['siteid'] = $this->get_siteid();
			$data['addtime'] = SYS_TIME;
			$data['username'] = $this->username;
			if ($data['starttime'] == '') $announce['starttime'] = date('Y-m-d');
		} else {
			if ($r['aid'] && ($r['aid']!=$this->input->get('aid'))) {
				dr_admin_msg(0,L('announce_exist'), array('field' => 'title'));
			}
		}
		return $data;
	}
}
?>