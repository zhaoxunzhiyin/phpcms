<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'link', 'parentid'=>$menu_data['id'], 'm'=>'link', 'c'=>'link', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-link', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'add:add,700,450', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_link', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'link_setting', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'list_type', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'list_type', 'data'=>'', 'icon'=>'fa fa-reorder', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'add_type', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'add:add_type,500,280', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'check_register', 'parentid'=>$parentid, 'm'=>'link', 'c'=>'link', 'a'=>'check_register', 'data'=>'', 'icon'=>'fa fa-check', 'listorder'=>0, 'display'=>'1'));

$language = array('link'=>'友情链接', 'add_link'=>'添加友情链接', 'edit_link'=>'编辑友情链接', 'delete_link'=>'删除友情链接', 'link_setting'=>'模块配置', 'add_type'=>'添加类别', 'list_type'=>'分类管理', 'check_register'=>'审核申请');
?>