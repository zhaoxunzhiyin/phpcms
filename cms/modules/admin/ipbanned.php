<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class ipbanned extends admin {
	private $input,$db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('ipbanned_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		pc_base::load_sys_class('form', '', 0);
	}
	
	function init () {
		$where = array();
		if($this->input->get('search')) extract($this->input->get('search'));
		if($ip){
			$where[] = "ip LIKE '%$ip%'";
		}
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo(($where ? implode(' AND ', $where) : ''),'ipbannedid DESC',$page,SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('ipbanned_list');
	}
	
	/**
	 * 验证数据有效性
	 */
	public function public_name() {
		$ip = $this->input->get('ip') && trim($this->input->get('ip')) ? (CHARSET == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('ip'))) : trim($this->input->get('ip'))) : exit('0');
 		//添加判断IP是否重复
		if ($this->db->get_one(array('ip'=>$ip), 'ipbannedid')) {
			exit('0');
		} else {
			exit('1');
		}
	}
		
	/**
	 * IP添加
	 */
	function add() {
		if(IS_POST){
			$info = $this->input->post('info');
			!$info['ip'] && dr_json(0, L('input').L('ipbanned'), array('field' => 'ip'));
			!$info['expires'] && dr_json(0, L('input').L('deblocking_time'), array('field' => 'expires'));
  			$info['expires']=strtotime($info['expires']);
			$this->db->insert($info);
			$this->public_cache_file();//更新缓存 
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=ipbanned&a=add','', 'add');
		}else{
			$show_validator = $show_scroll = $show_header = true;
	 		include $this->admin_tpl('ipbanned_add');
		}	 
	} 
	 
	/**
	 * IP删除
	 */
	function delete() {
 		if(is_array($this->input->post('ipbannedid'))){
			foreach($this->input->post('ipbannedid') as $ipbannedid_arr) {
				$this->db->delete(array('ipbannedid'=>$ipbannedid_arr));
			}
			$this->public_cache_file();//更新缓存 
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=ipbanned');	
		} else {
			$ipbannedid = intval($this->input->get('ipbannedid'));
			if($ipbannedid < 1) return false;
			$result = $this->db->delete(array('ipbannedid'=>$ipbannedid));
			$this->public_cache_file();//更新缓存 
			if($result){
				dr_admin_msg(1,L('operation_success'),'?m=admin&c=ipbanned');
			} else {
				dr_admin_msg(0,L("operation_failure"),'?m=admin&c=ipbanned');
			}
		}
	}
	
	/**
	 * 生成缓存
	 */
	public function public_cache_file() {
		$this->cache_api->cache('ipbanned');
 	}
}
?>