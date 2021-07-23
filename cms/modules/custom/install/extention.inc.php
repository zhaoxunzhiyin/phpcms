<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'custom', 'parentid'=>821, 'm'=>'custom', 'c'=>'custom', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-th-large', 'listorder'=>0, 'display'=>'1'), true);

$menu_db->insert(array('name'=>'add_custom', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'edit_custom', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_custom', 'parentid'=>$parentid, 'm'=>'custom', 'c'=>'custom', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));

$language = array('custom'=>'自定义资料管理');
?>