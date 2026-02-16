<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class guestbook extends admin {
	private $input,$M,$db,$db2;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->M = new_html_special_chars(getcache('guestbook', 'commons'));
		$this->db = pc_base::load_model('guestbook_model');
		$this->db2 = pc_base::load_model('type_model');
	}

	public function init() {
		if($this->input->get('typeid')!=''){
			$where = array('typeid'=>$this->input->get('typeid'),'siteid'=>$this->get_siteid());
		}else{
			$where = array('siteid'=>$this->get_siteid());
		}
 		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'listorder DESC,guestid DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$types = $this->db2->get_types($this->get_siteid());
		$types = new_html_special_chars($types);
 		$type_arr = array ();
 		foreach($types as $typeid=>$type){
			$type_arr[$type['typeid']] = $type['name'];
		}
		include $this->admin_tpl('guestbook_list');
	}
	
	//更新排序
 	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $guestid => $listorder) {
					$this->db->update(array('listorder'=>$listorder),array('guestid'=>$guestid));
				}
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
		} 
	}
	
	//添加留言板分类
 	public function add_type() {
		if(IS_AJAX_POST) {
			$type = $this->input->post('type');
			if(!$type['name']) dr_admin_msg(0,L('typename_noempty'), array('field' => 'name'));
			$type['siteid'] = $this->get_siteid(); 
			$type['module'] = ROUTE_M;
			if ($this->db2->count(array('name'=>$type['name'], 'siteid'=>$type['siteid'], 'module'=>ROUTE_M))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			$typeid = $this->db2->insert($type,true);
			dr_admin_msg(1,L('operation_success'));
		} else {
			$show_validator = $show_scroll = $show_header = true; 
 			include $this->admin_tpl('guestbook_type_add');
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
				if($result)
				{
					dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
				}else {
					dr_admin_msg(0,L("operation_failure"),HTTP_REFERER);
				}
			}
		}
	}
	
	//:分类管理
 	public function list_type() {
		$infos = $this->db2->listinfo(array('module'=> ROUTE_M,'siteid'=>$this->get_siteid()),$order = 'listorder DESC',$page, SYS_ADMIN_PAGESIZE);
		include $this->admin_tpl('guestbook_list_type');
	}
 
	public function show() {
		if(IS_POST){
 			$guestid = intval($this->input->get('guestid'));
			if($guestid < 1) return false;
			
			$this->db->update($this->input->post('guestbook'),array('guestid'=>$guestid));
			
			dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook&a=show','', 'show');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			$types = $this->db2->get_types($this->get_siteid());
 			$type_arr = array ();
			foreach($types as $typeid=>$type){
				$type_arr[$type['typeid']] = $type['name'];
			}
			//解出链接内容
			$info = $this->db->get_one(array('guestid'=>$this->input->get('guestid')));
			if(!$info) dr_admin_msg(0,L('guestbook_exit'));
			extract($info); 
 			include $this->admin_tpl('guestbook_show');
		}

	}
	
	/**
	 * 修改留言板 分类
	 */
	public function edit_type() {
		if(IS_AJAX_POST){
			$typeid = intval($this->input->get('typeid'));
			$type = $this->input->post('type');
			if($typeid < 1) dr_admin_msg(0,L('illegal_parameters'));
			if(!$type['name']) dr_admin_msg(0,L('typename_noempty'), array('field' => 'name'));
			if ($this->db2->count(array('typeid<>'=>$typeid, 'name'=>$type['name'], 'siteid'=>$this->get_siteid(), 'module'=>ROUTE_M))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			$this->db2->update($type,array('typeid'=>$typeid));
			dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook&a=list_type','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出分类内容
			$info = $this->db2->get_one(array('typeid'=>$this->input->get('typeid')));
			if(!$info) dr_admin_msg(0,L('guesttype_exit'));
			extract($info);
			include $this->admin_tpl('guestbook_type_edit');
		}

	}

	/**
	 * 删除留言板  
	 * @param	intval	$sid	留言板ID，递归删除
	 */
	public function delete() {
  		if((!$this->input->get('guestid') || empty($this->input->get('guestid'))) && (!$this->input->post('guestid') || empty($this->input->post('guestid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('guestid'))){
				foreach($this->input->post('guestid') as $guestid_arr) {
 					//批量删除留言板
					$this->db->delete(array('guestid'=>$guestid_arr));
					 
				}
				dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook');
			}else{
				$guestid = intval($this->input->get('guestid'));
				if($guestid < 1) return false;
				//删除留言板
				$result = $this->db->delete(array('guestid'=>$guestid));
				 
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=guestbook&c=guestbook');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	 
	/**
	 * 留言板模块配置
	 */
	public function setting() {
		//读取配置文件
		$data = array();
 		$siteid = $this->get_siteid();//当前站点 
		//更新模型数据库,重设setting 数据. 
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'guestbook'));
		$setting = string2array($data[0]['setting']);
		$now_seting = $setting[$siteid]; //当前站点配置
		if(IS_AJAX_POST) {
			//多站点存储配置文件
 			$setting[$siteid] = $this->input->post('setting');
  			setcache('guestbook', $setting, 'commons'); 
			//更新模型数据库,重设setting 数据. 
  			$m_db = pc_base::load_model('module_model'); //调用模块数据模型
			$set = array2string($setting);
			$m_db->update(array('setting'=>$set), array('module'=>ROUTE_M));
			dr_json(1,L('setting_updates_successful'));
		} else {
			@extract($now_seting);
 			include $this->admin_tpl('setting');
		}
	}
	
  	//批量审核申请 ...
 	public function check_register(){
		if(IS_POST) {
			if((!$this->input->get('guestid') || empty($this->input->get('guestid'))) && (!$this->input->post('guestid') || empty($this->input->post('guestid')))) {
				dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($this->input->post('guestid'))){//批量审核
					foreach($this->input->post('guestid') as $guestid_arr) {
						$this->db->update(array('passed'=>1),array('guestid'=>$guestid_arr));
					}
					dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook');
				}else{//单个审核
					$guestid = intval($this->input->get('guestid'));
					if($guestid < 1) return false;
					$result = $this->db->update(array('passed'=>1),array('guestid'=>$guestid));
					if($result){
						dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook');
					}else {
						dr_admin_msg(0,L("operation_failure"),'?m=guestbook&c=guestbook');
					}
				}
			}
		}else {//读取未审核列表
			$where = array('siteid'=>$this->get_siteid(),'passed'=>0);
			$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$infos = $this->db->listinfo($where,'guestid DESC',$page, SYS_ADMIN_PAGESIZE);
			$pages = $this->db->pages;
			include $this->admin_tpl('check_register_list');
		}
		
	}
	
 	//单个审核申请
 	public function check(){
		if((!$this->input->get('guestid') || empty($this->input->get('guestid'))) && (!$this->input->post('guestid') || empty($this->input->post('guestid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else { 
			$guestid = intval($this->input->get('guestid'));
			if($guestid < 1) return false;
			//删除留言板
			$result = $this->db->update(array('passed'=>1),array('guestid'=>$guestid));
			if($result){
				dr_admin_msg(1,L('operation_success'),'?m=guestbook&c=guestbook');
			}else {
				dr_admin_msg(0,L("operation_failure"),'?m=guestbook&c=guestbook');
			}
			 
		}
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