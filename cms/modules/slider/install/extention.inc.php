<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'slider', 'parentid'=>821, 'm'=>'slider', 'c'=>'slider', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-photo', 'listorder'=>0, 'display'=>'1'), true);

$menu_db->insert(array('name'=>'add_slider', 'parentid'=>$parentid, 'm'=>'slider', 'c'=>'slider', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_slider', 'parentid'=>$parentid, 'm'=>'slider', 'c'=>'slider', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_slider', 'parentid'=>$parentid, 'm'=>'slider', 'c'=>'slider', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'add_postion', 'parentid'=>$parentid, 'm'=>'slider', 'c'=>'slider', 'a'=>'add_type', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'list_postion', 'parentid'=>$parentid, 'm'=>'slider', 'c'=>'slider', 'a'=>'list_type', 'data'=>'', 'icon'=>'fa fa-reorder', 'listorder'=>0, 'display'=>'1'));

$language = array('slider'=>'幻灯片管理', 'add_slider'=>'添加幻灯片', 'edit_slider'=>'编辑幻灯片', 'delete_slider'=>'删除幻灯片', 'add_postion'=>'添加幻灯片位置', 'list_postion'=>'幻灯片位置管理');
?>