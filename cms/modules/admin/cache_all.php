<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class cache_all extends admin {
	private $input,$sitemodel_db,$linkage_db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->sitemodel_db = pc_base::load_model('sitemodel_model');
		$this->linkage_db = pc_base::load_model('linkage_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}

	public function init() {
		$show_header = true;
		if ($this->input->get('is_ajax') || IS_AJAX) {
			$modules = array(
				array('function' => 'module'),
				array('mod' => 'admin', 'file' => 'sites', 'function' => 'set_cache'),
				array('function' => 'category'),
				array('function' => 'page'),
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
				if (isset($m['mod']) && $m['mod'] && $m['function']) {
					if ($m['file'] == '') $m['file'] = $m['function'];
					$module = getcache('modules', 'commons');
					if ($module && in_array($m['mod'], array_keys($module))) {
						$cache = pc_base::load_app_class($m['file'], $m['mod']);
						$cache->{$m['function']}();
					}
				} else {
					$this->cache_api->cache($m['function'], isset($m['param']) ? $m['param'] : '');
				}
			}
			dr_json(1, L('site_cache_completed'));
		} else {
			$list = array(
				array('name' => L('update').L('module'), 'function' => 'module'),
				array('name' => L('update').L('sites'), 'mod' => 'admin', 'file' => 'sites', 'function' => 'set_cache'),
				array('name' => L('update').L('category'), 'function' => 'category'),
				array('name' => L('update').L('category_page'), 'function' => 'page'),
				array('name' => L('update').L('downservers'), 'function' => 'downservers'),
				array('name' => L('update').L('badword_name'), 'function' => 'badword'),
				array('name' => L('update').L('ipbanned'), 'function' => 'ipbanned'),
				array('name' => L('update').L('keylink'), 'function' => 'keylink'),
				array('name' => L('update').L('position'), 'function' => 'position'),
				array('name' => L('update').L('admin_role'), 'function' => 'admin_role'),
				array('name' => L('update').L('urlrule'), 'function' => 'urlrule'),
				array('name' => L('update').L('sitemodel'), 'function' => 'sitemodel'),
				array('name' => L('update').L('type'), 'function' => 'type', 'param' => 'content'),
				array('name' => L('update').L('workflow'), 'function' => 'workflow'),
				array('name' => L('update').L('dbsource'), 'function' => 'dbsource'),
				array('name' => L('update').L('member_setting'), 'function' => 'member_setting'),
				array('name' => L('update').L('member_group'), 'function' => 'member_group'),
				array('name' => L('update').L('membermodel'), 'function' => 'membermodel'),
				array('name' => L('update').L('member_model_field'), 'function' => 'member_model_field'),
				array('name' => L('update').L('search_type'), 'function' => 'type', 'param' => 'search'),
				array('name' => L('update').L('search_setting'), 'function' => 'search_setting'),
				array('name' => L('update_vote_setting'), 'function' => 'vote_setting'),
				array('name' => L('update_link_setting'), 'function' => 'link_setting'),
				array('name' => L('update').L('special'), 'function' => 'special'),
				array('name' => L('update').L('setting'), 'function' => 'setting'),
				array('name' => L('update').L('database'), 'function' => 'database'),
				array('name' => L('update_formguide_model'), 'function' => 'formguidemodel'),
				array('name' => L('update').L('cache_copyfrom'), 'function' => 'copyfrom'),
				array('name' => L('clear_files'), 'function' => 'del_file'),
				array('name' => L('clear_logs'), 'function' => 'update_log'),
				array('name' => L('update').L('remote_attachment'), 'function' => 'attachment_remote'),
				array('name' => L('update_attachment'), 'function' => 'attachment'),
				array('name' => L('update_thumb'), 'function' => 'update_thumb'),
			);
			$module_more = $module = array();
			$module = $this->sitemodel_db->select(array('type'=>0, 'disabled'=>0), "*", '', 'sort,modelid');
			if ($module) {
				$limit = 10;
				if (dr_count($module) > $limit) {
					$module_more = array_slice($module, $limit);
					$module = array_slice($module, 0, $limit);
				}
			}
			$linkage_more = $linkage = array();
			$linkage = $this->linkage_db->select();
			if ($linkage) {
				$limit = 10;
				if (dr_count($linkage) > $limit) {
					$linkage_more = array_slice($linkage, $limit);
					$linkage = array_slice($linkage, 0, $limit);
				}
			}
			include $this->admin_tpl('cache_all');
		}
	}

	// 执行更新缓存
	public function public_cache() {

		$function = dr_safe_replace($this->input->get('id'));
		$param = $this->input->get('param');
		$file = $this->input->get('file');
		$mod = $this->input->get('mod');
		if ($mod && $function) {
			if ($file == '') $file = $function;
			$module = getcache('modules', 'commons');
			if ($module && in_array($mod, array_keys($module))) {
				$cache = pc_base::load_app_class($file, $mod);
				$cache->{$function}();
			}
		} else {
			$this->cache_api->cache($function, $param ? $param : '');
		}

		dr_json(1, L('update').L('database_success'), 0);
	}
}
?>