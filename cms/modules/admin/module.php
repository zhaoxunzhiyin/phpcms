<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class module extends admin {
	private $db;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('module_model');
		parent::__construct();
	}
	
	public function init() {
		$show_header = '';
		$dirs = $module = $dirs_arr = $directory = array();
		$dirs = glob(PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'*');
		foreach ($dirs as $d) {
			if (is_dir($d)) {
				$d = basename($d);
				$dirs_arr[] = $d;
			}
		}
		define('INSTALL', true);
		$modules = $this->db->select('', '*', '', '', '', 'module');
		$total = count($dirs_arr);
		$dirs_arr = array_chunk($dirs_arr, 20, true);
		$page = max(intval($this->input->get('page')), 1);
		$pages = pages($total, $page, 20);
		$directory = $dirs_arr[intval($page-1)];
		include $this->admin_tpl('module_list');
	}
	
	/**
	 * 模块安装
	 */
	public function install() {
		$this->module = $this->input->post('module') ? $this->input->post('module') : $this->input->get('module');
		$module_api = pc_base::load_app_class('module_api');
		if (!$module_api->check($this->module)) showmessage($module_api->error_msg, 'blank');
		if ($this->input->post('dosubmit')) {
			if ($module_api->install()) showmessage(L('success_module_install'),'blank','','','var w = h = \'60%\';if (is_mobile()) {w = h = \'100%\';}if (w==\'100%\' && h==\'100%\') {var drag = false;} else {var drag = true;}var diag = new Dialog({id:\'module_id\',title:\''.L('update_backup').'\',url:\''.SELF.'?m=admin&c=cache_all&a=init&pc_hash='.$_SESSION['pc_hash'].'\',width:w,height:h,modal:true,draggable:drag});diag.onClose=function(){dr_install_confirm();};diag.show();');
			else showmesage($module_api->error_msg, HTTP_REFERER);
		} else {
			include PC_PATH.'modules'.DIRECTORY_SEPARATOR.$this->module.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php';
			include $this->admin_tpl('module_config');
		}
	}
	
	/**
	 * 模块卸载
	 */
	public function uninstall() {
		if(!$this->input->get('module') || empty($this->input->get('module'))) showmessage(L('illegal_parameters'));
		
		$module_api = pc_base::load_app_class('module_api');
		if(!$module_api->uninstall($this->input->get('module'))) showmessage($module_api->error_msg, 'blank');
		else showmessage(L('uninstall_success'),'blank','','','var w = h = \'60%\';if (is_mobile()) {w = h = \'100%\';}if (w==\'100%\' && h==\'100%\') {var drag = false;} else {var drag = true;}var diag = new Dialog({id:\'module_id\',title:\''.L('update_backup').'\',url:\''.SELF.'?m=admin&c=cache_all&a=init&pc_hash='.$_SESSION['pc_hash'].'\',width:w,height:h,modal:true,draggable:drag});diag.onClose=function(){dr_install_confirm();};diag.show();');
	}
	
	/**
	 * 更新模块缓存
	 */
	public function cache() {
		echo '<script type="text/javascript">window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.href = \'?m=admin&c=cache_all&a=init&pc_hash='.$_SESSION['pc_hash'].'\';ownerDialog.close();</script>';
		//showmessage(L('update_cache').L('success'), '', '', 'install');
	}
}
?>