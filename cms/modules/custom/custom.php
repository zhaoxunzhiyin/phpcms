<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class custom extends admin {
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('custom_model');
	}

	public function init() {
		$where = array('siteid'=>$this->get_siteid());
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'id DESC',$page, $pages = '30');
		$pages = $this->db->pages;
		
		$big_menu = array('javascript:artdialog(\'add\',\'?m=custom&c=custom&a=add\',\''.L('custom_add').'\',760,380);void(0);', L('custom_add'));
		include $this->admin_tpl('custom_list');
	}

	
	//添加
 	public function add() {
 		if($this->input->post('dosubmit')) {
			$custom = $this->input->post('custom');
	 		if(empty($custom['title'])){
				showmessage(L('custom_title_no_input'));
	 		}
	 		if(empty($custom['content'])){
				showmessage(L('custom_content_no_input'));
	 		}
			$custom['inputtime'] = SYS_TIME;
			$custom['siteid'] = $this->get_siteid();
			
			$customid = $this->db->insert($custom,true);
			if(!$customid) return FALSE; 
 			$siteid = $this->get_siteid();
			showmessage(L('operation_success'),HTTP_REFERER,'', 'edit');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
 			include $this->admin_tpl('custom_add');
		}

	}
	
	
	public function edit() {
		if($this->input->post('dosubmit')){
 			$id = intval($this->input->get('id'));
			$custom = $this->input->post('custom');
			if($id < 1) return false;
			if(!is_array($custom) || empty($custom)) return false;
			if((!$custom['title']) || empty($custom['content'])) return false;
			$this->db->update($custom,array('id'=>$id));
			showmessage(L('operation_success'),'?m=custom&c=custom&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			
			//解出链接内容
			$info = $this->db->get_one(array('id'=>$this->input->get('id')));
			if(!$info) showmessage(L('custom_exit'));
			extract($info); 
 			include $this->admin_tpl('custom_edit');
		}

	}
	
	

	/**
	 * 删除 
	 * @param	intval	$sid	幻灯片ID，递归删除
	 */
	public function delete() {
  		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id')))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('id'))){
				foreach($this->input->post('id') as $id_arr) {
 					//批量删除幻灯片
					$this->db->delete(array('id'=>$id_arr));
					//更新附件状态
					if(pc_base::load_config('system','attachment_stat')) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_delete('custom-'.$id_arr);
					}
				}
				showmessage(L('operation_success'),'?m=custom&c=custom');
			}else{
				$id = intval($this->input->get('id'));
				if($id < 1) return false;
				//删除幻灯片
				$result = $this->db->delete(array('id'=>$id));
				
				if($result){
					showmessage(L('operation_success'),'?m=custom&c=custom');
				}else {
					showmessage(L("operation_failure"),'?m=custom&c=custom');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	 
	public function view_content(){
		$id=intval($this->input->get('id'));
		$info = $this->db->get_one(array('id'=>$id));

		if(!$info) showmessage(L('custom_exit'));
		$content=$info['content'];
 		include $this->admin_tpl('custom_content');
	}

	public function view_lable(){
		$id=intval($this->input->get('id'));
		$info = $this->db->get_one(array('id'=>$id));
		if(!$info) showmessage(L('custom_exit'));
		extract($info); 
 		include $this->admin_tpl('custom_get_lable');
	}
    
	
	/**
	 * 说明:对字符串进行处理
	 * @param $string 待处理的字符串
	 * @param $isjs 是否生成JS代码
	 */
	function format_js($string, $isjs = 1){
		$string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
		return $isjs ? 'document.write("'.$string.'");' : $string;
	}
 
 
	
}
?>