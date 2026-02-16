<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取微信文章接口
 */
$isadmin = intval(pc_base::load_sys_class('input')->get('isadmin'));
$userid = intval(pc_base::load_sys_class('input')->get('userid'));
$groupid = intval(pc_base::load_sys_class('input')->get('groupid'));
$siteid = intval(pc_base::load_sys_class('input')->get('siteid'));
$rid = md5(FC_NOW_URL.pc_base::load_sys_class('input')->get_user_agent().pc_base::load_sys_class('input')->ip_address().$userid);
if(!$siteid) $siteid = get_siteid();
$field = pc_base::load_sys_class('input')->get('field');
$fieldname = pc_base::load_sys_class('input')->get('fieldname');
$data = pc_base::load_sys_class('input')->post('info');
$url = $data[$field];
if (!$url) {
	dr_json(0, L($fieldname.'不能为空'));
}

$html = dr_catcher_data($url, 5, true, 1);
if (!$html) {
	dr_json(0, L('没有获取到任何内容'));
}

$preg = 'id="js_content" style="visibility: hidden; opacity: 0; ">';
if (preg_match('/'.$preg.'(.+)<\/div>/sU', $html, $mt)) {
	pc_base::load_sys_class('upload','',0);
	$upload = new upload(pc_base::load_sys_class('input')->get('module'),intval(pc_base::load_sys_class('input')->get('catid')),$siteid);
	$upload->set_userid($userid);
	$body = trim($mt[1]);
	$body = str_replace(array('style="display: none;"', ''), '', $body);
	$body = str_replace('data-src=', 'src=', $body);
	// 下载远程图片
	if (intval(pc_base::load_sys_class('input')->get('is_esi')) && preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $body, $imgs)) {
		$downloadfiles = [];
		foreach ($imgs[3] as $img) {
			$ext = get_image_ext($img);
			if (!$ext) {
				continue;
			}
			// 下载图片
			if (intval(pc_base::load_sys_class('input')->get('is_esi')) && strpos($img, 'http') === 0) {
				if (!$isadmin && check_upload($userid)) {
					//用户存储空间已满
					log_message('debug', '用户存储空间已满');
				} else {
					$arr = parse_url($img);
					$domain = $arr['host'];
					if ($domain) {
						$sitedb = pc_base::load_model('site_model');
						$data = $sitedb->select();
						$sites = array();
						foreach ($data as $t) {
							$site_domain = parse_url($t['domain']);
							if ($site_domain['port']) {
								$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
							} else {
								$sites[$site_domain['host']] = $t['siteid'];
							}
						}
						if (isset($sites[$domain])) {
							// 过滤站点域名
						} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
							// 过滤附件白名单
						} else {
							if(strpos($img, '://') === false) continue;
							$zj = 0;
							$remote = get_cache('attachment');
							if ($remote) {
								foreach ($remote as $t) {
									if (strpos($t['url'], $domain) !== false) {
										$zj = 1;
										break;
									}
								}
							}
							if ($zj == 0) {
								// 可以下载文件
								// 下载远程文件
								$rt = $upload->down_file(array(
									'url' => $img,
									'timeout' => 5,
									'watermark' => intval(pc_base::load_sys_class('input')->get('watermark')),
									'attachment' => $upload->get_attach_info(intval(pc_base::load_sys_class('input')->get('attachment')), intval(pc_base::load_sys_class('input')->get('image_reduce'))),
									'file_ext' => $ext,
								));
								$data = array();
								if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
									$att_db = pc_base::load_model('attachment_model');
									$att = $att_db->get_one(array('userid'=>$userid,'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
									if ($att) {
										$data = dr_return_data($att['aid'], 'ok');
										// 删除现有附件
										// 开始删除文件
										$storage = new storage(pc_base::load_sys_class('input')->get('module'),intval(pc_base::load_sys_class('input')->get('catid')),$siteid);
										$storage->delete($upload->get_attach_info((int)pc_base::load_sys_class('input')->get('attachment')), $rt['data']['file']);
										$rt['data'] = get_attachment($att['aid']);
									}
								}
								if (!$data) {
									$rt['data']['isadmin'] = $isadmin;
									$data = $upload->save_data($rt['data'], 'ueditor:'.$rid);
								}
								upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
								$downloadfiles[] = $data['code'];
								$body = str_replace($img, $rt['data']['url'], $body);
							}
						}
					}
				}
			}
		}
		isset($downloadfiles) && $downloadfiles && pc_base::load_sys_class('cache')->set_data('downloadfiles-'.$siteid, $downloadfiles, 3600);
	}
} else {
	dr_json(0, L('没有获取到文章内容'));
}
if (preg_match('/<meta property="og:title" content="(.+)"/U', $html, $mt)) {
	$title = trim($mt[1]);
} else {
	dr_json(0, L('没有获取到文章标题'));
}

dr_json(1, L('导入成功'), array(
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