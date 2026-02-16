<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class log extends admin {
	private $input,$db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('log_model');
		pc_base::load_sys_class('form');
		$admin_username = param::get_cookie('admin_username');
		$userid = param::get_session('userid');
	}
	
	function init () {
        $show_header = true;
		if ($this->input->get('search')){
			extract($this->input->get('search'),EXTR_SKIP);
		}
		if($username){
			$where[] = "username='$username'";
		}
		if ($module){
			$where[] = "module='$module'";
		}
		if($start_time) {
			$where[] = '`time` BETWEEN ' . max((int)strtotime(strpos($start_time, ' ') ? $start_time : $start_time.' 00:00:00'), 1) . ' AND ' . ($end_time ? (int)strtotime(strpos($end_time, ' ') ? $end_time : $end_time.' 23:59:59') : SYS_TIME);
		}
 
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1; 
		$infos = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),'logid DESC',$page, SYS_ADMIN_PAGESIZE); 
 		$pages = $this->db->pages;
 		//模块数组
		$module_arr = array();
		$modules = getcache('modules','commons');
		$default = $module ? $module : L('open_module');
 		foreach($modules as $module=>$m) $module_arr[$m['module']] = $m['module'];
 		include $this->admin_tpl('log_list');
	}
		
	/**
	 * 操作日志删除 包含批量删除 单个删除
	 */
	function delete() {
		$week = intval($this->input->get('week'));
		if(IS_AJAX_POST){
			if($week){
				$start = SYS_TIME - $week*7*24*3600;
				$d = date("Y-m-d",$start);
				$where = "`time` <= '$d'";
				$this->db->delete($where);
				dr_json(1, L('operation_success'));
			} else {
				$this->db->query('TRUNCATE `'.$this->db->table_name.'`');
				dr_json(1, L('operation_success'));
			}
		}
	}
	
}
?>