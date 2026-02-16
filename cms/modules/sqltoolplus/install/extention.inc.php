<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'sqltoolplus', 'parentid'=>$menu_data['id'], 'm'=>'sqltoolplus', 'c'=>'index', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-briefcase', 'listorder'=>0, 'display'=>'1'), true);
$language = array('sqltoolplus'=>'SQL工具箱');
?>