<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'announce', 'parentid'=>$menu_data['id'], 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'init', 'data'=>'s=1', 'icon'=>'fa fa-bullhorn', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'announce_add', 'parentid'=>$parentid, 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'add:add,700,500', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_announce', 'parentid'=>$parentid, 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'edit', 'data'=>'s=1', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'check_announce', 'parentid'=>$parentid, 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'init', 'data'=>'s=2', 'icon'=>'fa fa-check', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'overdue', 'parentid'=>$parentid, 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'init', 'data'=>'s=3', 'icon'=>'fa fa-bell-slash', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'del_announce', 'parentid'=>$parentid, 'm'=>'announce', 'c'=>'admin_announce', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));

$language = array('announce'=>'公告', 'announce_add'=>'添加公告', 'edit_announce'=>'编辑公告', 'check_announce'=>'审核公告', 'overdue'=>'过期公告', 'del_announce'=>'删除公告');
?>