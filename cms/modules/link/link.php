<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class link extends admin {
	private $input,$setting,$db,$db2,$attachment_db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->setting = new_html_special_chars(getcache('link', 'commons'));
		$this->db = pc_base::load_model('link_model');
		$this->db2 = pc_base::load_model('type_model');
	}

	public function init() {
		if($this->input->get('typeid')!=''){
			$where = array('typeid'=>intval($this->input->get('typeid')),'siteid'=>$this->get_siteid());
		}else{
			$where = array('siteid'=>$this->get_siteid());
		}
 		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db->listinfo($where,$order = 'listorder ASC,linkid DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$types = $this->db2->get_types($this->get_siteid());
		$types = new_html_special_chars($types);
 		$type_arr = array ();
		$type_arr[0] = '默认分类';
 		foreach($types as $typeid=>$type){
			$type_arr[$type['typeid']] = $type['name'];
		}
		include $this->admin_tpl('link_list');
	}
	 
	//添加友情链接
 	public function add() {
 		if(IS_POST) {
			$link = $this->input->post('link');
			$link['addtime'] = SYS_TIME;
			$link['siteid'] = $this->get_siteid();
			if(empty($link['name'])) {
				dr_admin_msg(0,L('sitename_noempty'), array('field' => 'name'));
			} else {
				$link['name'] = safe_replace($link['name']);
			}
			
			if ($this->db->count(array('name'=>$link['name']))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			if(!$link['url']) dr_admin_msg(0,L('url').L('empty'), array('field' => 'url'));
			if ($link['logo']) {
				$link['logo'] = safe_replace($link['logo']);
			}
			$linkid = $this->db->insert($link,true);
			if(!$linkid) return FALSE; 
 			$siteid = $this->get_siteid();
	 		//更新附件状态
			if(SYS_ATTACHMENT_STAT & $link['logo']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($link['logo'],'link-'.$linkid,1);
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER,'', 'add');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
			$types = $this->db2->get_types($siteid);
			
			//print_r($types);exit;
 			include $this->admin_tpl('link_add');
		}

	}
	
	//更新排序
 	public function listorder() {
		if(IS_POST) {
			if ($this->input->post('listorders') && is_array($this->input->post('listorders'))) {
				foreach($this->input->post('listorders') as $linkid => $listorder) {
					$linkid = intval($linkid);
					$this->db->update(array('listorder'=>$listorder),array('linkid'=>$linkid));
				}
			}
			dr_admin_msg(1,L('operation_success'),HTTP_REFERER);
		} 
	}
	
	//添加友情链接分类
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
 			include $this->admin_tpl('link_type_add');
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
		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->db2->listinfo(array('module'=> ROUTE_M,'siteid'=>$this->get_siteid()),$order = 'listorder DESC',$page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db2->pages;
		include $this->admin_tpl('link_list_type');
	}
 
	public function edit() {
		if(IS_POST){
 			$linkid = intval($this->input->get('linkid'));
			if($linkid < 1) dr_admin_msg(0,L('illegal_parameters'));
			$link = $this->input->post('link');
			if(empty($link['name'])) {
				dr_admin_msg(0,L('sitename_noempty'), array('field' => 'name'));
			} else {
				$link['name'] = safe_replace($link['name']);
			}
			if ($this->db->count(array('linkid<>'=>$linkid, 'name'=>$name))) {
				dr_admin_msg(0,L('name').L('exists'), array('field' => 'name'));
			}
			if(!$link['url']) dr_admin_msg(0,L('url').L('empty'), array('field' => 'url'));
			$this->db->update($link,array('linkid'=>$linkid));
			//更新附件状态
			if(SYS_ATTACHMENT_STAT & $link['logo']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($link['logo'],'link-'.$linkid,1);
			}
			dr_admin_msg(1,L('operation_success'),'?m=link&c=link&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			$types = $this->db2->get_types($this->get_siteid());
 			$type_arr = array ();
			foreach($types as $typeid=>$type){
				$type_arr[$type['typeid']] = $type['name'];
			}
			//解出链接内容
			$info = $this->db->get_one(array('linkid'=>$this->input->get('linkid')));
			if(!$info) dr_admin_msg(0,L('link_exit'));
			extract($info); 
 			include $this->admin_tpl('link_edit');
		}

	}
	
	/**
	 * 修改友情链接 分类
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
			dr_admin_msg(1,L('operation_success'),'?m=link&c=link&a=list_type','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出分类内容
			$info = $this->db2->get_one(array('typeid'=>$this->input->get('typeid')));
			if(!$info) dr_admin_msg(0,L('linktype_exit'));
			extract($info);
			include $this->admin_tpl('link_type_edit');
		}

	}

	/**
	 * 删除友情链接  
	 * @param	intval	$sid	友情链接ID，递归删除
	 */
	public function delete() {
		if((!$this->input->get('linkid') || empty($this->input->get('linkid'))) && (!$this->input->post('linkid') || empty($this->input->post('linkid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('linkid'))){
				foreach($this->input->post('linkid') as $linkid_arr) {
 					//批量删除友情链接
					$this->db->delete(array('linkid'=>$linkid_arr));
					//更新附件状态
					if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_delete('link-'.$linkid_arr);
					}
				}
				dr_admin_msg(1,L('operation_success'),'?m=link&c=link');
			}else{
				$linkid = intval($this->input->get('linkid'));
				if($linkid < 1) return false;
				//删除友情链接
				$result = $this->db->delete(array('linkid'=>$linkid));
				//更新附件状态
				if(SYS_ATTACHMENT_STAT && SYS_ATTACHMENT_DEL) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_delete('link-'.$linkid);
				}
				if($result){
					dr_admin_msg(1,L('operation_success'),'?m=link&c=link');
				}else {
					dr_admin_msg(0,L("operation_failure"),'?m=link&c=link');
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	 
	/**
	 * 投票模块配置
	 */
	public function setting() {
		//读取配置文件
		$data = array();
 		$siteid = $this->get_siteid();//当前站点 
		//更新模型数据库,重设setting 数据. 
		$m_db = pc_base::load_model('module_model');
		$data = $m_db->select(array('module'=>'link'));
		$setting = string2array($data[0]['setting']);
		$now_seting = $setting[$siteid]; //当前站点配置
		if(IS_AJAX_POST) {
			//多站点存储配置文件
 			$setting[$siteid] = $this->input->post('setting');
  			setcache('link', $setting, 'commons');
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
			if((!$this->input->get('linkid') || empty($this->input->get('linkid'))) && (!$this->input->post('linkid') || empty($this->input->post('linkid')))) {
				dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			} else {
				if(is_array($this->input->post('linkid'))){//批量审核
					foreach($this->input->post('linkid') as $linkid_arr) {
						$this->db->update(array('passed'=>1),array('linkid'=>$linkid_arr));
					}
					dr_admin_msg(1,L('operation_success'),'?m=link&c=link');
				}else{//单个审核
					$linkid = intval($this->input->get('linkid'));
					if($linkid < 1) return false;
					$result = $this->db->update(array('passed'=>1),array('linkid'=>$linkid));
					if($result){
						dr_admin_msg(1,L('operation_success'),'?m=link&c=link');
					}else {
						dr_admin_msg(0,L("operation_failure"),'?m=link&c=link');
					}
				}
			}
		}else {//读取未审核列表
			$where = array('siteid'=>$this->get_siteid(),'passed'=>0);
			$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$infos = $this->db->listinfo($where,'linkid DESC',$page, SYS_ADMIN_PAGESIZE);
			$pages = $this->db->pages;
			include $this->admin_tpl('check_register_list');
		}
		
	}
	
 	//单个审核申请
 	public function check(){
		if((!$this->input->get('linkid') || empty($this->input->get('linkid'))) && (!$this->input->post('linkid') || empty($this->input->post('linkid')))) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else { 
			$linkid = intval($this->input->get('linkid'));
			if($linkid < 1) return false;
			//删除友情链接
			$result = $this->db->update(array('passed'=>1),array('linkid'=>$linkid));
			if($result){
				dr_admin_msg(1,L('operation_success'),'?m=link&c=link');
			}else {
				dr_admin_msg(0,L("operation_failure"),'?m=link&c=link');
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