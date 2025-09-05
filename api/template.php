<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 动态调用模板
 */
header('Access-Control-Allow-Origin: *');
$siteid = intval(pc_base::load_sys_class('input')->get('siteid'));
!$siteid && $siteid = (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
$module = dr_safe_filename(pc_base::load_sys_class('input')->get('module'));
$file = dr_safe_filename(pc_base::load_sys_class('input')->get('name'));

!$module && $module = 'content';

$data = [
	'siteid' => $siteid,
	'file' => $file,
	'module' => $module,
];

if (!$file) {
	$html = 'name不能为空';
} else {
	pc_base::load_sys_class('service')->assign('siteid', $siteid);
	pc_base::load_sys_class('service')->assign(pc_base::load_sys_class('input')->get('', true));
	$default_style = dr_site_info('default_style', $siteid);
	ob_start();
	pc_base::load_sys_class('service')->display($module, $file, $default_style);
	$html = ob_get_contents();
	ob_clean();
	
	$data['call_value'] = pc_base::load_sys_class('service')->call_value;
}

dr_json(1, $html, $data);
?>