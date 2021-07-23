<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('form','',0);

class urlrule extends admin {
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('urlrule_model');
		$this->module_db = pc_base::load_model('module_model');
	}
	
	function init () {
		$page = intval($this->input->get('page'));
		$infos = $this->db->listinfo('','',$page);
		$pages = $this->db->pages;
		$big_menu = array('javascript:artdialog(\'add\',\'?m=admin&c=urlrule&a=add\',\''.L('add_urlrule').'\',750,450);void(0);', L('add_urlrule'));
		$this->public_cache_urlrule();
		include $this->admin_tpl('urlrule_list');
	}
	function add() {
		if($this->input->post('dosubmit')) {
			$info = $this->input->post('info');
			$info['urlrule'] = rtrim(trim($info['urlrule']),'.php');
			$info['urlrule'] = $this->url_replace($info['urlrule']);
			if($this->url_ifok($info)==false){
				showmessage('url规则里含有非法php字符');
			}
			$this->db->insert($info);
			$this->public_cache_urlrule();
			showmessage(L('add_success'),'','','add');
		} else {
			$show_validator = $show_header = '';
			$modules_arr = $this->module_db->select('','module,name');
			
			$modules = array();
			foreach ($modules_arr as $r) {
				$modules[$r['module']] = $r['name'];
			}
		
			include $this->admin_tpl('urlrule_add');
		}
	}
	function delete() {
		$urlruleid = intval($this->input->get('urlruleid'));
		$this->db->delete(array('urlruleid'=>$urlruleid));
		$this->public_cache_urlrule();
		showmessage(L('operation_success'),HTTP_REFERER);
	}
	
	function edit() {
		if($this->input->post('dosubmit')) {
			$urlruleid = intval($this->input->post('urlruleid'));
			$info = $this->input->post('info');
			$info['urlrule'] = rtrim(trim($info['urlrule']),'.php');
			$info['urlrule'] = $this->url_replace($info['urlrule']);
			if($this->url_ifok($info['urlrule'])==false){
				showmessage('url规则里含有非法php字符');
			}			
			$this->db->update($info,array('urlruleid'=>$urlruleid));
			$this->public_cache_urlrule();
			showmessage(L('update_success'),'','','edit');
		} else {
			$show_validator = $show_header = '';
			$urlruleid = $this->input->get('urlruleid');
			$r = $this->db->get_one(array('urlruleid'=>$urlruleid));
			extract($r);
			$modules_arr = $this->module_db->select('','module,name');
			
			$modules = array();
			foreach ($modules_arr as $r) {
				$modules[$r['module']] = $r['name'];
			}
			include $this->admin_tpl('urlrule_edit');
		}
	}
	/**
	 * 更新URL规则
	 */
	public function public_cache_urlrule() {
		$datas = $this->db->select('','*','','','','urlruleid');
		$basic_data = array();
		foreach($datas as $roleid=>$r) {
			$basic_data[$roleid] = $r['urlrule'];;
		}
		setcache('urlrules_detail',$datas,'commons');
		setcache('urlrules',$basic_data,'commons');
	}
	/*
	*url规则替换
	**/
	public function url_replace($url){
		$urldb = explode("|",$url);
		foreach($urldb as $key=>$value){
			if(strpos($value, "index.php") === 0){
				$value = str_replace('index.php','',$value);
				$value = str_replace('.php','',$value);
				$value = "index.php".$value;
			}else{
				$value = str_replace('.php','',$value);
			}
			$urldb[$key]=$value;
		}
		return implode("|",$urldb);
	}
	/*
	*url规则 判断。
	**/
	public function url_ifok($url){
		$urldb = explode("|",$url);
		foreach($urldb as $key=>$value){
			if(strpos($value, "index.php") === 0){
				$value = substr($value,'9');
			}
			if( stripos($value, "php") !== false){
				return false;
			}
		}
		return true;
	}
}
?>