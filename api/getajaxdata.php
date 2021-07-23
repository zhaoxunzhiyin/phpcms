<?php
defined('IN_CMS') or exit('No permission resources.');

$db = '';
$db = pc_base::load_model('content_model');
if($input->get('modelid') && $input->get('categoryid')) {
	$model_arr = array();
	$model_arr = getcache('model','commons');
	$catid = intval($input->get('catid'));
	$modelid = intval($input->get('modelid'));
	$db->set_model($modelid);
	$steps = $input->get('steps') ? intval($input->get('steps')) : 0;
	$status = $steps ? $steps : 99;
	if($input->get('reject')) $status = 0;
	$where = 'catid='.$input->get('categoryid').' AND status='.$status;
	$datas = $db->listinfo($where,'id desc',$input->get('page'),$input->get('pagelength'));
	$pages = $db->pages;
	foreach ($datas as $r) {
		if($r['islink']) {
			$url='<a href="'.$r['url'].'">';
		} elseif(strpos($r['url'],'http://')!==false) {
			$url='<a href="'.$r['url'].'">';
		} else {
			$url='<a href="'.$release_siteurl.$r['url'].'">';
		}
		echo '<li>'.$url.'<span class="state tody date">'.mdate($r['updatetime']).'</span><span class="title">'.str_cut($r['title'],100).'</span></a></li>';
	}
}
?>