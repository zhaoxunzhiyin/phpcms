 <?php 
defined('IN_CMS') or exit('Access Denied');
defined('UNINSTALL') or exit('Access Denied');
//unlink(PC_PATH.'modules/bdts/install.lock');
dr_dir_delete(CACHE_PATH.'/caches_bdts/', true);
$type_db = pc_base::load_model('type_model');
$typeid = $type_db->delete(array('module'=>'bdts'));
if(!$typeid) return FALSE;
 ?>