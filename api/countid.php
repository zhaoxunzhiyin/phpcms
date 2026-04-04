<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 点击统计
 */
$id = (int)pc_base::load_sys_class('input')->get('id');
$modelid = (int)pc_base::load_sys_class('input')->get('modelid');
$module = pc_base::load_sys_class('input')->get('module');
$db = pc_base::load_model('hits_model');
if($modelid && $id) {
	$hitsid = 'c-'.$modelid.'-'.$id;
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r){
		$r['views'] = 0;
		$r['dayviews'] = 0;
		$r['weekviews'] = 0;
		$r['monthviews'] = 0;
	}
	echo '$(\'#todaydowns'.$id.'\').html(\''.$r['dayviews'].'\');';
	echo '$(\'#weekdowns'.$id.'\').html(\''.$r['weekviews'].'\');';
	echo '$(\'#monthdowns'.$id.'\').html(\''.$r['monthviews'].'\');';
} elseif($module && $id) {
	if((preg_match('/([^a-z0-9_\-]+)/i',$module))) exit('1');
	$hitsid = $module.'-'.$id;
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r){
		$r['views'] = 0;
	}
}
exit('$(\'#hits'.$id.'\').html(\''.$r['views'].'\');');