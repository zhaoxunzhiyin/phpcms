<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'message', 'parentid'=>$menu_data['id'], 'm'=>'message', 'c'=>'message', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-bell', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'message_send', 'parentid'=>$parentid, 'm'=>'message', 'c'=>'message', 'a'=>'add:message_send,550,300', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'send_one', 'parentid'=>$parentid, 'm'=>'message', 'c'=>'message', 'a'=>'send_one', 'data'=>'', 'icon'=>'fa fa-send', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'delete_message', 'parentid'=>$parentid, 'm'=>'message', 'c'=>'message', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'message_group_manage', 'parentid'=>$parentid, 'm'=>'message', 'c'=>'message', 'a'=>'message_group_manage', 'data'=>'', 'icon'=>'fa fa-list', 'listorder'=>0, 'display'=>'1'));

$language = array('message'=>'短消息', 'send_one'=>'发送消息', 'delete_message'=>'删除短消息', 'message_send'=>'群发短消息', 'message_group_manage'=>'群发短消息管理');
?>