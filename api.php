<?php 
/**
 *  index.php API 入口
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-7-26
 */
define('IS_DEV', 0);
define('CMS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include CMS_PATH.'cms/base.php';
$param = pc_base::load_sys_class('param');
$_userid = param::get_cookie('_userid');
$input = pc_base::load_sys_class('input');
if($_userid) {
	$member_db = pc_base::load_model('member_model');
	$_userid = intval($_userid);
	$memberinfo = $member_db->get_one(array('userid'=>$_userid),'islock');
	if($memberinfo['islock']) exit('<h1>Bad Request!</h1>');
}
$op = $input->get('op') && trim($input->get('op')) ? trim($input->get('op')) : exit('Operation can not be empty');
if ($input->get('callback') && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', $input->get('callback'))) '';
if (!preg_match('/([^a-z_]+)/i',$op) && file_exists(CMS_PATH.'api/'.$op.'.php')) {
	include CMS_PATH.'api/'.$op.'.php';
} else {
	exit('API handler does not exist');
}
?>