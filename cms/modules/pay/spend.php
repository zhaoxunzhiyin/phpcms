<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class spend extends admin {
	private $input,$db;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('pay_spend_model');
	}
	
	public function init() {
		pc_base::load_sys_class('form', '', 0);
		pc_base::load_sys_class('format', '', 0);
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$sql =  "";
		if ($this->input->get('dosubmit')) {
			$username = $this->input->get('username') && trim($this->input->get('username')) ? trim($this->input->get('username')) : '';
			$op = $this->input->get('op') && trim($this->input->get('op')) ? trim($this->input->get('op')) : '';
			$user_type = $this->input->get('user_type') && intval($this->input->get('user_type')) ? intval($this->input->get('user_type')) : '';
			$op_type = $this->input->get('op_type') && intval($this->input->get('op_type')) ? intval($this->input->get('op_type')) : '';
			$type = $this->input->get('type') && intval($this->input->get('type')) ? intval($this->input->get('type')) : '';
			$endtime = $this->input->get('endtime')  &&  trim($this->input->get('endtime')) ? strtotime(trim($this->input->get('endtime'))) : '';
			$starttime = $this->input->get('starttime') && trim($this->input->get('starttime')) ? strtotime(trim($this->input->get('starttime'))) : '';
			
			if (!empty($starttime) && empty($endtime)) {
				$endtime = SYS_TIME;
			}
			
			if (!empty($starttime) && !empty($endtime) && $endtime < $starttime) {
				dr_admin_msg(0,L('wrong_time_over_time_to_time_less_than'));
			}
			
			
			if (!empty($username) && $user_type == 1) {
				$sql .= $sql ? " AND `username` = '$username'" : " `username` = '$username'";
			}
			
			if (!empty($username) && $user_type == 2) {
				$sql .= $sql ? " AND `userid` = '$username'" : " `userid` = '$username'";
			}
			
			if (!empty($starttime)) {
				$sql .= $sql ? " AND `creat_at` BETWEEN '$starttime' AND '$endtime' " : " `creat_at` BETWEEN '$starttime' AND '$endtime' ";
			}
			
			if (!empty($op) && $op_type == 1) {
				$sql .= $sql ? " AND `op_username` = '$op' " : " `op_username` = '$op' ";
			} elseif (!empty($op) && $op_type == 2) {
				$sql .= $sql ? " AND `op_userid` = '$op' " : " `op_userid` = '$op' ";
			}
			
			if (!empty($type)) {
				$sql .= $sql ? " AND `type` = '$type' " : " `type` = '$type'";
			}
		}
		$list = $this->db->listinfo($sql, '`id` desc', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('spend_list');
	}
}