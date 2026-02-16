 <?php 
defined('IN_CMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
$cache = pc_base::load_sys_class('cache');
$cache->del_file('fieldlist')
?>