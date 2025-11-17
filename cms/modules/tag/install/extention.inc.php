<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$menu_data = $menu_db->get_one(array('name' => 'template_manager'));
$parentid = $menu_db->insert(array('name'=>'tag', 'parentid'=>$menu_data['id'], 'm'=>'tag', 'c'=>'tag', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-tags', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_tag', 'parentid'=>$parentid, 'm'=>'tag', 'c'=>'tag', 'a'=>'add:add,700,500', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_tag', 'parentid'=>$parentid, 'm'=>'tag', 'c'=>'tag', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_tag', 'parentid'=>$parentid, 'm'=>'tag', 'c'=>'tag', 'a'=>'del', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'tag_lists', 'parentid'=>$parentid, 'm'=>'tag', 'c'=>'tag', 'a'=>'lists', 'data'=>'', 'icon'=>'fa fa-tags', 'listorder'=>0, 'display'=>'0'));

$language = array('tag'=>'标签向导', 'add_tag'=>'添加标签向导', 'edit_tag'=>'修改标签向导', 'delete_tag'=>'删除标签向导', 'tag_lists'=>'标签向导列表');
?>