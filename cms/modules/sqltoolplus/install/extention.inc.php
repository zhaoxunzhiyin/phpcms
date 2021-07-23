<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'sqltoolplus', 'parentid'=>29, 'm'=>'sqltoolplus', 'c'=>'index', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-briefcase', 'listorder'=>0, 'display'=>'1'), true);
$language = array('sqltoolplus'=>'SQL工具箱加强版');
?>