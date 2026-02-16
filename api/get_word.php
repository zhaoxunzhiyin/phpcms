<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取Word接口
 */
$isadmin = intval(pc_base::load_sys_class('input')->get('isadmin'));
$userid = intval(pc_base::load_sys_class('input')->get('userid'));
$groupid = intval(pc_base::load_sys_class('input')->get('groupid'));
$siteid = intval(pc_base::load_sys_class('input')->get('siteid'));
$module = trim(pc_base::load_sys_class('input')->get('module'));
$catid = intval(pc_base::load_sys_class('input')->get('catid'));
$watermark = intval(pc_base::load_sys_class('input')->get('watermark'));
$attachment = intval(pc_base::load_sys_class('input')->get('attachment'));
$image_reduce = intval(pc_base::load_sys_class('input')->get('image_reduce'));
$rid = md5(FC_NOW_URL.pc_base::load_sys_class('input')->get_user_agent().pc_base::load_sys_class('input')->ip_address().$userid);
if(!$siteid) $siteid = get_siteid();

if (defined('SYS_CSRF') && SYS_CSRF && csrf_hash() != (string)$_GET['token']) {
	dr_json(0, L('跨站验证禁止上传文件'));
}
$grouplist = getcache('grouplist', 'member');
if (!$isadmin && !$grouplist[$groupid]['allowattachment']) {
	dr_json(0, L('您的用户组不允许上传文件'));
}

if (!$isadmin && check_upload($userid)) {
	dr_json(0, L('用户存储空间已满'));
}
pc_base::load_sys_class('upload','',0);
$upload = new upload($module,$catid,$siteid);
$upload->set_userid($userid);
$rt = $upload->upload_file(array(
	'path' => '',
	'form_name' => 'file_data',
	'file_exts' => array('docx'),
	'file_size' => dr_site_value('upload_maxsize', $siteid) * 1024 * 1024,
	'attachment' => $upload->get_attach_info($attachment, 0),
));
if (!$rt['code']) {
	exit(dr_array2string($rt));
}
$data = array();
if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
	$att_db = pc_base::load_model('attachment_model');
	$att = $att_db->get_one(array('userid'=>$userid,'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
	if ($att) {
		$data = dr_return_data($att['aid'], 'ok');
		// 删除现有附件
		// 开始删除文件
		$storage = new storage($module,$catid,$siteid);
		$storage->delete($upload->get_attach_info($attachment), $rt['data']['file']);
		$rt['data'] = get_attachment($att['aid']);
		if ($rt['data']) {
			$rt['data']['name'] = $rt['data']['filename'];
			$rt['data']['size'] = $rt['data']['filesize'];
			$rt['data']['ext'] = $rt['data']['fileext'];
		}
	}
}

// 附件归档
if (!$data) {
	$rt['data']['isadmin'] = $isadmin;
	$data = $upload->save_data($rt['data'], 'ueditor:'.$rid);
	if (!$data['code']) {
		exit(dr_array2string($data));
	}
	// 标记附件
	upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
}

if ($rt && $data) {
	$title = $rt['data']['name'];
} else {
	dr_json(0, L('文件上传失败'));
}
if (!$rt['data']['path'] && $rt['data']['file']) {
	$rt['data']['path'] = $rt['data']['file'];
}
if (!$rt['data']['path']) {
	dr_json(0, L('没有获取到文件内容'));
}
if (!$title) {
	dr_json(0, L('没有获取到文件标题'));
}
$body = readWordToHtml($rt['data']['path'], $module, $isadmin, $userid, $catid, $siteid, $watermark, $attachment, $image_reduce, $rid);
if (!$body) {
	dr_json(0, L('没有获取到Word内容'));
}

dr_json(1, L('导入成功'), array(
	'file' => $rt['data']['url'],
	'title' => $title,
	'keyword' => dr_get_keywords($title),
	'content' => $body,
));

// 验证附件上传权限，直接返回1 表示空间不够
function check_upload($uid) {
	$groupid = intval(pc_base::load_sys_class('input')->get('groupid'));
	$grouplist = getcache('grouplist', 'member');
	if ($isadmin) {
		return;
	}
	// 获取用户总空间
	$total = abs((int)$grouplist[$groupid]['filesize']) * 1024 * 1024;
	if ($total) {
		// 判断空间是否满了
		$filesize = get_member_filesize($uid);
		if ($filesize >= $total) {
			return 1;
		}
	}
	return;
}

// 用户已经使用附件空间
function get_member_filesize($uid) {
	$db = pc_base::load_model('attachment_model');
	$db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.$uid.' and isadmin='.$isadmin);
	$row = $db->fetch_array();
	return intval($row[0]['filesize']);
}
?>