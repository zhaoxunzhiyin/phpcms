<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'vote', 'parentid'=>$menu_data['id'], 'm'=>'vote', 'c'=>'vote', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-pie-chart', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_vote', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'add:add,700,450', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'edit_vote', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'edit', 'data'=>'', 'icon'=>'fa fa-edit', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'delete_vote', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'vote_setting', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'statistics_vote', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'statistics', 'data'=>'', 'icon'=>'fa fa-bar-chart-o', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'statistics_userlist', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'statistics_userlist', 'data'=>'', 'icon'=>'fa fa-area-chart', 'listorder'=>0, 'display'=>'0'));
$menu_db->insert(array('name'=>'create_js', 'parentid'=>$parentid, 'm'=>'vote', 'c'=>'vote', 'a'=>'ajax:create_js', 'data'=>'', 'icon'=>'fa fa-refresh', 'listorder'=>0, 'display'=>'1'));

$language = array('vote'=>'投票', 'add_vote'=>'添加投票', 'edit_vote'=>'编辑投票','delete_vote'=>'删除投票', 'vote_setting'=>'投票配置', 'statistics_vote'=>'查看统计', 'statistics_userlist'=>'会员统计','create_js'=>'更新JS');
?>