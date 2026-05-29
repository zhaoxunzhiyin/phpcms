<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'content_publish', 'm' => 'content', 'c' => 'content', 'a'=>'init'));
$parentid = $menu_db->insert(array('name'=>'cm_conf', 'parentid'=>$menu_data['id'], 'm'=>'customfield', 'c'=>'customfield', 'a'=>'manage_list', 'data'=>'', 'icon'=>'fa fa-code', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'cm_cate', 'parentid'=>$parentid, 'm'=>'customfield', 'c'=>'customfield', 'a'=>'category_list', 'data'=>'', 'icon'=>'fa fa-reorder', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'customfiled', 'parentid'=>$parentid, 'm'=>'customfield', 'c'=>'customfield', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-code', 'listorder'=>0, 'display'=>'1'));
$language = array('cm_conf'=>'字段设置','cm_cate'=>'分类管理','customfiled'=>'字段管理');
?>

