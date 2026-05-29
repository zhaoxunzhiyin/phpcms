<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class keylink extends admin {
	private $input,$db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('keylink_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}
	
	function init () {
		$page = $this->input->get('page') ? intval($this->input->get('page')) : '1';
		$infos = $this->db->listinfo('','keylinkid DESC',$page,SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;	
		include $this->admin_tpl('keylink_list');
	}
	
	/**
	 * 关联词添加
	 */
	function add() {
		if(IS_POST){
			$info = $this->input->post('info');
			!$info['word'] && dr_json(0, L('input').L('keylink'), array('field' => 'word'));
			if ($this->db->count(array('word'=>$info['word']))) {
				dr_admin_msg(0, L('keylink').L('exists'), array('field' => 'word'));
			}
			!$info['url'] && dr_json(0, L('input_siteurl'), array('field' => 'url'));
			$this->db->insert($this->input->post('info'));
			$this->public_cache_file();//更新缓存 
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=keylink&a=add','', 'add');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			include $this->admin_tpl('keylink_add');
		 }	 
	} 
	
	/**
	 * 关联词修改
	 */
	function edit() {
		if(IS_POST){
			$keylinkid = intval($this->input->get('keylinkid'));
			$info = $this->input->post('info');
			!$info['word'] && dr_json(0, L('input').L('keylink'), array('field' => 'word'));
			if ($this->db->count(array('keylinkid<>'=>$keylinkid, 'word'=>$info['word']))) {
				dr_admin_msg(0, L('keylink').L('exists'), array('field' => 'word'));
			}
			!$info['url'] && dr_json(0, L('input_siteurl'), array('field' => 'url'));
 			$this->db->update($info,array('keylinkid'=>$keylinkid));
			$this->public_cache_file();//更新缓存
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=keylink&a=edit','', 'edit');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			$info = $this->db->get_one(array('keylinkid'=>$this->input->get('keylinkid')));
			if(!$info) dr_admin_msg(0,L('specified_word_not_exist'));
 			extract($info);
			include $this->admin_tpl('keylink_edit');
		}	 
	}
	/**
	 * 关联词删除
	 */
	function delete() {
 		if(is_array($this->input->post('keylinkid'))){
			foreach($this->input->post('keylinkid') as $keylinkid_arr) {
				$this->db->delete(array('keylinkid'=>$keylinkid_arr));
			}
			$this->public_cache_file();//更新缓存
			dr_admin_msg(1,L('operation_success'),'?m=admin&c=keylink');	
		} else {
			$keylinkid = intval($this->input->get('keylinkid'));
			if($keylinkid < 1) return false;
			$result = $this->db->delete(array('keylinkid'=>$keylinkid));
			$this->public_cache_file();//更新缓存
			if($result){
				dr_admin_msg(1,L('operation_success'),'?m=admin&c=keylink');
			}else {
				dr_admin_msg(0,L("operation_failure"),'?m=admin&c=keylink');
			}
		}
	}
	/**
	 * 生成缓存
	 */
	public function public_cache_file() {
		$this->cache_api->cache('keylink');
 	}
}
?>