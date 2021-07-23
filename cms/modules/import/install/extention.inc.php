<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'import', 'parentid'=>29, 'm'=>'import', 'c'=>'import', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-database', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_import', 'parentid'=>$parentid, 'm'=>'import', 'c'=>'import', 'a'=>'add', 'data'=>'', 'icon'=>'fa fa-plus', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'delete_import', 'parentid'=>$parentid, 'm'=>'import', 'c'=>'import', 'a'=>'delete', 'data'=>'', 'icon'=>'fa fa-trash-o', 'listorder'=>0, 'display'=>'0'));
$language = array('import'=>'外部数据导入', 'add_import'=>'添加数据导入规则', 'delete_import'=>'删除数据导入配置');
?>