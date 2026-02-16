<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取联动菜单接口
 */
$linkage_db = pc_base::load_model('linkage_model');
$code = dr_safe_replace(pc_base::load_sys_class('input')->get('code'));
$data = $linkage_db->get_one(array('code'=>$code));
if ($data['style']) {
	if (pc_base::load_sys_class('input')->get('parent_id')=='--') {
		exit(dr_array2string(array('data' => array(), 'html' => '')));
	}
	$pid = (int)pc_base::load_sys_class('input')->get('parent_id');
	$linkage = dr_linkage_list($code, $pid);
	if (!$pid && !$linkage) {
		$linkage = array(
			array(
				'region_id' => 0,
				'region_code' => '',
				'region_name' => '请在联动菜单管理，找到【'.$code.'】，点击一键生成按钮',
			)
		);
		exit(dr_array2string(array('data' => $linkage, 'html' => '')));
	} else if (!$linkage) {
		exit(dr_array2string(array('data' => array(), 'html' => '')));
	}
	$json = array();
	$html = '';
	foreach ($linkage as $v) {
		if ($v['pid'] == $pid) {
			$json[] = array(
				'region_id' => $v['ii'],
				'region_code' => $v['id'],
				'region_name' => $v['name']
			);
		}
	}
	exit(dr_array2string(array('data' => $json, 'html' => $html)));
} else {
	$linkage = dr_linkage_json($code);
	if (!$linkage) {
		$linkage = array(
			array(
				'value' => 0,
				'label' => '请在联动菜单管理，找到【'.$code.'】，点击一键生成按钮',
				'children' => array(),
			)
		);
	}
	exit('var linkage_'.$code.' = '.dr_array2string($linkage).';');
}
?>