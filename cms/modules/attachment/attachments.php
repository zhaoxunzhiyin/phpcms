<?php 
defined('IN_CMS') or exit('No permission resources.');
if(param::get_cookie('sys_lang')) {
	define('SYS_STYLE',param::get_cookie('sys_lang'));
} else {
	define('SYS_STYLE','zh-cn');
}
class attachments {
	private $input,$cache,$att_db,$upload,$imgext,$userid,$rid,$grouplist,$roleid,$isadmin,$groupid,$sitedb,$cookie_att;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		pc_base::load_app_func('global');
		pc_base::load_sys_class('upload','',0);
		$this->imgext = array('jpg','gif','png','bmp','jpeg','webp');
		$this->grouplist = getcache('grouplist', 'member');
		$this->roleid = IS_ADMIN ? param::get_session('roleid') : 0;
		$this->isadmin = $this->roleid ? 1 : 0;
		$this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
		$this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().intval($this->userid));
		$this->groupid = param::get_cookie('_groupid') ? param::get_cookie('_groupid') : 8;
		if(!$this->isadmin && !$this->grouplist[$this->groupid]['allowattachment']) {
			showmessage(L('您的用户组不允许上传文件'));
		}
	}
	
	// 验证权限脚本
	public function check_upload_auth($editor = 0) {

		$error = '';
		if (!IS_API && defined('SYS_CSRF') && SYS_CSRF && csrf_hash() != (string)$_GET['token']) {
			$error = L('跨站验证禁止上传文件');
		} elseif ($this->isadmin && !$this->userid) {
			$error = L('请登录在操作');
		} elseif ($this->isadmin || IS_ADMIN) {
			return;
		} elseif (!$this->isadmin && !$this->grouplist[$this->groupid]['allowattachment']) {
			$error = L('您的用户组不允许上传文件');
		} elseif (!$this->isadmin && $this->check_upload($this->userid)) {
			$error = L('用户存储空间已满');
		}

		// 挂钩点 验证格式
		$rt2 = pc_base::load_sys_class('hooks')::trigger_callback('check_upload_auth', $this->userid, $this->isadmin, $this->groupid, $error);
		if ($rt2 && isset($rt2['code'])) {
			$error = $rt2['code'] ? '' : $rt2['msg'];
		}

		if ($error) {
			if ($editor == 1) {
				return L($error);
			} else {
				dr_json(0, L($error));
			}
		}

		return;
	}
	
	/**
	 * 常规上传
	 */
	public function upload() {
		// 验证上传权限
		if($this->check_upload_auth(1)) return false;
		$args = $this->input->get('args');
		$p = geth5init($args);
		if (!$p) {
			$result = array("uploaded"=>false,"error"=>array("message"=>L('attachment_parameter_error')));
			exit(dr_array2string($result));
		}
		$argskeys = dr_string2array(dr_authcode($args, 'DECODE'));
		$authkey = $this->input->get('authkey');
		foreach($argskeys as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) {
			$result = array("uploaded"=>false,"error"=>array("message"=>L('attachment_parameter_error')));
			exit(dr_array2string($result));
		}
		$module = trim($this->input->get('module'));
		$catid = intval($this->input->get('catid'));
		$upload = new upload($module,$catid,$p['siteid']);
		$upload->set_userid($this->userid);
		$rt = $upload->upload_file(array(
			'path' => '',
			'form_name' => 'upload',
			'file_exts' => explode('|', strtolower($p['file_types_post'])),
			'file_size' => $p['file_size_limit'] * 1024 * 1024,
			'watermark' => dr_site_value('ueditor', $p['siteid']) ? 1 : intval($p['watermark_enable']),
			'attachment' => $upload->get_attach_info(intval($p['attachment']), intval($p['image_reduce'])),
		));
		if (!$rt['code']) {
			$result = array("uploaded"=>false,"error"=>array("message"=>$rt['msg']));
			exit(dr_array2string($result));
			//exit(dr_array2string($rt));
		}
		$data = array();
		if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
			$att_db = pc_base::load_model('attachment_model');
			$att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
			if ($att) {
				$data = dr_return_data($att['aid'], 'ok');
				// 删除现有附件
				// 开始删除文件
				$storage = new storage($module,$catid,$p['siteid']);
				$storage->delete($upload->get_attach_info((int)$p['attachment']), $rt['data']['file']);
				$rt['data'] = get_attachment($att['aid']);
				if ($rt['data']) {
					$rt['data']['name'] = $rt['data']['filename'];
					$rt['data']['size'] = $rt['data']['filesize'];
				}
			}
		}
		
		// 附件归档
		if (!$data) {
			$rt['data']['isadmin'] = $this->isadmin;
			$data = $upload->save_data($rt['data']);
			if (!$data['code']) {
				$result = array("uploaded"=>false,"error"=>array("message"=>$data['msg']));
				exit(dr_array2string($result));
			}
		}
		
		if($rt && $data){
			$fn = intval($this->input->get('CKEditorFuncNum'));
			upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
			$result = array("uploaded"=>true,
				"fileName"=>$rt['data']['name'],
				"url"=>$rt['data']['url'],
				"error"=>array(
					"message"=>""
				)
			);
		}else{
			$result = array('uploaded'=>false,'error'=>array('message'=>L('上传错误')));
		}
		exit(dr_array2string($result));
	}
	/**
	 * h5upload上传附件
	 */
	public function h5upload(){
		if(IS_POST){
			// 验证上传权限
			$this->check_upload_auth();
			if($this->input->post('h5_auth_key') != md5(SYS_KEY.$this->input->post('H5UPLOADSESSID')) || (!$this->input->post('isadmin') && !$this->grouplist[$this->input->post('groupid')]['allowattachment'])) dr_json(0, '');
			$args = $this->input->post('args');
			$p = geth5init($args);
			if (!$p) {
				dr_json(0, L('attachment_parameter_error'));
			}
			define('SITEID', $p['siteid']);
			$upload = new upload($this->input->post('module'),$this->input->post('catid'),$p['siteid']);
			$upload->set_userid($this->userid);
			$rt = $upload->upload_file(array(
				'path' => '',
				'form_name' => 'file_data',
				'file_exts' => explode('|', strtolower($p['file_types_post'])),
				'file_size' => $p['file_size_limit'] * 1024 * 1024,
				'watermark' => intval($p['watermark_enable']),
				'attachment' => $upload->get_attach_info(intval($p['attachment']), (int)$p['image_reduce']),
			));
			if (!$rt['code']) {
				exit(dr_array2string($rt));
			}
			$data = array();
			if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
				$att_db = pc_base::load_model('attachment_model');
				$att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
				if ($att) {
					$data = dr_return_data($att['aid'], 'ok');
					// 删除现有附件
					// 开始删除文件
					$storage = new storage($this->input->post('module'),$this->input->post('catid'),$p['siteid']);
					$storage->delete($upload->get_attach_info((int)$p['attachment']), $rt['data']['file']);
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
				$rt['data']['isadmin'] = $this->input->post('isadmin');
				$data = $upload->save_data($rt['data']);
				if (!$data['code']) {
					exit(dr_array2string($data));
				}
			}
			
			// 缩略图
			if (dr_is_image($rt['data']['url']) && ($p['thumb_width'] > 0 || $p['thumb_height'] > 0)) {
				thumb($data['code'], $p['thumb_width'], $p['thumb_height'] ,intval($p['watermark_enable']));
			}
			
			if($rt && $data) {
				if($upload->uploadedfiles[0]['isimage'] || $rt['data']['isimage'] || dr_in_array($rt['data']['ext'], array('gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp'))) {
					$rt['data']['ext'] = 1;
				}
				$rt['data']['id'] = $data['code'];
				$rt['data']['size'] = format_file_size($rt['data']['size']);
				dr_json(1, L('att_upload_succ'), $rt['data']);
			} else {
				dr_json(0, $rt['msg']);
			}
		} else {
			$args = $this->input->get('args');
			$ct = (int)$this->input->get('ct'); // 当已有数量
			$p = dr_string2array(dr_authcode($args, 'DECODE'));
			if (!$p) {
				showmessage(L('attachment_parameter_error'));
			}
			$authkey = $this->input->get('authkey');
			foreach($p as $k=>$v) {
				$arraykey[$k] = $v;
			}
			$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
			if(upload_key($argskey) != $authkey) showmessage(L('attachment_parameter_error'));
			extract(geth5init($args));
			$att_not_used = $this->cache->get_data('att_json');
			if(empty($att_not_used) || !isset($att_not_used)) $page = 0;
			if(!empty($att_not_used)) $page = 4;
			include $this->admin_tpl('h5upload');
		}
	}
	/**
	 * 下载附件
	 */
	public function download(){
		// 验证上传权限
		$this->check_upload_auth();
		$filename = $this->input->post('filename');
		if(empty($filename)) dr_json(0, L('文件地址不能为空'));
		if (strpos($filename, 'http') !== 0 ) {
			dr_json(0, L('下载文件地址必须是https或者http开头'));
		}
		// 获取扩展名
		$ext = str_replace('.', '', trim(strtolower(strrchr($filename, '.')), '.'));
		if (strlen($ext) > 6) {
			foreach (array('jpg', 'jpeg', 'png', 'gif', 'webp') as $i) {
				if (strpos($filename, $i) !== false) {
					$ext = $i;
					break;
				}
			}
			if (strlen($ext) > 6) {
				$ext2 = str_replace('#', '', trim(strtolower(strrchr($filename, '#')), '#'));
				if ($ext2) {
					$ext = $ext2;
					$filename = substr($filename, 0, strlen($filename)-strlen($ext2)-1);
				}
			}
			if (strlen($ext) > 6 || !$ext) {
				dr_json(0, L('无法获取到文件扩展名，请在URL后方加入扩展名字符串，例如：#jpg'));
			}
		}
		$args = $this->input->post('args');
		$p = geth5init($args);
		if (!$p) {
			dr_json(0, L('attachment_parameter_error'));
		}
		$argskeys = dr_string2array(dr_authcode($args, 'DECODE'));
		$authkey = $this->input->post('authkey');
		foreach($argskeys as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) dr_json(0, L('attachment_parameter_error'));
		$upload = new upload($this->input->post('module'),$this->input->post('catid'),$p['siteid']);
		$upload->set_userid($this->userid);
		// 下载远程文件
		$rt = $upload->down_file(array(
			'url' => $filename,
			'file_ext' => $ext,
			'attachment' => $upload->get_attach_info(intval($p['attachment']), intval($p['image_reduce'])),
		));
		if (!$rt['code']) {
			exit(dr_array2string($rt));
		}
		$data = array();
		if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rt['data']['md5']) {
			$att_db = pc_base::load_model('attachment_model');
			$att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rt['data']['md5'],'fileext'=>$rt['data']['ext'],'filesize'=>$rt['data']['size']));
			if ($att) {
				$data = dr_return_data($att['aid'], 'ok');
				// 删除现有附件
				// 开始删除文件
				$storage = new storage($this->input->post('module'),$this->input->post('catid'),$p['siteid']);
				$storage->delete($upload->get_attach_info((int)$p['attachment']), $rt['data']['file']);
				$rt['data'] = get_attachment($att['aid']);
				if ($rt['data']) {
					$rt['data']['name'] = $rt['data']['filename'];
					$rt['data']['size'] = $rt['data']['filesize'];
				}
			}
		}
		
		// 附件归档
		if (!$data) {
			$rt['data']['isadmin'] = $this->isadmin;
			$data = $upload->save_data($rt['data']);
			if (!$data['code']) {
				exit(dr_array2string($data));
			}
		}
		
		upload_json($data['code'],$rt['data']['url'],$rt['data']['name'],format_file_size($rt['data']['size']));
		exit(dr_array2string(array('code' => 1, 'msg' => L('上传成功'), 'id' => $data['code'], 'info' => $rt['data'])));
	}
	/**
	 * 下载远程图片
	 */
	public function down_img() {

		$args = $this->input->get('args');
		$p = dr_string2array(dr_authcode($args, 'DECODE'));
		if (!$p) {
			showmessage(L('attachment_parameter_error'));
		}
		$authkey = $this->input->get('authkey');
		foreach($p as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) showmessage(L('attachment_parameter_error'));

		$value = $this->input->post('value');
		if (!$value) {
			dr_json(0, L('内容不能为空'));
		}
		$module = trim($this->input->get('module'));
		$catid = intval($this->input->get('catid'));

		$base64 = strpos($value, ';base64,');
		if ($base64) {
			$value = str_replace('_"data:image', '"data:image', $value);
		}

		// 找远程图片
		$exts = $arrs = [];
		$temp = preg_replace('/<pre(.*)<\/pre>/siU', '', $value);
		$temp = preg_replace('/<code(.*)<\/code>/siU', '', $temp);
		if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+)\\2/i", $temp, $imgs)) {
			foreach ($imgs[3] as $img) {
				if ($base64 && preg_match('/^(data:\s*image\/(\w+);base64,)/i', $img, $result)) {
					// 处理图片
					$ext = strtolower($result[2]);
					if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
						continue;
					}
					$content = base64_decode(str_replace($result[1], '', $img));
					if (strlen($content) > 30000000) {
						continue;
					}
					$arrs[] = $img;
					$exts[] = $ext;
				} else {
					$arr = parse_url($img);
					$domain = $arr['host'];
					if ($domain) {
						$this->sitedb = pc_base::load_model('site_model');
						$data = $this->sitedb->select();
						$sites = array();
						foreach ($data as $t) {
							$site_domain = parse_url($t['domain']);
							if ($site_domain['port']) {
								$sites[$site_domain['host'].':'.$site_domain['port']] = $t['siteid'];
							} else {
								$sites[$site_domain['host']] = $t['siteid'];
							}
							if ($t['mobile_domain']) {
								$site_mobile_domain = parse_url($t['mobile_domain']);
								if ($site_mobile_domain['port']) {
									$sites[$site_mobile_domain['host'].':'.$site_mobile_domain['port']] = $t['siteid'];
								} else {
									$sites[$site_mobile_domain['host']] = $t['siteid'];
								}
							}
						}
						if (isset($sites[$domain])) {
							// 过滤站点域名
						} elseif (strpos(SYS_UPLOAD_URL, $domain) !== false) {
							// 过滤附件白名单
						} else {
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
								$ext = get_image_ext($img);
								if (!$ext) {
									continue;
								}
								$arrs[] = $img;
								$exts[] = $ext;
							}
						}
					}
				}
			}
		}

		if (!$arrs){
			dr_json(0, L('没有分析出远程图片'));
		}

		// 储存缓存
		$vid = md5($value);
		$this->cache->set_auth_data('down_img_'.$vid, [
			'url' => $arrs,
			'ext' => $exts,
			'value' => $value,
			'attachment' => $p['attachment'],
			'image_reduce' => $p['image_reduce'],
			'watermark' => $p['watermark_enable'],
			'rid' => $this->rid,
		]);

		dr_json(1, '?m=attachment&c=attachments&a=down_img_list&module='.$module.'&catid='.$catid.'&siteid='.$p['siteid'].'&attachment='.$p['attachment'].'&vid='.$vid);
	}
	/**
	 * 下载远程图片
	 */
	public function down_img_list() {

		$vid = $this->input->get('vid');
		if (!$vid) {
			dr_json(0, L('vid参数不能为空'));
		}
		$module = trim($this->input->get('module'));
		$catid = intval($this->input->get('catid'));
		$siteid = intval($this->input->get('siteid'));
		$attachment = intval($this->input->get('attachment'));

		$rt = $this->cache->get_auth_data('down_img_'.$vid);
		if (!$rt) {
			dr_json(0, L('数据读取失败，请重试'));
		}

		if (IS_POST) {

			$post = $this->input->post('data');
			if (!$post) {
				dr_json(0, L('还没有下载完毕'));
			}

			$err = $ct = 0;
			$downloadfiles = [];
			foreach ($post as $id => $aid) {
				if ($aid) {
					$rt['value'] = str_replace($rt['url'][$id], dr_get_file($aid), $rt['value']);
					$downloadfiles[] = $aid;
					$ct++;
				} else {
					$err++;
				}
			}
			isset($downloadfiles) && $downloadfiles && pc_base::load_sys_class('cache')->set_data('downloadfiles-'.$siteid, $downloadfiles, 3600);
			$this->cache->del_auth_data('down_img_'.$vid);
			dr_json(1, L('共下载成功'.$ct.'张，失败'.$err.'张'), $rt['value']);
		}

		$list = $rt['url'];
		$down_url = '?m=attachment&c=attachments&a=down_img_url&module='.$module.'&catid='.$catid.'&siteid='.$siteid.'&attachment='.$attachment.'&vid='.$vid.'&token='.csrf_hash();
		include $this->admin_tpl('down_img');exit;
	}

	/**
	 * 下载远程图片
	 */
	public function down_img_url() {

		// 验证上传权限
		$this->check_upload_auth();

		$vid = $this->input->get('vid');
		if (!$vid) {
			dr_json(0, L('vid参数不能为空'));
		}
		$module = trim($this->input->get('module'));
		$catid = intval($this->input->get('catid'));
		$siteid = intval($this->input->get('siteid'));
		$attachment = intval($this->input->get('attachment'));
		$upload = new upload($module,$catid,$siteid);
		$upload->set_userid($this->userid);

		$rt = $this->cache->get_auth_data('down_img_'.$vid);
		if (!$rt) {
			dr_json(0, L('数据读取失败，请重试'));
		}

		$id = $this->input->get('id');
		if (!isset($rt['ext'][$id]) || !$rt['ext'][$id]) {
			dr_json(0, L('扩展名识别失败，请重试'));
		}

		// 下载图片
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/i', (string)$rt['url'][$id], $result)) {
			// 处理图片
			$content = base64_decode(str_replace($result[1], '', (string)$rt['url'][$id]));
			$rs = $upload->base64_image([
				'ext' => $rt['ext'][$id],
				'content' => $content,
				'watermark' => $rt['watermark'],
				'attachment' => $upload->get_attach_info(intval($rt['attachment']), $rt['image_reduce']),
			]);
			if ($rs['code']) {
				$data = array();
				if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rs['data']['md5']) {
					$att_db = pc_base::load_model('attachment_model');
					$att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rs['data']['md5'],'fileext'=>$rs['data']['ext'],'filesize'=>$rs['data']['size']));
					if ($att) {
						$data = dr_return_data($att['aid'], 'ok');
						// 删除现有附件
						// 开始删除文件
						$storage = new storage($this->module,$catid,$this->siteid);
						$storage->delete($upload->get_attach_info((int)$attachment), $rs['data']['file']);
						$rs['data'] = get_attachment($att['aid']);
					}
				}
				if (!$data) {
					$rs['data']['isadmin'] = $this->isadmin;
					$data = $upload->save_data($rs['data'], 'ueditor:'.$this->rid);
				}
				upload_json($data['code'],$rs['data']['url'],$rs['data']['name'],format_file_size($rs['data']['size']));
				dr_json(1, $data['code']);
			} else {
				dr_json(0, $rs['msg']);
			}
		} else {
			// 正常下载
			// 下载远程文件
			$rs = $upload->down_file([
				'url' => html_entity_decode((string)$rt['url'][$id]),
				'timeout' => 5,
				'watermark' => $rt['watermark'],
				'attachment' => $upload->get_attach_info(intval($rt['attachment']), $rt['image_reduce']),
				'file_ext' => $rt['ext'][$id],
			]);
			if ($rs['code']) {
				$data = array();
				if (defined('SYS_ATTACHMENT_CF') && SYS_ATTACHMENT_CF && $rs['data']['md5']) {
					$att_db = pc_base::load_model('attachment_model');
					$att = $att_db->get_one(array('userid'=>intval($this->userid),'filemd5'=>$rs['data']['md5'],'fileext'=>$rs['data']['ext'],'filesize'=>$rs['data']['size']));
					if ($att) {
						$data = dr_return_data($att['aid'], 'ok');
						// 删除现有附件
						// 开始删除文件
						$storage = new storage($this->module,$catid,$this->siteid);
						$storage->delete($upload->get_attach_info((int)$attachment), $rs['data']['file']);
						$rs['data'] = get_attachment($att['aid']);
					}
				}
				if (!$data) {
					$rs['data']['isadmin'] = $this->isadmin;
					$data = $upload->save_data($rs['data'], 'ueditor:'.$this->rid);
				}
				upload_json($data['code'],$rs['data']['url'],$rs['data']['name'],format_file_size($rs['data']['size']));
				dr_json(1, $data['code']);
			} else {
				dr_json(0, $rs['msg']);
			}
		}
	}
	/**
	 * 获取临时未处理文件列表
	 */
	public function att_not(){
		$args = $this->input->get('args');
		$ct = (int)$this->input->get('ct'); // 当已有数量
		$p = dr_string2array(dr_authcode($args, 'DECODE'));
		if (!$p) {
			showmessage(L('attachment_parameter_error'));
		}
		$authkey = $this->input->get('authkey');
		foreach($p as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) showmessage(L('attachment_parameter_error'));
		extract(geth5init($args));
		//获取临时未处理文件列表
		$att = $this->att_not_used();
		include $this->admin_tpl('att_not');
	}
	
	/**
	 * 加载图片库
	 */
	public function album_load() {
		$uploadtime= '';
		$this->att_db= pc_base::load_model('attachment_model');
		$ct = (int)$this->input->get('ct'); // 当已有数量
		$args = $this->input->get('args');
		$p = dr_string2array(dr_authcode($args, 'DECODE'));
		if (!$p) {
			showmessage(L('attachment_parameter_error'));
		}
		$authkey = $this->input->get('authkey');
		foreach($p as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) showmessage(L('attachment_parameter_error'));
		extract(geth5init($args));
		$array_test = explode('|', $file_types_post);
		$length = sizeof($array_test);
		$s_str = '';
		for($i=0;$i<$length;$i++){
			$s_str .= "'".$array_test[$i]."',";
		}
		$s_str = substr($s_str, 0, strlen($s_str) - 1);
		$where = "fileext in (".$s_str.") AND module<>'member' AND siteid=".$siteid." AND userid=".(int)$this->userid;
		if ($this->isadmin && $this->roleid && cleck_admin($this->roleid)) {} else {$where .= " AND isadmin=".$this->isadmin." AND userid=".(int)$this->userid;}
		if($this->input->get('info')){
			extract($this->input->get('info'));
			$filename = safe_replace($filename);
			if($filename) $where .= " AND `filename` LIKE '%$filename%' ";
			if($uploadtime) {
				$start_uploadtime = strtotime($uploadtime.' 00:00:00');
				$stop_uploadtime = strtotime($uploadtime.' 23:59:59');
				$where .= " AND `uploadtime` >= '$start_uploadtime' AND `uploadtime` <= '$stop_uploadtime'";				
			}
		}
		pc_base::load_sys_class('form');
		$page = $this->input->get('page') ? $this->input->get('page') : '1';
		$infos = $this->att_db->listinfo($where, 'uploadtime desc,aid desc', $page, SYS_ATTACHMENT_PAGESIZE);
		foreach($infos as $n=>$v){
			$ext = fileext($v['filepath']);
			if(in_array($ext,$this->imgext)) {
				$infos[$n]['src']=dr_get_file_url($v);
				$infos[$n]['width']='80';
			} else {
				$infos[$n]['src']=file_icon($v['filepath']);
				$infos[$n]['width']='64';
			}
		}
		$pages = $this->att_db->pages;
		include $this->admin_tpl('album_list');
	}
	
	/**
	 * 目录浏览模式添加图片
	 */
	public function album_dir() {
		if(!$this->isadmin) return false;
		$args = $this->input->get('args');
		$ct = (int)$this->input->get('ct'); // 当已有数量
		$p = dr_string2array(dr_authcode($args, 'DECODE'));
		if (!$p) {
			showmessage(L('attachment_parameter_error'));
		}
		$authkey = $this->input->get('authkey');
		foreach($p as $k=>$v) {
			$arraykey[$k] = $v;
		}
		$argskey = str_replace('"', '', dr_array2string(implode(',', $arraykey)));
		if(upload_key($argskey) != $authkey) showmessage(L('attachment_parameter_error'));
		extract(geth5init($args));
		$dir = $this->input->get('dir') && trim($this->input->get('dir')) ? str_replace(array('..\\', '../', './', '.\\','..','.*'), '', trim($this->input->get('dir'))) : '';
		$filepath = SYS_UPLOAD_PATH.$dir;
		$list = glob($filepath.'/'.'*');
		if(!empty($list)) rsort($list);
		$local = str_replace(array(PC_PATH, CMS_PATH ,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('','',DIRECTORY_SEPARATOR), $filepath);
		$url = ($dir == '.' || $dir=='') ? SYS_UPLOAD_URL : SYS_UPLOAD_URL.str_replace('.', '', $dir).'/';
		$show_header = true;
		include $this->admin_tpl('album_dir');
	}
	
	/**
	 * 设置h5upload上传的json格式cookie
	 */
	public function h5upload_json() {
		$aid = intval($this->input->get('aid'));
		$src = safe_replace($this->input->get('src'));
		$filename = safe_replace($this->input->get('filename'));
		$size = $this->input->get('size');
		upload_json($aid,$src,$filename,$size);
	}
	
	/**
	 * 删除h5upload上传的json格式cookie
	 */	
	public function h5upload_json_del() {
		$aid = intval($this->input->get('aid'));
		$src = safe_replace($this->input->get('src'));
		$filename = safe_replace($this->input->get('filename'));
		$size = $this->input->get('size');
		upload_json_del($aid,$src,$filename,$size);
	}

	private function att_not_used() {
		$this->att_db= pc_base::load_model('attachment_model');
		//获取临时未处理文件列表
		if($att_json = $this->cache->get_data('att_json')) {
			if($att_json) $att_cookie_arr = explode('||', $att_json);	
			foreach ($att_cookie_arr as $_att_c) $att[] = json_decode($_att_c,true);
			if(is_array($att) && !empty($att)) {
				foreach ($att as $n=>$v) {
					$ext = fileext($v['src']);
					if(in_array($ext,$this->imgext)) {
						$att[$n]['fileimg']=$v['src'];
						$att[$n]['width']='80';
						$att[$n]['filename']=urldecode($v['filename']);
					} else {
						$att[$n]['fileimg']=file_icon($v['src']);
						$att[$n]['width']='64';
						$att[$n]['filename']=urldecode($v['filename']);
					}
					$this->cookie_att .= '|'.$v['src'];
				}
			}			
		}
		return $att;
	}
	
	// 验证附件上传权限，直接返回1 表示空间不够
	public function check_upload($uid) {
		if ($this->isadmin) {
			return;
		}
		// 获取用户总空间
		$total = abs((int)$this->grouplist[$this->groupid]['filesize']) * 1024 * 1024;
		if ($total) {
			// 判断空间是否满了
			$filesize = $this->get_member_filesize($uid);
			if ($filesize >= $total) {
				return 1;
			}
		}
		return;
	}
	
	// 用户已经使用附件空间
	public function get_member_filesize($uid) {
		$db = pc_base::load_model('attachment_model');
		$db->query('SELECT sum(filesize) as filesize FROM `'.$db->dbprefix('attachment').'` where userid='.intval($uid).' and isadmin='.intval($this->isadmin));
		$row = $db->fetch_array();
		return intval($row[0]['filesize']);
	}
	
	final public static function admin_tpl($file, $m = '') {
		return admin_template($file, $m);
	}
}
?>