<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class slider extends admin {
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('slider_model');
		$this->db2 = pc_base::load_model('type_model');
	}

	public function init() {
		if($_GET['typeid']!=''){
			$where = array('typeid'=>intval($_GET['typeid']),'siteid'=>$this->get_siteid());
		}else{
			$where = array('siteid'=>$this->get_siteid());
		}
 		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db->listinfo($where,$order = 'listorder DESC,id DESC',$page, $pages = '9');
		$pages = $this->db->pages;
		$types = $this->db2->get_types($this->get_siteid());
		$types = new_html_special_chars($types);
 		$type_arr = array ();
 		foreach($types as $typeid=>$type){
			$type_arr[$type['typeid']] = $type['name'];
		}
		$big_menu = array('javascript:artdialog(\'add\',\'?m=slider&c=slider&a=add&typeid='.$_GET['typeid'].'\',\''.L('slider_add').'\',700,330);void(0);', L('add_slider'));
		include $this->admin_tpl('slider_list');
	}

	/*
	 *判断标题重复和验证 
	 */
	public function public_name() {
		$slider_title = isset($_GET['slider_name']) && trim($_GET['slider_name']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['slider_name'])) : trim($_GET['slider_name'])) : exit('0');
			
		$id = isset($_GET['id']) && intval($_GET['id']) ? intval($_GET['id']) : '';
		$data = array();
		if ($id) {

			$data = $this->db->get_one(array('id'=>$id), 'name');
			if (!empty($data) && $data['name'] == $slider_title) {
				exit('1');
			}
		}
		if ($this->db->get_one(array('name'=>$slider_title), 'id')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	 
	//添加分类时，验证分类名是否已存在
	public function public_check_name() {
		$type_name = isset($_GET['type_name']) && trim($_GET['type_name']) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($_GET['type_name'])) : trim($_GET['type_name'])) : exit('0');
		$type_name = safe_replace($type_name);
 		$typeid = isset($_GET['typeid']) && intval($_GET['typeid']) ? intval($_GET['typeid']) : '';
 		$data = array();
		if ($typeid) {
 			$data = $this->db2->get_one(array('typeid'=>$typeid), 'name');
			if (!empty($data) && $data['name'] == $type_name) {
				exit('1');
			}
		}
		if ($this->db2->get_one(array('name'=>$type_name), 'typeid')) {
			exit('0');
		} else {
			exit('1');
		}
	}
	 
	//添加幻灯片
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
			$_POST['slider']['addtime'] = SYS_TIME;
			$_POST['slider']['siteid'] = $this->get_siteid();
			
			if ($_POST['slider']['image']) {
				$_POST['slider']['image'] = safe_replace($_POST['slider']['image']);
			}
			$data = new_addslashes($_POST['slider']);
			$sliderid = $this->db->insert($data,true);
			if(!$sliderid) return FALSE; 
 			$siteid = $this->get_siteid();
	 		//更新附件状态
			if(pc_base::load_config('system','attachment_stat') & $_POST['slider']['image']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($_POST['slider']['image'],'slider-'.$id,1);
			}
			showmessage(L('operation_success'),HTTP_REFERER,'', 'edit');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
 			$siteid = $this->get_siteid();
			$types = $this->db2->get_types($siteid);
			
			//print_r($types);exit;
 			include $this->admin_tpl('slider_add');
		}

	}
	
	/**
	 * 说明:异步更新排序 
	 * @param  $optionid
	 */
	public function listorder_up() {
		$result = $this->db->update(array('listorder'=>'+=1'),array('id'=>$_GET['id']));
		if($result){
			echo 1;
		} else {
			echo 0;
		}
	}
	
	//更新排序
 	public function listorder() {
		if(isset($_POST['dosubmit'])) {
			if (isset($_POST['listorders']) && is_array($_POST['listorders'])) {
				foreach($_POST['listorders'] as $id => $listorder) {
					$id = intval($id);
					$this->db->update(array('listorder'=>$listorder),array('id'=>$id));
				}
			}
			showmessage(L('operation_success'),HTTP_REFERER);
		} 
	}
	
	
	
	/**
	 * 删除分类
	 */
	public function delete_type() {
		if((!isset($_GET['typeid']) || empty($_GET['typeid'])) && (!isset($_POST['typeid']) || empty($_POST['typeid']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['typeid'])){
				foreach($_POST['typeid'] as $typeid_arr) {
 					$this->db2->delete(array('typeid'=>$typeid_arr));
				}
				showmessage(L('operation_success'),HTTP_REFERER);
			}else{
				$typeid = intval($_GET['typeid']);
				if($typeid < 1) return false;
				$result = $this->db2->delete(array('typeid'=>$typeid));
				if($result)
				{
					showmessage(L('operation_success'),HTTP_REFERER);
				}else {
					showmessage(L("operation_failure"),HTTP_REFERER);
				}
			}
		}
	}
	
	//:分类管理
 	public function list_type() {
		$this->db2 = pc_base::load_model('type_model');
		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
		$infos = $this->db2->listinfo(array('module'=> ROUTE_M,'siteid'=>$this->get_siteid()),$order = 'listorder DESC',$page, $pages = '10');
		$big_menu = array('javascript:artdialog(\'add\',\'?m=slider&c=slider&a=add\',\''.L('slider_add').'\',700,450);void(0);', L('slider_add'));
		$pages = $this->db2->pages;
		include $this->admin_tpl('slider_list_type');
	}
 
	public function edit() {
		if(isset($_POST['dosubmit'])){
 			$id = intval($_GET['id']);
			if($id < 1) return false;
			if(!is_array($_POST['slider']) || empty($_POST['slider'])) return false;
			if((!$_POST['slider']['name']) || empty($_POST['slider']['name'])) return false;
			$this->db->update($_POST['slider'],array('id'=>$id));
			//更新附件状态
			if(pc_base::load_config('system','attachment_stat') & $_POST['slider']['image']) {
				$this->attachment_db = pc_base::load_model('attachment_model');
				$this->attachment_db->api_update($_POST['slider']['image'],'slider-'.$id,1);
			}
			showmessage(L('operation_success'),'?m=slider&c=slider&a=edit','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			pc_base::load_sys_class('form', '', 0);
			$types = $this->db2->get_types($this->get_siteid());
 			$type_arr = array ();
			foreach($types as $typeid=>$type){
				$type_arr[$type['typeid']] = $type['name'];
			}
			//解出链接内容
			$info = $this->db->get_one(array('id'=>$_GET['id']));
			if(!$info) showmessage(L('slider_exit'));
			extract($info); 
 			include $this->admin_tpl('slider_edit');
		}

	}
	
	/**
	 * 修改幻灯片 分类
	 */
	public function edit_type() {
		if(isset($_POST['dosubmit'])){ 
			$typeid = intval($_GET['typeid']); 
			if($typeid < 1) return false;
			if(!is_array($_POST['type']) || empty($_POST['type'])) return false;
			if((!$_POST['type']['name']) || empty($_POST['type']['name'])) return false;
			$this->db2->update($_POST['type'],array('typeid'=>$typeid));
			showmessage(L('operation_success'),'?m=slider&c=slider&a=list_type','', 'edit');
			
		}else{
 			$show_validator = $show_scroll = $show_header = true;
			//解出分类内容
			$info = $this->db2->get_one(array('typeid'=>$_GET['typeid']));
			if(!$info) showmessage(L('slider_exit'));
			extract($info);
			include $this->admin_tpl('slider_type_edit');
		}

	}

	/**
	 * 删除幻灯片  
	 * @param	intval	$sid	幻灯片ID，递归删除
	 */
	public function delete() {
  		if((!isset($_GET['id']) || empty($_GET['id'])) && (!isset($_POST['id']) || empty($_POST['id']))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($_POST['id'])){
				foreach($_POST['id'] as $id_arr) {
 					//批量删除幻灯片
					$this->db->delete(array('id'=>$id_arr));
					//更新附件状态
					if(pc_base::load_config('system','attachment_stat')) {
						$this->attachment_db = pc_base::load_model('attachment_model');
						$this->attachment_db->api_delete('slider-'.$id_arr);
					}
				}
				showmessage(L('operation_success'),'?m=slider&c=slider');
			}else{
				$id = intval($_GET['id']);
				if($id < 1) return false;
				//删除幻灯片
				$result = $this->db->delete(array('id'=>$id));
				//更新附件状态
				if(pc_base::load_config('system','attachment_stat')) {
					$this->attachment_db = pc_base::load_model('attachment_model');
					$this->attachment_db->api_delete('slider-'.$id);
				}
				if($result){
					showmessage(L('operation_success'),'?m=slider&c=slider');
				}else {
					showmessage(L("operation_failure"),'?m=slider&c=slider');
				}
			}
			showmessage(L('operation_success'), HTTP_REFERER);
		}
	}
	 
	
    //添加幻灯片分类
 	public function add_type() {
		if(isset($_POST['dosubmit'])) {
			if(empty($_POST['type']['name'])) {
				showmessage(L('slider_postion_noempty'),HTTP_REFERER);
			}
			$_POST['type']['siteid'] = $this->get_siteid(); 
			$_POST['type']['module'] = ROUTE_M;
 			$this->db2 = pc_base::load_model('type_model');
			$typeid = $this->db2->insert($_POST['type'],true);
			if(!$typeid) return FALSE;
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			$show_validator = $show_scroll = true;
			$big_menu = array('javascript:artdialog(\'add\',\'?m=slider&c=slider&a=add\',\''.L('slider_add').'\',700,450);void(0);', L('slider_add'));
 			include $this->admin_tpl('slider_type_add');
		}

	}


	public function view_lable(){
		$show_header = '';
		$typeid=intval($_GET['typeid']);
 		include $this->admin_tpl('slider_get_lable');
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