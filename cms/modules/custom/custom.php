<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class custom extends admin {
	private $input,$db;
	public $siteid;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('custom_model');
		$this->siteid = $this->get_siteid();
	}

	public function init() {
		$where = array('siteid'=>$this->siteid);
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'id DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('custom_list');
	}

	//添加
 	public function add() {
 		if(IS_POST) {
			$custom = $this->input->post('custom');
	 		if(dr_is_empty($custom['title'])){
				dr_admin_msg(0,L('custom_title_no_input'), array('field' => 'title'));
	 		}
			$custom['siteid'] = $this->siteid;
			if ($this->db->count(array('title'=>$custom['title'], 'siteid'=>$custom['siteid']))) {
				dr_admin_msg(0,L('custom_title').L('exists'), array('field' => 'title'));
			}
	 		if(dr_is_empty($custom['content'])){
				dr_admin_msg(0,L('custom_content_no_input'), (SYS_EDITOR ? array('field' => 'content', 'jscode' => 'CKEDITOR.instances.content.focus();') : $jscode = array('field' => 'content')));
	 		}
			$custom['inputtime'] = SYS_TIME;
			
			$customid = $this->db->insert($custom,true);
			if(!$customid) return FALSE; 
 			$siteid = $this->siteid;
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER,'', 'edit');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->siteid;
 			include $this->admin_tpl('custom_add');
		}

	}

	public function edit() {
		if(IS_POST){
 			$id = intval($this->input->get('id'));
			$custom = $this->input->post('custom');
			if($id < 1) dr_admin_msg(0, L('illegal_parameters'));
	 		if(dr_is_empty($custom['title'])){
				dr_admin_msg(0,L('custom_title_no_input'), array('field' => 'title'));
	 		}
			if ($this->db->count(array('id<>'=>$id, 'title'=>$custom['title'], 'siteid'=>$this->siteid))) {
				dr_admin_msg(0,L('custom_title').L('exists'), array('field' => 'title'));
			}
	 		if(dr_is_empty($custom['content'])){
				dr_admin_msg(0,L('custom_content_no_input'), (SYS_EDITOR ? array('field' => 'content', 'jscode' => 'CKEDITOR.instances.content.focus();') : $jscode = array('field' => 'content')));
	 		}
			$this->db->update($custom,array('id'=>$id));
			dr_admin_msg(1,L('operation_success'),'?m=custom&c=custom&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			
			//解出链接内容
			$info = $this->db->get_one(array('id'=>$this->input->get('id')));
			if(!$info) dr_admin_msg(0,L('custom_exit'));
			extract($info); 
 			include $this->admin_tpl('custom_edit');
		}

	}

	/**
	 * 删除 
	 * @param	intval	$sid	ID，递归删除
	 */
	public function delete() {
		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('id'))){
				foreach($this->input->post('id') as $id_arr) {
 					//批量删除
					$this->db->delete(array('id'=>$id_arr));
				}
				dr_admin_msg(1,L('operation_success'),'?m=custom&c=custom');
			}else{
				$id = intval($this->input->get('id'));
				if($id < 1) dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
				//删除
				$result = $this->db->delete(array('id'=>$id));
				
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=custom&c=custom');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=custom&c=custom');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}

	public function public_view_content(){
		$show_header = $show_dialog = $show_pc_hash = true;
		$id=intval($this->input->get('id'));
		$info = $this->db->get_one(array('id'=>$id));
		if(!$info) dr_admin_msg(0,L('custom_exit'));
		$content=$info['content'];
 		include $this->admin_tpl('custom_content');
	}

	public function public_view_lable(){
		$show_header = $show_dialog = $show_pc_hash = true;
		$id=intval($this->input->get('id'));
		$info = $this->db->get_one(array('id'=>$id));
		if(!$info) dr_admin_msg(0,L('custom_exit'));
		extract($info); 
 		include $this->admin_tpl('custom_get_lable');
	}

	/**
	 * 说明:对字符串进行处理
	 * @param $string 待处理的字符串
	 * @param $isjs 是否生成JS代码
	 */
	function format_js($string, $isjs = 1){
		return format_js($string, $isjs);
	}
}
?>