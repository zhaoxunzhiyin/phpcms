<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 收藏url，必须登录
 * @param url 地址，需urlencode，防止乱码产生
 * @param title 标题，需urlencode，防止乱码产生
 * @return {1:成功;-1:未登录;-2:缺少参数}
 */
if(empty(pc_base::load_sys_class('input')->get('title')) || empty(pc_base::load_sys_class('input')->get('url'))) {
	exit('-2');	
} else {
	$title = pc_base::load_sys_class('input')->get('title');
	$title = urldecode($title);
	$title = new_html_special_chars($title);
	$url = safe_replace(urldecode(pc_base::load_sys_class('input')->get('url')));
	$url = trim_script($url);
}
$callback = safe_replace(pc_base::load_sys_class('input')->get('callback'));
//判断是否登录	
$cms_auth = param::get_cookie('auth');
if($cms_auth) {
	list($userid, $password) = explode("\t", sys_auth($cms_auth, 'DECODE', get_auth_key('login')));
	$userid = intval($userid);
	if($userid >0) {

	} else {
		exit(trim_script($callback).'('.json_encode(array('status'=>-1)).')');
	} 
} else {
	exit(trim_script($callback).'('.json_encode(array('status'=>-1)).')');
}

$favorite_db = pc_base::load_model('favorite_model');
$data = array('title'=>$title, 'url'=>$url, 'adddate'=>SYS_TIME, 'userid'=>$userid);
//根据url判断是否已经收藏过。
$is_exists = $favorite_db->get_one(array('url'=>$url, 'userid'=>$userid));
if(!$is_exists) {
	$favorite_db->insert($data);
}
exit(trim_script($callback).'('.json_encode(array('status'=>1)).')');
?>