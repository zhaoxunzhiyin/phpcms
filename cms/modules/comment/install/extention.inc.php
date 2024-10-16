<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$menu_data = $menu_db->get_one(array('name' => 'module_list', 'm' => 'admin', 'c' => 'module'));
$parentid = $menu_db->insert(array('name'=>'comment', 'parentid'=>$menu_data['id'], 'm'=>'comment', 'c'=>'comment_admin', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-comments', 'listorder'=>0, 'display'=>'1'), true);
$menu_content_data = $menu_db->get_one(array('name' => 'content_publish', 'm' => 'content', 'c' => 'content', 'a'=>'init'));
$mid = $menu_db->insert(array('name'=>'comment_manage', 'parentid'=>$menu_content_data['id'], 'm'=>'comment', 'c'=>'comment_admin', 'a'=>'listinfo', 'data'=>'', 'icon'=>'fa fa-comments', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'comment_check', 'parentid'=>$mid, 'm'=>'comment', 'c'=>'check', 'a'=>'checks', 'data'=>'', 'icon'=>'fa fa-check', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'comment_list', 'parentid'=>$parentid, 'm'=>'comment', 'c'=>'comment_admin', 'a'=>'lists', 'data'=>'', 'icon'=>'fa fa-comments', 'listorder'=>0, 'display'=>'0'));

$language = array('comment'=>'评论', 'comment_manage'=>'评论管理', 'comment_check'=>'评论审核', 'comment_list'=>'评论列表');
?>