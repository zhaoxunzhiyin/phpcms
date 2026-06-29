<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 点击统计
 */
$id = (int)pc_base::load_sys_class('input')->get('id');
$modelid = (int)pc_base::load_sys_class('input')->get('modelid');
$module = pc_base::load_sys_class('input')->get('module');
if((preg_match('/([^a-z0-9_\-]+)/i', $module))) $module = '';
if (IS_AJAX) {
	if (!$id) {
		dr_jsonp(0, L('阅读统计: id参数不完整'));
	}
	$db = pc_base::load_model('hits_model');
	list($userid, $password) = explode("\t", sys_auth(param::get_cookie('auth'), 'DECODE', get_auth_key('login')));
	if ($modelid && $id) {
		$hitsid = 'c-'.$modelid.'-'.$id;
		$catid = (int)dr_value($modelid, $id, 'catid');
		$name = 'module-'.md5($hitsid).'-hits-'.$id.USER_HTTP_CODE.$modelid.intval($userid);
	} elseif ($module && $id) {
		$hitsid = $module.'-'.$id;
		$name = 'module-'.md5($hitsid).'-hits-'.$id.USER_HTTP_CODE.$module.intval($userid);
	}
	if ($hitsid) {
		$views = pc_base::load_sys_class('input')->get_cookie($name);
		if (!(int)pc_base::load_sys_class('input')->get('qx') && $views) {
			dr_jsonp(1, (int)$views, '不重复统计');
		}
		$r = $db->get_one(array('hitsid'=>$hitsid));
		$views = (int)$r['views'] + 1;
		$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? (int)$r['dayviews'] : (int)$r['yesterdayviews'];
		$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ((int)$r['dayviews'] + 1) : 1;
		$weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ((int)$r['weekviews'] + 1) : 1;
		$monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ((int)$r['monthviews'] + 1) : 1;
		$data = array('hitsid'=>$hitsid,'views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME);
		if ($catid) {
			$data['catid'] = $catid;
		}
		$db->insert($data, false, true);
		pc_base::load_sys_class('input')->set_cookie($name, $views, 300);
	}
	// 输出
	dr_jsonp(1, (int)$views);
}
if ($modelid && $id) {
	$hitsid = 'c-'.$modelid.'-'.$id;
	$catid = (int)dr_value($modelid, $id, 'catid');
	hits($hitsid, $catid);
	$r = get_count($hitsid);
	echo '$(\'#todaydowns\').html(\''.(int)$r['dayviews'].'\');';
	echo '$(\'#weekdowns\').html(\''.(int)$r['weekviews'].'\');';
	echo '$(\'#monthdowns\').html(\''.(int)$r['monthviews'].'\');';
} elseif ($module && $id) {
	$hitsid = $module.'-'.$id;
	hits($hitsid);
	$r = get_count($hitsid);
}

/**
 * 获取点击数量
 * @param $hitsid
 */
function get_count($hitsid) {
	$db = pc_base::load_model('hits_model');
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r){
		$r['views'] = 0;
		$r['dayviews'] = 0;
		$r['weekviews'] = 0;
		$r['monthviews'] = 0;
	}
	return $r;
}

/**
 * 点击次数统计
 * @param $contentid
 */
function hits($hitsid, $catid = 0) {
	$db = pc_base::load_model('hits_model');
	$r = $db->get_one(array('hitsid'=>$hitsid));
	$views = (int)$r['views'] + 1;
	$yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? (int)$r['dayviews'] : (int)$r['yesterdayviews'];
	$dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ((int)$r['dayviews'] + 1) : 1;
	$weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ((int)$r['weekviews'] + 1) : 1;
	$monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ((int)$r['monthviews'] + 1) : 1;
	$data = array('hitsid'=>$hitsid,'views'=>$views,'yesterdayviews'=>$yesterdayviews,'dayviews'=>$dayviews,'weekviews'=>$weekviews,'monthviews'=>$monthviews,'updatetime'=>SYS_TIME);
	if ($catid) {
		$data['catid'] = $catid;
	}
	return $db->insert($data, false, true);
}
exit('$(\'#hits\').html(\''.(int)$r['views'].'\');');