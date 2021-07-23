<?php
error_reporting(E_ALL);
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$parentid = $menu_db->insert(array('name'=>'formguide', 'parentid'=>29, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-table', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'formguide_add', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'formguide_edit', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'form_info_list', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide_info', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-list', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'formguide_disabled', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'disabled', 'data'=>'', 'icon'=>'fa fa-ban', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'formguide_delete', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'formguide_stat', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'stat', 'data'=>'', 'icon'=>'fa fa-bar-chart-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'add_public_field', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide_field', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'list_public_field', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide_field', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-code', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'module_setting', 'parentid'=>$parentid, 'm'=>'formguide', 'c'=>'formguide', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-gears', 'listorder'=>0, 'display'=>'0'));

$language = array('formguide'=>'表单向导', 'formguide_add'=>'添加表单向导', 'formguide_edit'=>'修改表单向导', 'form_info_list'=>'信息列表', 'formguide_disabled'=>'禁用表单', 'formguide_delete'=>'删除表单', 'formguide_stat'=>'表单统计', 'add_public_field'=>'添加公共字段', 'list_public_field'=>'管理公共字段', 'module_setting'=>'模块配置');
?>