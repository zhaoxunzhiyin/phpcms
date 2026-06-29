<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class manage extends admin {
	private $input,$db,$remote_db,$admin_username,$siteid,$type;
	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('attachment_model');
		$this->remote_db = pc_base::load_model('attachment_remote_model');
		$this->admin_username = param::get_cookie('admin_username');
		$this->siteid = $this->get_siteid();
	}	
	/**
	 * 附件列表
	 */
	public function init() {
		$modules = getcache('modules','commons');
		$field = [
			'userid' => [
				'issystem' => 1,
				'formtype' => 'text',
				'field' => 'userid',
				'name' => '账号',
			],
			'related' => [
				'issystem' => 1,
				'formtype' => 'text',
				'field' => 'related',
				'name' => L('附件归属'),
			],
			'fileext' => [
				'issystem' => 1,
				'formtype' => 'text',
				'field' => 'fileext',
				'name' => L('扩展名'),
			],
			'filename' => [
				'issystem' => 1,
				'formtype' => 'text',
				'field' => 'filename',
				'name' => L('附件名称'),
			],
			'filepath' => [
				'issystem' => 1,
				'formtype' => 'text',
				'field' => 'filepath',
				'name' => L('附件路径'),
			],
		];
		$remote = $this->remote_db->select();
		$param = $this->input->get();
		$pagesize = $param['limit'] ? $param['limit'] : SYS_ADMIN_PAGESIZE;
		$order = $param['order'] ? $param['order'] : 'uploadtime desc,aid desc';
		$page = $param['page'] ? $param['page'] : '1';
		$where = array();
		$where[] = "`siteid`='".$this->siteid."'";
		if(isset($param['remote']) && $param['remote']) $where[] = "`remote` = '".$param['remote']."'";
		if(isset($param['start_uploadtime']) && $param['start_uploadtime']) {
			$where[] = 'uploadtime BETWEEN ' . max((int)strtotime(strpos($param['start_uploadtime'], ' ') ? $param['start_uploadtime'] : $param['start_uploadtime'].' 00:00:00'), 1) . ' AND ' . ($param['end_uploadtime'] ? (int)strtotime(strpos($param['end_uploadtime'], ' ') ? $param['end_uploadtime'] : $param['end_uploadtime'].' 23:59:59') : SYS_TIME);
		}
		if ($param['field'] && $param['keyword'] && $param['field'] == 'aid') {
			// 按aid查询
			$id = [];
			$ids = explode(',', $param['keyword']);
			foreach ($ids as $i) {
				$id[] = (int)$i;
			}
			dr_count($id) == 1 ? $where[] = '`'.$param['field'].'`='.(int)$id[0] : $where[] = '`'.$param['field'].'` in ('.implode(',', $id).')';
			$param['keyword'] = htmlspecialchars($param['keyword']);
		} elseif ($param['field'] && $param['keyword'] && ($param['field'] == 'userid' || $field[$param['field']]['formtype'] == 'userid')) {
			$where[] = '`'.$param['field'].'`='.intval($param['keyword']);
		} elseif ($param['field'] && $param['keyword'] && substr_count($param['keyword'], ',') == 1 && preg_match('/[\+\-0-9\.]+,[\+\-0-9\.]+/', $param['keyword'])) {
			// BETWEEN 条件
			list($s, $e) = explode(',', $param['keyword']);
			$s = floatval($s);
			$e = floatval($e);
			if ($s == $e && $s == 0) {
				$where[] = '`'.$param['field'].'` = 0';
			}
			if (!$e && $s > 0) {
				$where[] = '`'.$param['field'].'` > '.$s;
			} else {
				$where[] = '`'.$param['field'].'` BETWEEN '.$s.' AND '.$e;
			}
		} elseif ($param['field'] && $param['keyword'] && (strpos($param['keyword'], '%') !== false || strpos($param['keyword'], ' ') !== false)) {
			// like 条件
			$arr = explode('%', str_replace(' ', '%', $param['keyword']));
			if (count($arr) == 1) {
				$where[] = '`'.$param['field'].'` LIKE "%'.trim($this->db->escape($param['keyword']), '%').'%"';
			} else {
				$wh = [];
				foreach ($arr as $c) {
					$c && $wh[] = '`'.$param['field'].'` LIKE "%'.trim($this->db->escape($param['keyword'])).'%"';
				}
				$wh ? $where[] = ('('.implode(strpos($param['keyword'], '%%') !== false ? ' AND ' : ' OR ', $wh).')') : '';
			}
		} elseif ($param['field'] && $param['keyword'] && is_numeric($param['keyword'])) {
			$where[] = '`'.$param['field'].'`='.$param['keyword'];
		} elseif ($param['field'] && $param['keyword']) {
			$where[] = '`'.$param['field'].'` LIKE "%'.trim($this->db->escape($param['keyword']), '%').'%"';
		}
		$status = $param['status'];
		if(isset($status) && ($status==1 || $status==0)) $where[] = "`status`='$status'";
		$module = $param['module'];
		if(isset($module) && $module) $where[] = "`module`='$module'";
		$datas = $this->db->listinfo(($where ? implode(' AND ', $where) : ''), $order, $page, $pagesize);
		$total = $this->db->count(($where ? implode(' AND ', $where) : ''));
		$pages = $this->db->pages;
		$color = array(0 => 'default', 1 => 'primary', 2 => 'info', 3 => 'success', 4 => 'danger', 5 => 'warning');
		foreach ($remote as $t) {
			$this->type[$t['id']] = $t;
		}
		if(!empty($datas)) {
			foreach($datas as $r) {
				$thumb = glob(SYS_THUMB_PATH.md5($r['aid']).'/*');
				$rs['aid'] = $r['aid'];
				if(!$r['remote']) {
					$rs['type'] = '<label><span class="label label-sm label-default">'.L('默认').'</span></label>';
				} elseif (!$this->type[$r['remote']]) {
					$rs['type'] = '<label><span class="label label-sm label-danger">'.L('已失效').'</span></label>';
				} else {
					$rs['type'] = '<label><span class="label label-sm label-'.$color[$this->type[$r['remote']]['type']].'">'.L($this->type[$r['remote']]['name']).'</span></label>';
				}
				$rs['module'] = $modules[$r['module']]['name'];
				if ($r['module']=='member' && $r['catid']==0) {
					$rs['catname'] = '头像';
					$rs['filepath'] = SYS_ATTACHMENT_SAVE_ID ? dr_get_file_url($r) : dr_file(SYS_AVATAR_URL.$r['filepath']);
				} else if ($r['module']=='cloud' && $r['catid']==0) {
					$rs['catname'] = '云服务';
					$rs['filepath'] = dr_get_file_url($r);
				} else {
					$rs['catname'] = dr_cat_value($r['catid'], 'catname');
					$rs['filepath'] = dr_get_file_url($r);
				}
				$rs['filename'] = dr_keyword_highlight(dr_is_empty($r['filename']) ? '未命名' : $r['filename'], $param['keyword']);
				$rs['fileext'] = $r['fileext'].'<img src="'.WEB_PATH.'api.php?op=icon&fileext='.$r['fileext'].'" width="20" />'.($thumb ? ' <i class="fa fa-photo" onclick="showthumb('.$r['aid'].', \''.$r['filename'].'\')"></i>':'').($r['status'] ? ' <i class="fa fa-link"></i>':'');
				$rs['related'] = $r['related'];
				$rs['status'] = $r['status'];
				$rs['filesize'] = format_file_size($r['filesize']);
				$rs['uploadtime'] = dr_date($r['uploadtime'],null,'red');
				$array[] = $rs;
			}
		}
		include $this->admin_tpl('attachment_list');
	}
	
	/**
	 * 附件改名
	 */
	public function public_name_edit() {
		$show_header = true;
		$aid = (int)$this->input->get('aid');
		if (!$aid) {
			dr_json(0, L('附件id不能为空'));
		}
		$data = $this->db->get_one(array('aid'=>$aid));
		if (!$data) {
			dr_json(0, L('附件'.$id.'不存在'));
		}
		if (IS_POST) {
			$name = $this->input->post('name');
			if (dr_is_empty($name)) {
				dr_json(0, L('附件名称不能为空'));
			}
			$this->db->update(array('filename' => $name),array('aid'=>$aid));
			pc_base::load_sys_class('upload')->clear_data($data);
			dr_json(1, L('操作成功'));
		}
		$filename = $data['filename'];
		include $this->admin_tpl('attachment_edit');exit;
	}
	
	// 重新上传附件
	public function public_file_edit() {

		$aid = $this->input->get('aid');
		if (!$aid) {
			dr_json(0, L('你还没有选择呢'));
		}

		$data = get_attachment($aid, true);
		if (!$data) {
			dr_json(0, L('附件信息不存在'));
		}

		include $this->admin_tpl('attachment_upload');exit;
	}
	
	// 重新上传附件
	public function public_upload_edit() {
		pc_base::load_sys_class('upload','',0);

		$aid = $this->input->get('aid');
		if (!$aid) {
			dr_json(0, L('你还没有选择呢'));
		}

		$data = get_attachment($aid, true);
		if (!$data) {
			dr_json(0, L('附件信息不存在'));
		}

		$upload = new upload($data['module'],$data['catid'],$data['siteid']);
		$upload->set_userid($data['userid']);
		$rt = $upload->upload_file([
			'save_name' => str_replace('.'.$data['fileext'], '', basename($data['filepath'])),
			'path' => dirname($data['filepath']),
			'form_name' => 'file_data',
			'file_exts' => [$data['fileext']],
			'file_size' => 1000 * 1024 * 1024,
			'attachment' => $upload->get_attach_info($data['remote']),
		]);
		if (!$rt['code']) {
			exit(dr_array2string($rt));
		}
		$this->db->update(array('filemd5'=>$rt['data']['md5'], 'filesize'=>$rt['data']['size'], 'attachinfo'=>dr_array2string($rt['data']['info'])), array('aid'=>$aid));

		dr_json(1, L('上传成功'), $rt['data']);
	}
	
	/**
	 * 目录浏览模式添加图片
	 */
	public function dir() {
		if(!$this->admin_username) return false;
		$rel = $this->_dir_rel_path($this->input->get('path'));
		$listing = $this->_dir_listing($rel);
		if ($listing === null) {
			$rel = '';
			$listing = $this->_dir_listing($rel);
		}
		$parent_rel = '';
		if ($listing['rel'] !== '') {
			$norm = str_replace('\\', '/', $listing['rel']);
			$d = dirname($norm);
			$parent_rel = ($d === '.' || $d === '') ? '' : str_replace('\\', '/', $d);
		}
		$breadcrumb = $this->_dir_breadcrumb($listing['rel']);
		include $this->admin_tpl('attachment_dir');
	}

	/**
	 * 列出目录内容
	 */
	protected function _dir_listing($rel) {

		list($ok, $abs, $r) = $this->_dir_safe($rel);
		if (!$ok || !is_dir($abs)) {
			return null;
		}

		$dirs = [];
		$files = [];
		if ($fp = @opendir($abs)) {
			while (($name = readdir($fp)) !== false) {
				if ($name === '.' || $name === '..' || $name === '.DS_Store') {
					continue;
				}
				if (strtolower(substr($name, -4)) === '.php') {
					continue;
				}
				$full = $abs.DIRECTORY_SEPARATOR.$name;
				$childRel = $r === '' ? $name : $r.'/'.$name;
				if (is_dir($full)) {
					$dirs[] = [
						'name' => $name,
						'path' => $childRel,
						'size' => $this->_dir_tree_size($full, realpath(rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, SYS_UPLOAD_PATH), DIRECTORY_SEPARATOR))),
						'mtime' => filemtime($full),
					];
				} elseif (is_file($full)) {
					$ext = strtolower(trim(strrchr($name, '.'), '.'));
					$files[] = [
						'name' => $name,
						'path' => $childRel,
						'size' => filesize($full),
						'mtime' => filemtime($full),
						'ext' => $ext,
						'url' => SYS_UPLOAD_URL.str_replace('\\', '/', $childRel),
						'is_image' => dr_is_image($ext),
					];
				}
			}
			closedir($fp);
		}

		usort($dirs, function ($a, $b) {
			return strcasecmp($a['name'], $b['name']);
		});
		usort($files, function ($a, $b) {
			return strcasecmp($a['name'], $b['name']);
		});

		return [
			'rel' => $r,
			'dirs' => $dirs,
			'files' => $files,
		];
	}

	/**
	 * 解析安全绝对路径
	 */
	protected function _dir_safe($rel) {

		$rel = $this->_dir_rel_path($rel);
		$root = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, SYS_UPLOAD_PATH), DIRECTORY_SEPARATOR);
		if (!is_dir($root)) {
			return [false, '', ''];
		}
		$rootReal = realpath($root);
		if ($rootReal === false) {
			return [false, '', ''];
		}
		if ($rel === '') {
			return [true, $rootReal.DIRECTORY_SEPARATOR, ''];
		}
		$sub = str_replace('/', DIRECTORY_SEPARATOR, $rel);
		$full = $rootReal.DIRECTORY_SEPARATOR.$sub;
		if (is_file($full) || is_dir($full)) {
			$rp = realpath($full);
			if ($rp !== false && $this->_dir_under_root($rootReal, $rp)) {
				return [true, $rp, $rel];
			}

			return [false, '', ''];
		}

		return [false, '', ''];
	}

	/**
	 * 是否位于上传根目录之下
	 */
	protected function _dir_under_root($rootReal, $candidate) {

		$root = strtolower(str_replace('\\', '/', rtrim($rootReal, '/\\')));
		$path = strtolower(str_replace('\\', '/', $candidate));

		return $path === $root || strpos($path, $root.'/') === 0;
	}

	/**
	 * 规范化相对路径（相对 SYS_UPLOAD_PATH）
	 * 按段解析，禁止路径跳出上传根（..），避免简单替换字符串被绕过
	 */
	protected function _dir_rel_path($raw) {

		$raw = (string) $raw;
		if ($raw === '' || strpos($raw, "\0") !== false) {
			return '';
		}
		$raw = str_replace('\\', '/', $raw);
		$raw = str_replace(["\r", "\n", '<', '>', '{', '}'], '', $raw);
		$raw = trim($raw, '/');
		if ($raw === '') {
			return '';
		}
		$parts = explode('/', $raw);
		$out = [];
		foreach ($parts as $p) {
			if ($p === '' || $p === '.') {
				continue;
			}
			if ($p === '..') {
				if (empty($out)) {
					return '';
				}
				array_pop($out);
				continue;
			}
			$p = trim($p);
			if ($p === '' || $p === '.' || $p === '..') {
				continue;
			}
			$out[] = $p;
		}

		return implode('/', $out);
	}

	/**
	 * 递归统计目录下所有文件字节（不跟随符号链接）
	 */
	protected function _dir_tree_size($absDir, $rootReal) {

		$total = 0;
		if (!is_dir($absDir)) {
			return 0;
		}
		$absDir = rtrim($absDir, DIRECTORY_SEPARATOR);
		if (is_link($absDir)) {
			return 0;
		}

		if ($fp = @opendir($absDir)) {
			while (($name = readdir($fp)) !== false) {
				if ($name === '.' || $name === '..') {
					continue;
				}
				$sub = $absDir.DIRECTORY_SEPARATOR.$name;
				if (is_link($sub)) {
					continue;
				}
				if (is_file($sub)) {
					$sz = @filesize($sub);
					if ($sz !== false) {
						$total += $sz;
					}
				} elseif (is_dir($sub)) {
					$rp = realpath($sub);
					if ($rp !== false && $this->_dir_under_root($rootReal, $rp)) {
						$total += $this->_dir_tree_size($rp, $rootReal);
					}
				}
			}
			closedir($fp);
		}

		return $total;
	}

	/**
	 * 面包屑
	 */
	protected function _dir_breadcrumb($rel) {

		$crumbs = [['name' => L('附件根目录'), 'path' => '']];
		if ($rel === '') {
			return $crumbs;
		}
		$parts = explode('/', $rel);
		$acc = '';
		foreach ($parts as $p) {
			if ($p === '') {
				continue;
			}
			$acc = $acc === '' ? $p : $acc.'/'.$p;
			$crumbs[] = ['name' => $p, 'path' => $acc];
		}

		return $crumbs;
	}
	
	/**
	 * 更新
	 */
	public function update() {
		if(IS_POST) {
			$this->db->update(array($this->input->post('field')=>$this->input->post('value')),array('aid'=>$this->input->post('aid')));
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}

	// 变更储存策略
	public function remote_edit() {

		if (IS_POST) {
			$post = $this->input->post('data');
			if ($post['o'] == $post['n']) {
				dr_json(0, L('储存策略不能相同'));
			}

			$this->db->update(array('remote'=>intval($post['n'])),array('remote'=>intval($post['o'])));

			dr_dir_delete(CACHE_PATH.'caches_attach/caches_data');
			dr_mkdirs(CACHE_PATH.'caches_attach/caches_data');
			
			dr_json(1, L('操作成功'));
		}

		$show_header = true;
		$this->remote_db = pc_base::load_model('attachment_remote_model');
		$remote = $this->remote_db->select();
		include $this->admin_tpl('attachment_remote_edit');
	}
	
	/**
	 * 删除附件
	 */
	public function delete() {
		if (!IS_POST) {
			dr_json(0, L('请求错误'));
		}
		if ($this->input->get('action')=='dir') {
			$paths = $this->input->post('paths');
			if (!$paths || !is_array($paths)) {
				dr_json(0, L('你还没有选择呢'));
			}
			foreach ($paths as $p) {
				$rel = $this->_dir_rel_path($p);
				if ($rel === '') {
					dr_json(0, L('禁止删除附件根目录'));
				}
				list($ok, $abs, $r) = $this->_dir_safe($rel);
				if (!$ok) {
					continue;
				}
				if (is_file($abs)) {
					if (!@unlink($abs)) {
						dr_json(0, L('文件删除失败'));
					}
				} elseif (is_dir($abs)) {
					$items = @scandir($abs);
					$left = $items ? array_diff($items, ['.', '..']) : [];
					if ($left) {
						dr_json(0, L('目录非空，无法删除'));
					}
					if (!@rmdir($abs)) {
						dr_json(0, L('目录删除失败'));
					}
				}
			}
		} else {
			$ids = $this->input->get_post_ids();
			if (!$ids) {
				dr_json(0, L('你还没有选择呢'));
			}
			$data = $this->db->select(array('aid'=>$ids));
			if (!$data) {
				dr_json(0, L('所选附件不存在'));
			}
			foreach($data as $t){
				$rt = pc_base::load_sys_class('upload')->_delete_file($t);
				if (!$rt['code']) {
					dr_json(0, $rt['msg']);
				}
			}
		}
		dr_json(1, L('delete').L('success'));
	}
	
	public function public_showthumbs() {
		$aid = intval($this->input->get('aid'));
		$info = $this->db->get_one(array('aid'=>$aid));
		if($info) {
			$infos = glob(SYS_THUMB_PATH.md5($aid).'/*');
			foreach ($infos as $n=>$thumb) {
				$thumbs[$n]['thumb_url'] = str_replace(SYS_THUMB_PATH, SYS_THUMB_URL, $thumb);
				$thumbinfo = explode('_', basename($thumb));
				$thumbinfo = explode('x', $thumbinfo[0]);
				$thumbs[$n]['thumb_filepath'] = $thumb;
				$thumbs[$n]['width'] = $thumbinfo[0];
				$thumbs[$n]['height'] = $thumbinfo[1];
			}
		}
		$show_header = true; 
		include $this->admin_tpl('attachment_thumb');
	}
	
	public function public_delthumbs() {
		$filepath = urldecode($this->input->get('filepath'));
		$ext = fileext($filepath);
		if(!dr_is_image($ext)) exit('0');
		$reslut = @unlink($filepath);
		if($reslut) exit('1');
		 exit('0');
	}
	
	// 批量变更储存策略
	public function public_type_edit() {

		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}

		$rid = intval($this->input->post('remote'));
		if ($rid < 0) {
			dr_json(0, L('你还没有选择储存策略'));
		}

		foreach($ids as $id){
			$this->db->update(array('remote'=>$rid),array('aid'=>$id));
		}

		dr_dir_delete(CACHE_PATH.'caches_attach/caches_data');
		dr_mkdirs(CACHE_PATH.'caches_attach/caches_data');

		dr_json(1, L('操作成功'));
	}
	
	// 图片编辑
	public function public_image_edit() {

		$aid = (int)$this->input->get('aid');
		if (!$aid) {
			dr_json(0, L('附件id不能为空'));
		}

		$info = $this->db->get_one(array('aid'=>$aid));
		if (!$info) {
			dr_json(0, L('附件'.$aid.'不存在'));
		}

		if (!dr_is_image($info['fileext'])) {
			dr_json(0, L('此文件不属于图片'));
		}

		$info['file'] = SYS_UPLOAD_PATH.$info['filepath'];
		$info['url'] = dr_get_file_url($info);

		// 修改图片的钩子
		pc_base::load_sys_class('hooks')::trigger('image_edit', $info);

		if (IS_POST) {

			// 文件真实地址
			if ($info['remote']) {
				$remote = get_cache('attachment', $info['remote']);
				if (!$remote) {
					// 远程地址无效
					dr_json(0, L('自定义附件（'.$info['remote'].'）的配置已经不存在'));
				} else {
					$info['file'] = $remote['value']['path'].$info['filepath'];
					if (!is_file($info['file'])) {
						dr_json(0, L('远程附件无法编辑'));
					}
				}
			}

			$post = $this->input->post('data');
			$image_lib = pc_base::load_sys_class('image');
			$brush = $image_lib->parse_brush_points($post);
            if (!$post['w'] && !$brush['points']) {
                dr_json(0, L('请先框选剪切区域或进行画笔涂抹'));
            }

			if ($post['w']) {
				$config = [];
				$config['source_image'] = $info['file'];
				$config['maintain_ratio'] = false;
				$config['width'] = $post['w'];
				$config['height'] = $post['h'];
				$config['x_axis'] = $post['x'];
				$config['y_axis'] = $post['y'];
				$image_lib->initialize($config);

				if (!$image_lib->crop()) {
					$err = $image_lib->display_errors();
					dr_json(0, $err ? $err : L('剪切失败'));
				}
				if ($brush['points']) {
					$brush['points'] = $image_lib->offset_brush_points(
						$brush['points'],
						(int)$post['x'],
						(int)$post['y'],
						(int)$post['w'],
						(int)$post['h']
					);
				}
			}

			if ($brush['points'] && !$image_lib->image_brush($info['file'], $brush)) {
				dr_json(0, L('画笔处理失败'));
			}

			pc_base::load_sys_class('upload')->clear_data($info);
			dr_json(1, L('操作成功'));
		}
		$show_header = true;
		include $this->admin_tpl('attachment_image');exit;
	}
}
?>