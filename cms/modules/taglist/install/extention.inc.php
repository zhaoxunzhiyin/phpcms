<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'taglist', 'parentid'=>$menu_data['id'], 'm'=>'taglist', 'c'=>'taglist', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-tag', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_taglist', 'parentid'=>$parentid, 'm'=>'taglist', 'c'=>'taglist', 'a'=>'add:add,450,280', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_taglist', 'parentid'=>$parentid, 'm'=>'taglist', 'c'=>'taglist', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_taglist', 'parentid'=>$parentid, 'm'=>'taglist', 'c'=>'taglist', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));

$language = array('taglist'=>'TAG管理', 'add_taglist'=>'添加TAG', 'edit_taglist'=>'编辑TAG', 'delete_taglist'=>'删除TAG');
?>