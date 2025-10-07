<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class slider extends admin {
	private $input,$db,$db2,$attachment_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('slider_model');
		$this->db2 = pc_base::load_model('type_model');
	}

	public function init() {
		$typeid = $this->input->get('typeid');
		if($typeid){
			$where = array('typeid'=>intval($typeid),'siteid'=>$this->get_siteid());
		}else{
			$where = array('siteid'=>$this->get_siteid());
		}
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'listorder DESC,id DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$types = $this->db2->get_types($this->get_siteid());
		$types = new_html_special_chars($types);
 		$type_arr = array ();
 		foreach($types as $typeid=>$type){
			$type_arr[$type['typeid']] = $type['name'];
		}
		include $this->admin_tpl('slider_list');
	}
	 
	//添加幻灯片
 	public function add() {
 		if(IS_POST) {
			$slider = $this->input->post('slider');
			if((!$slider['name']) || empty($slider['name'])) dr_admin_msg(0,L('slider_name').L('empty'), array('field' => 'name'));
			if((!$slider['image']) || empty($slider['image'])) dr_admin_msg(0,L('image').L('empty'), array('field' => 'image'));
			$slider['addtime'] = SYS_TIME;
			$slider['siteid'] = $this->get_siteid();
			if ($slider['image']) {
				$slider['image'] = safe_replace($slider['image']);
			}
			$sliderid = $this->db->insert($slider,true);
			if(!$sliderid) return FALSE; 
 			$siteid = $this->get_siteid();
	 		//更新附件状态
			if(SYS_ATTACHMENT_STAT & $slider['image']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($slider['image'],'slider-'.$id,1);
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER,'', 'edit');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
			$types = $this->db2->get_types($siteid);
			
			//print_r($types);exit;
 			include $this->admin_tpl('slider_add');
		}

	}
	
	//更新排序
 	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $id => $listorder) {
					$id = intval($id);
					$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
				}
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
		} 
	}
	
	
	
	/**
	 * 删除分类
	 */
	public function delete_type() {
		if((!$this->input->get('typeid') || empty($this->input->get('typeid'))) && (!$this->input->post('typeid') || empty($this->input->post('typeid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('typeid'))){
				foreach($this->input->post('typeid') as $typeid_arr) {
 					$this->db2->delete(array('typeid'=>$typeid_arr));
				}
				dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
			}else{
				$typeid = intval($this->input->get('typeid'));
				if($typeid < 1) return false;
				$result = $this->db2->delete(array('typeid'=>$typeid));
				if($result){
					dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
				}else {
					dr_admin_msg(0,L("operation_failure"),HTTP_REFERER);
				}
			}
		}
	}
	
	//:分类管理
 	public function list_type() {
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db2->listinfo(array('module'=> ROUTE_M,'siteid'=>$this->get_siteid()),$order = 'listorder DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db2->pages;
		include $this->admin_tpl('slider_list_type');
	}
 
	public function edit() {
		if(IS_POST){
 			$id = intval($this->input->get('id'));
			if($id < 1) dr_admin_msg(0,L('illegal_parameters'));
			$slider = $this->input->post('slider');
			if((!$slider['name']) || empty($slider['name'])) dr_admin_msg(0,L('slider_name').L('empty'), array('field' => 'name'));
			if((!$slider['image']) || empty($slider['image'])) dr_admin_msg(0,L('image').L('empty'), array('field' => 'image'));
			$this->db->update($slider,array('id'=>$id));
			//更新附件状态
			if(SYS_ATTACHMENT_STAT & $slider['image']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($slider['image'],'slider-'.$id,1);
			}
			dr_admin_msg(1,L('operation_success'),'?m=slider&c=slider&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			$types = $this->db2->get_types($this->get_siteid());
 			$type_arr = array ();
			foreach($types as $typeid=>$type){
				$type_arr[$type['typeid']] = $type['name'];
			}
			//解出链接内容
			$info = $this->db->get_one(array('id'=>$this->input->get('id')));
			if(!$info) dr_admin_msg(0,L('slider_exit'));
			extract($info); 
 			include $this->admin_tpl('slider_edit');
		}

	}
	
	/**
	 * 修改幻灯片 分类
	 */
	public function edit_type() {
		if(IS_AJAX_POST){
			$typeid = intval($this->input->get('typeid'));
			$type = $this->input->post('type');
			if($typeid < 1) dr_admin_msg(0,L('illegal_parameters'));
			if(!$type['name']) dr_admin_msg(0,L('input').L('slider_postion_name'), array('field' => 'name'));
			if ($this->db2->count(array('typeid<>'=>$typeid, 'name'=>$type['name'], 'siteid'=>$this->get_siteid(), 'module'=>ROUTE_M))) {
				dr_admin_msg(0,L('slider_postion_name').L('exists'), array('field' => 'name'));
			}
			$this->db2->update($type,array('typeid'=>$typeid));
			dr_admin_msg(1,L('operation_success'),'?m=slider&c=slider&a=list_type','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出分类内容
			$info = $this->db2->get_one(array('typeid'=>$this->input->get('typeid')));
			if(!$info) dr_admin_msg(0,L('slider_exit'));
			extract($info);
			include $this->admin_tpl('slider_type_edit');
		}

	}

	/**
	 * 删除幻灯片  
	 * @param	intval	$sid	幻灯片ID，递归删除
	 */
	public function delete() {
  		if((!$this->input->get('id') || empty($this->input->get('id'))) && (!$this->input->post('id') || empty($this->input->post('id')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('id'))){
				foreach($this->input->post('id') as $id_arr) {
 					//批量删除幻灯片
					$this->db->delete(array('id'=>$id_arr));
					//更新附件状态
					if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_delete('slider-'.$id_arr);
					}
				}
				dr_admin_msg(1,L('operation_success'),'?m=slider&c=slider');
			}else{
				$id = intval($this->input->get('id'));
				if($id < 1) return false;
				//删除幻灯片
				$result = $this->db->delete(array('id'=>$id));
				//更新附件状态
				if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_delete('slider-'.$id);
				}
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=slider&c=slider');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=slider&c=slider');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	 
	
    //添加幻灯片分类
 	public function add_type() {
		if(IS_AJAX_POST) {
			$type = $this->input->post('type');
			if(!$type['name']) dr_admin_msg(0,L('input').L('slider_postion_name'), array('field' => 'name'));
			$type['siteid'] = $this->get_siteid(); 
			$type['module'] = ROUTE_M;
			if ($this->db2->count(array('name'=>$type['name'], 'siteid'=>$type['siteid'], 'module'=>ROUTE_M))) {
				dr_admin_msg(0,L('slider_postion_name').L('exists'), array('field' => 'name'));
			}
			$typeid = $this->db2->insert($type,true);
			dr_admin_msg(1,L('operation_success'));
		} else {
			$show_validator = $show_scroll = $show_header = true;
 			include $this->admin_tpl('slider_type_add');
		}

	}


	public function view_lable(){
		$show_header = '';
		$typeid=intval($this->input->get('typeid'));
 		include $this->admin_tpl('slider_get_lable');
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