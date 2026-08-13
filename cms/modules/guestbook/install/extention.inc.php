<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'guestbook', 'parentid'=>$menu_data['id'], 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-comment', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_guestbook', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_guestbook', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_guestbook', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'guestbook_setting', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'add_type', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'add:add_type,500,280', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_type', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'edit_type', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'list_type', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'list_type', 'data'=>'', 'icon'=>'fa fa-reorder', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'check_register', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'check_register', 'data'=>'', 'icon'=>'fa fa-check', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'show_guestbook', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'show', 'data'=>'', 'icon'=>'fa fa-eye', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'check_guestbook', 'parentid'=>$parentid, 'm'=>'guestbook', 'c'=>'guestbook', 'a'=>'check', 'data'=>'', 'icon'=>'fa fa-check', 'listorder'=>0, 'display'=>'0'));

$language = array('guestbook'=>'留言板', 'add_guestbook'=>'添加留言板', 'edit_guestbook'=>'编辑留言板', 'delete_guestbook'=>'删除留言板', 'guestbook_setting'=>'模块配置', 'add_type'=>'添加类别', 'edit_type'=>'编辑类别', 'list_type'=>'分类管理', 'check_register'=>'审核申请', 'show_guestbook'=>'留言板查看', 'check_guestbook'=>'留言板审核');
?>