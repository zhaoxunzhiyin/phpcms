<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class attachment extends admin {
	private $db;
	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
		$this->input = pc_base::load_sys_class('input');
		$this->upload = pc_base::load_sys_class('upload');
		$this->admin_username = param::get_cookie('admin_username');
		$this->userid = $_SESSION['userid'] ? $_SESSION['userid'] : (param::get_cookie('_userid') ? param::get_cookie('_userid') : sys_auth($this->input->post('userid_h5'),'DECODE'));
		$this->siteid = $this->get_siteid();
		$this->db = pc_base::load_model('attachment_remote_model');
		$this->type = array(
			0 => array(
				'name' => '本地磁盘',
			),
		);
		$this->path = PC_PATH.'plugin/storage/';
		if (is_dir($this->path)) {
			$local = dr_dir_map($this->path, 1);
		} else {
			$local = array();
		}
		$this->load_file = array();
		foreach ($local as $dir) {
			if (is_file($this->path.$dir.'/app.php')) {
				$cfg = require $this->path.$dir.'/app.php';
				if ($cfg['id']) {
					$this->load_file[] = $this->path.$dir.'/config.html';
					$this->type[$cfg['id']] = $cfg;
				}
			}
		}
	}
	
	/**
	 * 附件设置
	 */
	public function init() {
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
		$remote = getcache('attachment', 'commons');
		include $this->admin_tpl('attachment_setting');
	}
	
	/**
	 * 保存配置信息
	 */
	public function save() {
		$post = $this->input->post('data');
		if ($post['sys_thumb_path'] || $post['sys_avatar_path']) {
			if ($post['sys_attachment_path'] && $post['sys_thumb_path'] == $post['sys_attachment_path']) {
				showmessage(L('附件上传目录不能与缩略图存储目录相同'));
			} elseif ($post['sys_attachment_path'] && $post['sys_avatar_path'] == $post['sys_attachment_path']) {
				showmessage(L('附件上传目录不能与头像存储目录相同'));
			} elseif ($post['sys_avatar_path'] && $post['sys_thumb_path'] == $post['sys_avatar_path']) {
				showmessage(L('头像存储目录不能与缩略图存储目录相同'));
			}
		}
		$this->set_config($post);	 //保存进config文件
		$this->setcache();
		showmessage(L('修改成功').$snda_error, HTTP_REFERER);
	}
	
	/**
	 * 设置config文件
	 * @param $config 配属信息
	 * @param $filename 要配置的文件名称
	 */
	public function set_config($config, $filename="system") {
		$configfile = CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.$filename.'.php';
		if(!is_writable($configfile)) showmessage('Please chmod '.$configfile.' to 0777 !');
		$pattern = $replacement = array();
		foreach($config as $k=>$v) {
			if(in_array($k,array('sys_attachment_save_id','sys_attachment_safe','sys_attachment_path','sys_attachment_save_type','sys_attachment_save_dir','sys_attachment_url','sys_avatar_path','sys_avatar_url','sys_thumb_path','sys_thumb_url','attachment_stat','attachment_file'))) {
				$v = trim($v);
				$configs[$k] = $v;
				$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
				$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";					
			}
		}
		$str = file_get_contents($configfile);
		$str = preg_replace($pattern, $replacement, $str);
		return pc_base::load_config('system','lock_ex') ? file_put_contents($configfile, $str, LOCK_EX) : file_put_contents($configfile, $str);		
	}
	
	/**
	 * 设置缓存
	 * Enter description here ...
	 */
	private function setcache() {
		$this->module_db = pc_base::load_model('module_model');
		$result = $this->module_db->get_one(array('module'=>'admin'));
		$setting = string2array($result['setting']);
		setcache('common', $setting, 'commons');
	}
	
	/**
	 * 存储策略
	 */
	public function remote() {
		$tablename = $this->db->db_tablepre.'attachment_remote';
		if (!$this->db->table_exists('attachment_remote')) {
			$this->db->query('CREATE TABLE `'.$tablename.'` (
			`id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
			`type` tinyint(2) NOT NULL COMMENT \'类型\',
			`name` varchar(50) NOT NULL COMMENT \'名称\',
			`url` varchar(255) NOT NULL COMMENT \'访问地址\',
			`value` text NOT NULL COMMENT \'参数值\',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci');
		}
		if (IS_POST) {
			$pagesize = $this->input->post('limit') ? $this->input->post('limit') : 10;
			$page = $this->input->post('page') ? $this->input->post('page') : '1';
			$datas = $this->db->listinfo('', 'id ASC', $page, $pagesize);
			$total = $this->db->count();
			$pages = $this->db->pages;
			if(!empty($datas)) {
				foreach($datas as $r) {
					$rs['id'] = $r['id'];
					$rs['type'] = $this->type[$r['type']]['name'];
					$rs['name'] = $r['name'];
					$array[] = $rs;
				}
			}
			exit(json_encode(array('code'=>0,'msg'=>L('to_success'),'count'=>$total,'data'=>$array,'rel'=>1)));
		}
		include $this->admin_tpl('attachment_remote');
	}
	
	/**
	 * 添加
	 */
	public function remote_add() {
		if (IS_POST) {
			$data = $this->input->post('data');
			$data['value'] = dr_array2string($data['value']);
			$this->db->insert($data);
			// 自动更新缓存
			$this->public_cache_remote();
			showmessage(L('operation_success'),'?m=attachment&c=attachment&a=remote&menuid=1598');
		}
		include $this->admin_tpl('remote_add');
	}
	
	/**
	 * 修改
	 */
	public function remote_edit() {
		if (IS_POST) {
			$id = intval($this->input->get('id'));
			$data = $this->input->post('data');
			$data['value'] = dr_array2string($data['value']);
			$this->db->update($data,array('id'=>$id));
			// 自动更新缓存
			$this->public_cache_remote();
			showmessage(L('operation_success'),'?m=attachment&c=attachment&a=remote&menuid=1598');
		}
		$data = $this->db->get_one(array('id'=>$this->input->get('id')));
		$data['value'] = dr_string2array($data['value']);
		$data['value'] = $data['value'][intval($data['type'])];
		if(!$data) showmessage(L('数据id不存在'));
		include $this->admin_tpl('remote_edit');
	}
	
	/**
	 * 更新
	 */
	public function update() {
		if($this->input->get('dosubmit')) {
			$this->db->update(array($this->input->post('field')=>$this->input->post('value')),array('id'=>$this->input->post('id')));
			// 自动更新缓存
			$this->public_cache_remote();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	
	/**
	 * 删除
	 */
	public function delete() {
		$id = $this->input->post('id');
		if($this->db->delete(array('id'=>$id))) {
			// 自动更新缓存
			$this->public_cache_remote();
			dr_json(1, L('operation_success'));
		} else {
			dr_json(0, L('operation_failure'));
		}
	}
	
	/**
	 * 批量删除
	 */
	public function public_delete_all() {
		$del_arr = array();
		$del_arr = $this->input->get_post_ids() ? $this->input->get_post_ids() : dr_json(0, L('illegal_parameters'));
		$attachment_index = pc_base::load_model('attachment_index_model');
		if(is_array($del_arr)){
			foreach($del_arr as $v){
				$id = intval($v);
				$this->db->delete(array('id'=>$id));
			}
			// 自动更新缓存
			$this->public_cache_remote();
			dr_json(1, L('delete').L('success'));
		}
	}

	/**
	 * 测试远程附件
	 */
	public function public_test_attach() {
		pc_base::load_sys_class('upload','',0);
		$upload = new upload('content',0,$siteid);
		$upload->set_userid($this->userid);

		$data = $this->input->post('data');
		$type = intval($data['type']);
		$value = $data['value'][$type];
		if (!$value) {
			dr_json(0, L('参数不存在'));
		} elseif ($type == 0) {
			if (substr($value['path'],-1, 1) != '/') {
				dr_json(0, L('存储路径目录一定要以“/”结尾'));
			} elseif ((strpos($value['path'], '/') === 0 || strpos($value['path'], ':') !== false)) {
				if (!is_dir($value['path'])) {
					dr_json(0, L('本地路径'.$value['path'].'不存在'));
				}
			} elseif (is_dir(SYS_UPLOAD_PATH.$value['path'])) {

			} else {
				dr_json(0, L('本地路径'.SYS_UPLOAD_PATH.$value['path'].'不存在'));
			}
		} 

		$rt = $upload->save_file(
			'content',
			'this is cms file-test',
			'test/test.txt',
			array(
				'id' => 0,
				'url' => $data['url'],
				'type' => $type,
				'value' => $value,
			)
		);

		if (!$rt['code']) {
			dr_json(0, $rt['msg']);
		} elseif (strpos(dr_catcher_data($rt['data']['url']), 'cms') !== false) {
			dr_json(1, L('测试成功'));
		}

		dr_json(0, L('无法访问到附件: '.$rt['data']['url']));
	}
	
	/**
	 * 测试附件目录是否可用
	 */
	public function public_test_attach_dir() {

		$v = $this->input->get('v');
		if (!$v) {
			dr_json(1, L('默认目录'));
		} elseif (strpos($v, ' ') === 0) {
			dr_json(0, L('不能用空格开头'));
		}
		$path = dr_get_dir_path($v);
		if (is_dir($path)) {
			dr_json(1, L('目录正常'));
		} else {
			dr_json(0, L('目录'.$path.'不存在'));
		}
	}
	
	/**
	 * 测试附件域名是否可用
	 */
	public function public_test_attach_domain() {

		$note = '';
		$data = $this->input->post('data');
		if (!$data['sys_attachment_path']) {
			$note = L('上传目录留空时，采用系统默认分配的目录');
			$data['sys_attachment_path'] = 'uploadfile';
		} elseif (!$data['sys_attachment_url']) {
			$note = L('URL地址留空时，采用系统默认分配的URL');
		}

		if ((strpos($data['sys_attachment_path'], '/') === 0 || strpos($data['sys_attachment_path'], ':') !== false)
			&& is_dir($data['sys_attachment_path'])) {
			// 相对于根目录
			if (!$data['sys_attachment_url']) {
				dr_json(0, '<font color="red">'.L('没有设置附件URL地址').'</font>');
			}
			// 附件上传目录
			$path = rtrim($data['sys_attachment_path'], DIRECTORY_SEPARATOR).'/';
			// 附件访问URL
			$url = trim($data['sys_attachment_url'], '/').'/';
			$note = L('已使用自定义上传目录和自定义访问地址');
		} else {
			// 在当前网站目录
			$path = CMS_PATH.trim($data['sys_attachment_path'], '/').'/';
			$url = APP_PATH.trim($data['sys_attachment_path'], '/').'/';
			!$note && $note = L('上传目录不是绝对的路径时采用，系统分配的URL地址');
		}

		dr_json(1, $note.'<br>'.L('附件上传目录：'.$path) .'<br>' . L('附件访问地址：'.$url));
	}
	
	/**
	 * 测试缩略图域名是否可用
	 */
	public function public_test_thumb_domain() {

		$note = '';
		$data = $this->input->post('data');
		if (!$data) {
			dr_json(0, L('参数错误'));
		} elseif (!$data['sys_thumb_path']) {
			$note = L('存储目录留空时，采用系统默认分配的目录');
			$data['sys_thumb_path'] = 'uploadfile/thumb/';
		} elseif (!$data['sys_thumb_url']) {
			$note = L('URL地址留空时，采用系统默认分配的URL');
		}

		if ((strpos($data['sys_thumb_path'], '/') === 0 || strpos($data['sys_thumb_path'], ':') !== false) && is_dir($data['sys_thumb_path'])) {
			// 相对于根目录
			$path = rtrim($data['sys_thumb_path'], DIRECTORY_SEPARATOR).'/';
			if (!$data['sys_thumb_url']) {
				dr_json(0, '<font color="red">'.L('没有设置访问URL地址').'</font>');
			}
			$url = trim($data['sys_thumb_url'], '/').'/';
			$note = L('已使用自定义存储目录和自定义访问地址');
		} else {
			// 在当前网站目录
			$path = SYS_UPLOAD_PATH.trim($data['sys_thumb_path'], '/').'/';
			$url = SYS_UPLOAD_URL.trim($data['sys_thumb_path'], '/').'/';
			!$note && $note = L('存储目录不是绝对的路径时采用，系统分配的URL地址');
		}

		dr_json(1, $note.'<br>'.L('存储目录：'.$path) .'<br>' . L('访问地址：'.$url));
	}
	
	/**
	 * 测试头像域名是否可用
	 */
	public function public_test_avatar_domain() {

		$note = '';
		$data = $this->input->post('data');
		if (!$data['sys_avatar_path']) {
			$note = L('存储目录留空时，采用系统默认分配的目录');
			$data['sys_avatar_path'] = 'avatar/';
		} elseif (!$data['sys_avatar_url']) {
			$note = L('URL地址留空时，采用系统默认分配的URL');
		}

		if ((strpos($data['sys_avatar_path'], '/') === 0 || strpos($data['sys_avatar_path'], ':') !== false) && is_dir($data['sys_avatar_path'])) {
			// 相对于根目录
			$path = rtrim($data['sys_avatar_path'], DIRECTORY_SEPARATOR).'/';
			if (!$data['sys_avatar_url']) {
				dr_json(0, '<font color="red">'.L('没有设置访问URL地址').'</font>');
			}
			$url = trim($data['sys_avatar_url'], '/').'/';
			$note = L('已使用自定义存储目录和自定义访问地址');
		} else {
			// 在当前网站目录
			$path = SYS_UPLOAD_PATH.trim($data['sys_avatar_path'], '/').'/';
			$url = SYS_UPLOAD_URL.trim($data['sys_avatar_path'], '/').'/';
			!$note && $note = L('存储目录不是绝对的路径时采用，系统分配的URL地址');
		}

		dr_json(1, $note.'<br>'.L('存储目录：'.$path) .'<br>' . L('访问地址：'.$url));
	}
	
	// 远程附件缓存
	public function public_cache_remote() {
		$data = $this->db->select();
		$cache = array();
		if ($data) {
			foreach ($data as $t) {
				$t['url'] = trim($t['url'], '/').'/';
				$t['value'] = dr_string2array($t['value']);
				$t['value'] = $t['value'][intval($t['type'])];
				$cache[$t['id']] = $t;
			}
		}
		setcache('attachment', $cache, 'commons');
	}
}
?>