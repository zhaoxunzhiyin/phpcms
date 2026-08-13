<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'content_publish', 'm' => 'content', 'c' => 'content', 'a'=>'init'));
$parentid = $menu_db->insert(array('name'=>'custom', 'parentid'=>$menu_data['id'], 'm'=>'custom', 'c'=>'custom', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-th-large', 'listorder'=>0, 'display'=>'1'), true);

$menu_db->insert(array('name'=>'add', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'add:add,760,500', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));

$language = array('custom'=>'自定义资料管理');
?>