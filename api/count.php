<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 点击统计
 */
$id = (int)pc_base::load_sys_class('input')->get('id');
$modelid = (int)pc_base::load_sys_class('input')->get('modelid');
if (IS_AJAX) {
	if (!$id || !$modelid) {
		dr_jsonp(0, L('阅读统计: id参数不完整'));
	}
	$db = pc_base::load_model('hits_model');
	$hitsid = 'c-'.$modelid.'-'.$id;
	list($userid, $password) = explode("\t", sys_auth(param::get_cookie('auth'), 'DECODE', get_auth_key('login')));
	$name = 'module-'.md5($hitsid).'-hits-'.$id.USER_HTTP_CODE.$modelid.intval($userid);
	$views = pc_base::load_sys_class('input')->get_cookie($name);
	if (!(int)pc_base::load_sys_class('input')->get('qx') && $views) {
		dr_jsonp(1, $views, '不重复统计');
	}
	$db = pc_base::load_model('hits_model');
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) dr_jsonp(1, 0);
	$views = (int)$r['views'] + 1;
	$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
	$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayviews'] + 1) : 1;
	$weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekviews'] + 1) : 1;
	$monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthviews'] + 1) : 1;
	$db->update(array('views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME), array('hitsid'=>$hitsid));
	pc_base::load_sys_class('input')->set_cookie($name, $views, 300);
	// 输出
	dr_jsonp(1, $views);
}
$module = pc_base::load_sys_class('input')->get('module');
if($modelid && $id) {
	$hitsid = 'c-'.$modelid.'-'.$id;
	hits($hitsid);
	$r = get_count($hitsid);
	if(!$r) exit;
	extract($r);
	echo "\$('#todaydowns').html('$dayviews');";
	echo "\$('#weekdowns').html('$weekviews');";
	echo "\$('#monthdowns').html('$monthviews');";
} elseif($module && $id) {
	if((preg_match('/([^a-z0-9_\-]+)/i',$module))) exit('1');
	$hitsid = $module.'-'.$id;
	hits($hitsid);
	$r = get_count($hitsid);
	if(!$r) exit;
	extract($r);
}

/**
 * 获取点击数量
 * @param $hitsid
 */
function get_count($hitsid) {
	$db = pc_base::load_model('hits_model');
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) return false;
	return $r;
}

/**
 * 点击次数统计
 * @param $contentid
 */
function hits($hitsid) {
	$db = pc_base::load_model('hits_model');
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) return false;
	$views = $r['views'] + 1;
	$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
	$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayviews'] + 1) : 1;
	$weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekviews'] + 1) : 1;
	$monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthviews'] + 1) : 1;
	$sql = array('views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME);
	return $db->update($sql, array('hitsid'=>$hitsid));
}
exit('$(\'#hits\').html(\''.$views.'\');');