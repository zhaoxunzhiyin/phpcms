<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 搜索
 */
$get = pc_base::load_sys_class('input')->get();
$catid = (int)$get['catid'];
$siteid = (int)$get['siteid'];
$mobile = (int)$get['mobile'];
$code = $get['keyword'];
$code && $code = dr_authcode(dr_safe_replace($code), 'ENCODE');
$param = array();
if ($catid) {
	$model_arr = getcache('model', 'commons');
	$MODEL = $model_arr[dr_cat_value($catid, 'modelid')];
	unset($model_arr);
	$sitemodel = pc_base::load_sys_class('cache')->get('sitemodel');
	$cache = $sitemodel[$MODEL['tablename']];
	$cache['field']['catdir'] = true;
	$cache['field']['catid'] = true;
	$cache['field']['order'] = true;
	foreach ($get as $key => $value) {
		if ($cache['field'] && isset($cache['field'][$key])) {
			$param[$key] = $value;
		}
	}
	// 跳转url
	dr_redirect(dr_module_search_url($catid, $param, 'keyword', $code ? 'CODE'.$code : '', $mobile));
} else {
	$cache = array();
	$cache['field']['siteid'] = true;
	$cache['field']['typeid'] = true;
	$cache['field']['time'] = true;
	foreach ($get as $key => $value) {
		if ($cache['field'] && isset($cache['field'][$key])) {
			$param[$key] = $value;
		}
	}
	// 跳转url
	dr_redirect(dr_search_url($param, 'keyword', $code ? 'CODE'.$code : '', $siteid, $mobile));
}