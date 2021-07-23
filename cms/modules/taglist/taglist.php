<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class taglist extends admin {
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('keyword_model');
		$this->data_db = pc_base::load_model('keyword_data_model');
	}
	
	public function init() {
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$datas = $this->db->listinfo(array('siteid'=>$this->get_siteid()),'id DESC',$page,20);
		$pages = $this->db->pages;
		//var_dump($info);	
		$big_menu = array('javascript:artdialog(\'add\',\'?m=taglist&c=taglist&a=add\',\'添加内容\',700,450);void(0);', '添加内容');
		include $this->admin_tpl('taglist'); 

	}
	
	public function add() {
 		if(isset($_POST['dosubmit'])) {
			if(empty($_POST['tag']['keyword'])) {
				showmessage(L('关键字不能为空'),HTTP_REFERER);
			} else {
				$_POST['tag']['keyword'] = safe_replace($_POST['tag']['keyword']);
			}
			if((!$_POST['tag']['pinyin']) || empty($_POST['tag']['pinyin'])) {
				$pinyin = pc_base::load_sys_class('pinyin');
				$py = $pinyin->result($_POST['tag']['keyword']);
				if (strlen($py) > 12) {
					$py = $pinyin->result($_POST['tag']['keyword'], 0);
				}
				$_POST['tag']['pinyin'] = $py;
			}
			$_POST['tag']['siteid'] = $this->get_siteid();
			$data = new_addslashes($_POST['tag']);
			if($this->db->insert($data,true)){
				showmessage(L('operation_success'),HTTP_REFERER,'', 'add');
			}else{		
			 return FALSE; 
			}

		}
       include $this->admin_tpl('add');
	}
	
	public function edit() {
		if(isset($_POST['dosubmit'])){
 			$id = intval($_GET['id']);
			echo $id;
			if($id < 1) return false;
			if(!is_array($_POST['tag']) || empty($_POST['tag'])) showmessage(L('参数错误'),HTTP_REFERER);
			if((!$_POST['tag']['keyword']) || empty($_POST['tag']['keyword'])) showmessage(L('关键字不能为空'),HTTP_REFERER);
			if((!$_POST['tag']['pinyin']) || empty($_POST['tag']['pinyin'])) {
				$pinyin = pc_base::load_sys_class('pinyin');
				$py = $pinyin->result($_POST['tag']['keyword']);
				if (strlen($py) > 12) {
					$py = $pinyin->result($_POST['tag']['keyword'], 0);
				}
				$_POST['tag']['pinyin'] = $py;
			}
			$this->db->update($_POST['tag'],array('id'=>$id));
			showmessage(L('operation_success'),'?m=taglist&c=taglist&a=edit','', 'edit');
		}else{
			$info = $this->db->get_one(array('id'=>$_GET['id']));
			if(!$info) showmessage(L('修改失败'));
			extract($info); 
			include $this->admin_tpl('edit');
		}
	}
	
	public function delete() {
  		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $id_arr) {
 					//批量删除
					$this->db->delete(array('id'=>$id_arr, 'siteid'=>$this->get_siteid()));
					$this->data_db->delete(array('tagid'=>$id_arr, 'siteid'=>$this->get_siteid()));
				}
				showmessage(L('operation_success'),'?m=taglist&c=taglist');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除
				$result = $this->db->delete(array('id'=>$id, 'siteid'=>$this->get_siteid()));
				$result = $this->data_db->delete(array('tagid'=>$id, 'siteid'=>$this->get_siteid()));
				if($result){
					showmessage(L('operation_success'),'?m=taglist&c=taglist');
				}else {
					showmessage(L("operation_failure"),'?m=taglist&c=taglist');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
}
?>