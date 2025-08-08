<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');

$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'fclient', 'parentid'=>$menu_data['id'], 'm'=>'fclient', 'c'=>'fclient', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-sitemap', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_fclient', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'add:add,700,450', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_fclient', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_fclient', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'fclient_setting', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'download', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'down', 'data'=>'', 'icon'=>'fa fa-download', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'update', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'update', 'data'=>'', 'icon'=>'fa fa-refresh', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'web_site_admin', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'sync_admin', 'data'=>'', 'icon'=>'fa fa-user', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'send', 'parentid'=>$parentid, 'm'=>'fclient', 'c'=>'fclient', 'a'=>'sync_web', 'data'=>'', 'icon'=>'fa fa-send', 'listorder'=>0, 'display'=>'0'));

$language = array('fclient'=>'客户站群', 'add_fclient'=>'添加客户站群', 'edit_fclient'=>'修改客户站群', 'delete_fclient'=>'删除客户站群', 'fclient_setting'=>'客户站群配置', 'download'=>'下载客户端', 'update'=>'升级版本', 'web_site_admin'=>'访问网站后台', 'send'=>'通信测试');
?>