<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class taglist extends admin {
	private $input,$db,$data_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('keyword_model');
		$this->data_db = pc_base::load_model('keyword_data_model');
	}
	
	public function init() {
		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$datas = $this->db->listinfo(array('siteid'=>$this->get_siteid()),'id DESC',$page,SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		include $this->admin_tpl('taglist'); 

	}
	
	public function add() {
 		if(IS_POST) {
			$tag = $this->input->post('tag');
			if(empty($tag['keyword'])) {
				dr_admin_msg(0,L('关键字不能为空'), array('field' => 'keyword'));
			} else {
				$tag['keyword'] = safe_replace($tag['keyword']);
			}
			if((!$tag['pinyin']) || empty($tag['pinyin'])) {
				$pinyin = pc_base::load_sys_class('pinyin');
				$py = $pinyin->result($tag['keyword']);
				$tag['pinyin'] = $py;
			}
			$tag['siteid'] = $this->get_siteid();
			if($this->db->insert($tag,true)){
				dr_admin_msg(1,L('operation_success'),HTTP_REFERER,'', 'add');
			}else{		
			 return FALSE; 
			}

		}
		$show_header = true;
		include $this->admin_tpl('add');
	}
	
	public function edit() {
		$id = intval($this->input->get('id'));
		if(IS_POST){
 			$id = intval($this->input->get('id'));
			if($id < 1) return false;
			$tag = $this->input->post('tag');
			if(!is_array($tag) || empty($tag)) dr_admin_msg(0,L('参数错误'));
			if((!$tag['keyword']) || empty($tag['keyword'])) dr_admin_msg(0,L('关键字不能为空'), array('field' => 'keyword'));
			if((!$tag['pinyin']) || empty($tag['pinyin'])) {
				$pinyin = pc_base::load_sys_class('pinyin');
				$py = $pinyin->result($tag['keyword']);
				$tag['pinyin'] = $py;
			}
			$this->db->update($tag,array('id'=>$id));
			dr_admin_msg(1,L('operation_success'),'?m=taglist&c=taglist&a=edit','', 'edit');
		}else{
			$show_header = true;
			$info = $this->db->get_one(array('id'=>$id));
			if(!$info) dr_admin_msg(0,L('修改失败'));
			extract($info); 
			include $this->admin_tpl('edit');
		}
	}
	
	public function delete() {
  		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('id'))){
				foreach($this->input->post('id') as $id_arr) {
 					//批量删除
					$this->db->delete(array('id'=>$id_arr, 'siteid'=>$this->get_siteid()));
					$this->data_db->delete(array('tagid'=>$id_arr, 'siteid'=>$this->get_siteid()));
				}
				dr_admin_msg(1,L('operation_success'),'?m=taglist&c=taglist');
			}else{
				$id = intval($this->input->get('id'));
				if($id < 1) return false;
				//删除
				$result = $this->db->delete(array('id'=>$id, 'siteid'=>$this->get_siteid()));
				$result = $this->data_db->delete(array('tagid'=>$id, 'siteid'=>$this->get_siteid()));
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=taglist&c=taglist');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=taglist&c=taglist');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
}
?>