<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class manage extends admin {
	private $input,$db,$remote_db,$upload,$admin_username,$siteid,$type;
	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('attachment_model');
		$this->remote_db = pc_base::load_model('attachment_remote_model');
		$this->upload = pc_base::load_sys_class('upload');
		$this->admin_username = param::get_cookie('admin_username');
		$this->siteid = $this->get_siteid();
	}	
	/**
	 * 附件列表
	 */
	public function init() {
		pc_base::load_sys_class('form');
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
		$dir = $this->input->get('dir') && trim($this->input->get('dir')) ? str_replace(array('..\\', '../', './', '.\\'), '', trim($this->input->get('dir'))) : '';
		$filepath = SYS_UPLOAD_PATH.$dir;
		$list = glob($filepath.'/'.'*');
		if(!empty($list)) rsort($list);
		$local = trim(str_replace(array(PC_PATH, CMS_PATH ,DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('','',DIRECTORY_SEPARATOR), $filepath), '/');
		include $this->admin_tpl('attachment_dir');
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
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}
		$data = $this->db->select(array('aid'=>$ids));
		if (!$data) {
			dr_json(0, L('所选附件不存在'));
		}
		foreach($data as $t){
			$rt = $this->upload->_delete_file($t);
			if (!$rt['code']) {
				return dr_json(0, $rt['msg']);
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
		if(!in_array(strtoupper($ext),array('JPG','GIF','BMP','PNG','JPEG','WEBP'))) exit('0');
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
			if (!$post['w']) {
				dr_json(0, L('图形宽度不规范'));
			}

			$config = [];
			$config['source_image'] = $info['file'];
			$config['maintain_ratio'] = false;
			$config['width'] = $post['w'];
			$config['height'] = $post['h'];
			$config['x_axis'] = $post['x'];
			$config['y_axis'] = $post['y'];
			$image_lib = pc_base::load_sys_class('image');
			$image_lib->initialize($config);

			if (!$image_lib->crop()) {
				$err = $image_lib->display_errors();
				dr_json(0, $err ? $err : L('剪切失败'));
			}

			dr_json(1, L('操作成功'));
		}
		$show_header = true;
		include $this->admin_tpl('attachment_image');exit;
	}
}
?>