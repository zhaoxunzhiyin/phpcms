<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'bdts_config', 'parentid'=>$menu_data['id'], 'm'=>'bdts', 'c'=>'bdts', 'a'=>'config', 'data'=>'', 'icon'=>'fa fa-internet-explorer', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'bdts_add', 'parentid'=>$parentid, 'm'=>'bdts', 'c'=>'bdts', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'url_add', 'parentid'=>$parentid, 'm'=>'bdts', 'c'=>'bdts', 'a'=>'add:url_add,600,112', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'log_index', 'parentid'=>$parentid, 'm'=>'bdts', 'c'=>'bdts', 'a'=>'log_index', 'data'=>'', 'icon'=>'fa fa-calendar', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'bdts_help', 'parentid'=>$parentid, 'm'=>'bdts', 'c'=>'bdts', 'a'=>'help:help', 'data'=>'', 'icon'=>'fa fa-question-circle', 'listorder'=>0, 'display'=>'1'));
$language = array('bdts_config'=>'百度主动推送','bdts_add'=>'批量推送','url_add'=>'手动推送','log_index'=>'推送日志','bdts_help'=>'在线帮助');
?>