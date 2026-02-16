<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 点击统计
 */
if(pc_base::load_sys_class('input')->get('modelid') && pc_base::load_sys_class('input')->get('id')) {
	$model_arr = array();
	$model_arr = getcache('model','commons');
	$modelid = intval(pc_base::load_sys_class('input')->get('modelid'));
	$hitsid = 'c-'.$modelid.'-'.intval(pc_base::load_sys_class('input')->get('id'));
	hits($hitsid);
	$r = get_count($hitsid);
	if(!$r) exit;
	extract($r);
	echo "\$('#todaydowns').html('$dayviews');";
	echo "\$('#weekdowns').html('$weekviews');";
	echo "\$('#monthdowns').html('$monthviews');";
} elseif(pc_base::load_sys_class('input')->get('module') && pc_base::load_sys_class('input')->get('id')) {
	$module = pc_base::load_sys_class('input')->get('module');
	if((preg_match('/([^a-z0-9_\-]+)/i',$module))) exit('1');
	$hitsid = $module.'-'.intval(pc_base::load_sys_class('input')->get('id'));
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