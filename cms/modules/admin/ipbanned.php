<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class ipbanned extends admin {
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('ipbanned_model');
		pc_base::load_sys_class('form', '', 0);
		parent::__construct();
	}
	
	function init () {
		$page = $this->input->get('page') ? $this->input->get('page') : '1';
		$infos = array();
		$infos = $this->db->listinfo('','ipbannedid DESC',$page ,'20');
		$pages = $this->db->pages;	
		$big_menu = array('javascript:artdialog(\'add\',\'?m=admin&c=ipbanned&a=add\',\''.L('add_ipbanned').'\',450,320);void(0);', L('add_ipbanned'));
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
		if($this->input->post('dosubmit')){
			$info = $this->input->post('info');
  			$info['expires']=strtotime($info['expires']);
			$this->db->insert($info);
			$this->public_cache_file();//更新缓存 
			showmessage(L('operation_success'),'?m=admin&c=ipbanned&a=add','', 'add');
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
			showmessage(L('operation_success'),'?m=admin&c=ipbanned');	
		} else {
			$ipbannedid = intval($this->input->get('ipbannedid'));
			if($ipbannedid < 1) return false;
			$result = $this->db->delete(array('ipbannedid'=>$ipbannedid));
			$this->public_cache_file();//更新缓存 
			if($result){
				showmessage(L('operation_success'),'?m=admin&c=ipbanned');
			} else {
				showmessage(L("operation_failure"),'?m=admin&c=ipbanned');
			}
		}
	}
	
	/**
	 * IP搜索
	 */
	public function search_ip() {
		$where = '';
		if($this->input->get('search')) extract($this->input->get('search'));
		if($ip){
			$where .= $where ?  " AND ip LIKE '%$ip%'" : " ip LIKE '%$ip%'";
		}
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'ipbannedid DESC',$page, $pages = '2');
		$pages = $this->db->pages;
  		$big_menu = array('javascript:artdialog(\'add\',\'?m=admin&c=ipbanned&a=add\',\''.L('add_ipbanned').'\',450,320);void(0);', L('add_ipbanned'));
		include $this->admin_tpl('ip_search_list');
	} 
	
	/**
	 * 生成缓存
	 */
	public function public_cache_file() {
		$infos = $this->db->select('','ip,expires','','ipbannedid desc');
		setcache('ipbanned', $infos, 'commons');
		return true;
 	}
}
?>