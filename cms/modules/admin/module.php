<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class module extends admin {
	private $input,$cache_api,$db,$module;
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->db = pc_base::load_model('module_model');
	}
	
	public function init() {
		$show_header = true;
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
		foreach ($modules as $dir => $path) {
			if (is_file(PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php')) {
				$cfg = require PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php';
				if ($author) {
					$modules[$dir]['author'] = $author;
				}
				if ($version) {
					$modules[$dir]['version'] = $version;
				}
			}
		}
		$directory = $dirs_arr;
		include $this->admin_tpl('module_list');
	}
	
	/**
	 * 模块安装
	 */
	public function install() {
		$this->module = $this->input->post('module') ? $this->input->post('module') : dr_json(0, L('illegal_parameters'));
		$module_api = pc_base::load_app_class('module_api');
		if (!$module_api->check($this->module)) dr_json(0, $module_api->error_msg);
		if ($module_api->install()) {
			$this->cache();
			dr_json(1, L('success_module_install'), '');
		} else {
			dr_json(0, $module_api->error_msg);
		}
	}
	
	/**
	 * 模块卸载
	 */
	public function uninstall() {
		$this->module = $this->input->post('module') ? $this->input->post('module') : dr_json(0, L('illegal_parameters'));
		$module_api = pc_base::load_app_class('module_api');
		if(!$module_api->uninstall($this->module)) {
			dr_json(0, $module_api->error_msg);
		} else {
			$this->cache();
			dr_json(1, L('uninstall_success'), '');
		}
	}
	
	/**
	 * 更新模块缓存
	 */
	public function cache() {
		$modules = array(
			array('function' => 'module'),
			array('mod' => 'admin', 'file' => 'sites', 'function' => 'set_cache'),
			array('function' => 'category'),
			array('function' => 'downservers'),
			array('function' => 'badword'),
			array('function' => 'ipbanned'),
			array('function' => 'keylink'),
			array('function' => 'position'),
			array('function' => 'admin_role'),
			array('function' => 'urlrule'),
			array('function' => 'sitemodel'),
			array('function' => 'type', 'param' => 'content'),
			array('function' => 'workflow'),
			array('function' => 'dbsource'),
			array('function' => 'member_setting'),
			array('function' => 'member_group'),
			array('function' => 'membermodel'),
			array('function' => 'member_model_field'),
			array('function' => 'type', 'param' => 'search'),
			array('function' => 'search_setting'),
			array('function' => 'vote_setting'),
			array('function' => 'link_setting'),
			array('function' => 'special'),
			array('function' => 'setting'),
			array('function' => 'database'),
			array('function' => 'formguidemodel'),
			array('function' => 'copyfrom'),
			array('function' => 'del_file'),
			array('function' => 'attachment_remote'),
		);
		foreach ($modules as $m) {
			if ($m['mod'] && $m['function']) {
				if ($m['file'] == '') $m['file'] = $m['function'];
				$M = getcache('modules', 'commons');
				if (in_array($m['mod'], array_keys($M))) {
					$cache = pc_base::load_app_class($m['file'], $m['mod']);
					$cache->{$m['function']}();
				}
			} else {
				$this->cache_api->cache($m['function'], $m['param']);
			}
		}
	}
}
?>