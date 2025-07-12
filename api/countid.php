<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 点击统计
 */
$db = pc_base::load_model('hits_model');
if(pc_base::load_sys_class('input')->get('modelid') && pc_base::load_sys_class('input')->get('id')) {
	$model_arr = array();
	$model_arr = getcache('model','commons');
	$modelid = intval(pc_base::load_sys_class('input')->get('modelid'));
	$hitsid = 'c-'.$modelid.'-'.intval(pc_base::load_sys_class('input')->get('id'));
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) exit;
	extract($r);
	echo "\$('#todaydowns".pc_base::load_sys_class('input')->get('id')."').html('$dayviews');";
	echo "\$('#weekdowns".pc_base::load_sys_class('input')->get('id')."').html('$weekviews');";
	echo "\$('#monthdowns".pc_base::load_sys_class('input')->get('id')."').html('$monthviews');";
} elseif(pc_base::load_sys_class('input')->get('module') && pc_base::load_sys_class('input')->get('id')) {
	$module = pc_base::load_sys_class('input')->get('module');
	if((preg_match('/([^a-z0-9_\-]+)/i',$module))) exit('1');
	$hitsid = $module.'-'.intval(pc_base::load_sys_class('input')->get('id'));
	$r = $db->get_one(array('hitsid'=>$hitsid));
	if(!$r) exit;
	extract($r);
}
exit('$(\'#hits'.pc_base::load_sys_class('input')->get('id').'\').html(\''.$views.'\');');