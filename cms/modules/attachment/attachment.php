<?php 
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class attachment extends admin {
	private $input,$upload,$cache_api,$isadmin,$siteid,$type,$path,$load_file,$db;
	function __construct() {
		parent::__construct();
		pc_base::load_app_func('global');
		$this->input = pc_base::load_sys_class('input');
		$this->upload = pc_base::load_sys_class('upload');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		$this->isadmin = IS_ADMIN && param::get_session('roleid') ? 1 : 0;
        $this->userid = $this->isadmin ? (param::get_session('userid') ? param::get_session('userid') : param::get_cookie('userid')) : param::get_cookie('_userid');
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
		if(IS_AJAX_POST) {
			$post = $this->input->post('data');
			if ($post['sys_thumb_path'] || $post['sys_avatar_path']) {
				if ($post['sys_attachment_path'] && $post['sys_thumb_path'] == $post['sys_attachment_path']) {
					dr_json(0, L('附件上传目录不能与缩略图存储目录相同'));
				} elseif ($post['sys_attachment_path'] && $post['sys_avatar_path'] == $post['sys_attachment_path']) {
					dr_json(0, L('附件上传目录不能与头像存储目录相同'));
				} elseif ($post['sys_avatar_path'] && $post['sys_thumb_path'] == $post['sys_avatar_path']) {
					dr_json(0, L('头像存储目录不能与缩略图存储目录相同'));
				}
			}
			foreach (array('sys_attachment_path' => '附件上传', 'sys_avatar_path' => '头像上传', 'sys_thumb_path' => '缩略图上传') as $key => $name) {
				if (isset($post[$key]) && $post[$key] &&
					(strpos($post[$key], 'config') !== false || strpos($post[$key], CONFIGPATH) !== false)) {
					dr_json(0, L($name.'目录不能包含config目录'));
				}
			}
			$post['sys_attachment_save_id'] = intval($post['sys_attachment_save_id']);
			$post['sys_attachment_pagesize'] = intval($post['sys_attachment_pagesize']);
			if (!$post['sys_attachment_pagesize']) {
				dr_json(0, L('浏览附件分页').L('empty'), array('field' => 'sys_attachment_pagesize'));
			}
			$post['sys_attachment_save_type'] = intval($post['sys_attachment_save_type']);
			$post['sys_attachment_path'] = addslashes($post['sys_attachment_path']);
			$post['sys_avatar_path'] = addslashes($post['sys_avatar_path']);
			$post['sys_thumb_path'] = addslashes($post['sys_thumb_path']);
			$post['attachment_stat'] = (int)$post['attachment_stat'];
			$post['attachment_file'] = (int)$post['attachment_file'];
			$post['attachment_del'] = (int)$post['attachment_del'];
			$post['sys_attachment_cf'] = (int)$post['sys_attachment_cf'];
			$post['sys_attachment_safe'] = (int)$post['sys_attachment_safe'];
			$this->set_config($post);//保存进config文件
			$this->setcache();
			dr_json(1, L('修改成功'));
		}
		$show_header = true;
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
		$remote = get_cache('attachment');
		$page = (int)$this->input->get('page');
		include $this->admin_tpl('attachment_setting');
	}
	
	/**
	 * 设置config文件
	 * @param $config 配属信息
	 * @param $filename 要配置的文件名称
	 */
	public function set_config($config, $filename="system") {
		$configfile = CONFIGPATH.$filename.'.php';
		if(!is_writable($configfile)) dr_json(0, 'Please chmod '.$configfile.' to 0777 !');
		$pattern = $replacement = array();
		foreach($config as $k=>$v) {
			if(in_array($k,array('sys_attachment_save_id','sys_attachment_cf','sys_attachment_pagesize','sys_attachment_safe','sys_attachment_path','sys_attachment_save_type','sys_attachment_save_dir','sys_attachment_url','sys_avatar_path','sys_avatar_url','sys_thumb_path','sys_thumb_url','attachment_stat','attachment_file','attachment_del'))) {
				$v = trim($v);
				$configs[$k] = $v;
				$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
				$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";					
			}
		}
		$str = file_get_contents($configfile);
		$str = preg_replace($pattern, $replacement, $str);
		return file_put_contents($configfile, $str, LOCK_EX);		
	}
	
	/**
	 * 设置缓存
	 * Enter description here ...
	 */
	private function setcache() {
		$this->cache_api->cache('setting');
	}
	
	/**
	 * 存储策略
	 */
	public function remote() {
		$pagesize = $this->input->get('limit') ? $this->input->get('limit') : SYS_ADMIN_PAGESIZE;
		$order = $this->input->get('order') ? $this->input->get('order') : 'id ASC';
		$page = $this->input->get('page') ? $this->input->get('page') : '1';
		$datas = $this->db->listinfo('', $order, $page, $pagesize);
		$total = $this->db->count();
		$pages = $this->db->pages;
		$color = array(1 => 'primary', 2 => 'info', 3 => 'success', 4 => 'danger', 5 => 'warning');
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
			dr_admin_msg(1, L('operation_success'), array('url' => '?m=attachment&c=attachment&a=remote&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
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
			dr_admin_msg(1, L('operation_success'), array('url' => '?m=attachment&c=attachment&a=remote&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
		}
		$data = $this->db->get_one(array('id'=>$this->input->get('id')));
		$data['value'] = dr_string2array($data['value']);
		$data['value'] = $data['value'][intval($data['type'])];
		if(!$data) dr_admin_msg(0,L('数据id不存在'));
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
	 * 批量删除
	 */
	public function remote_delete() {
		$ids = $this->input->get_post_ids();
		if (!$ids) {
			dr_json(0, L('你还没有选择呢'));
		}
		$this->db->delete(array('id'=>$ids));
		$rt = $this->db->error();
		// 自动更新缓存
		$this->public_cache_remote();
		dr_json(1, L('delete').L('success'));
	}

	/**
	 * 测试远程附件
	 */
	public function public_test_attach() {
		pc_base::load_sys_class('upload','',0);
		$upload = new upload('content',0,$siteid);
		$upload->set_userid($this->userid);

		$data = $this->input->post('data');
		if (!$data) {
			dr_json(0, L('参数错误'));
		}
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
			dr_json(1, L('测试成功：'.$rt['data']['url']));
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
		} elseif (strpos($v, '..') !== false) {
			dr_json(0, L('不能出现..符号'));
		}
		$path = dr_get_dir_path($v);
		if (is_dir($path)) {
			dr_json(1, L('目录正常'));
		} else {
            if (strpos($path, CMS_PATH) !== false) {
                dr_json(0, L('目录'.$path.'不存在'));
            }
			dr_json(0, L('目录['.$path.']无法识别，请检查服务器的防跨站开关或者.user.ini权限文件'));
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
			$url = ($data['sys_attachment_url'] ? trim($data['sys_attachment_url'], '/').'/' : APP_PATH.trim($data['sys_attachment_path'], '/').'/');
			!$note && $note = $data['sys_attachment_url'] ? L('已使用自定义访问地址') : L('系统分配的URL地址');
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
			$data['sys_thumb_path'] = 'uploadfile/thumb';
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
			$path = CMS_PATH.trim($data['sys_thumb_path'], '/').'/';
			$url = ($data['sys_thumb_url'] ? trim($data['sys_thumb_url'], '/').'/' : APP_PATH.trim($data['sys_thumb_path'], '/').'/');
			!$note && $note = $data['sys_thumb_url'] ? L('已使用自定义访问地址') : L('系统分配的URL地址');
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
			$data['sys_avatar_path'] = 'uploadfile/avatar';
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
			$path = CMS_PATH.trim($data['sys_avatar_path'], '/').'/';
			$url = ($data['sys_avatar_url'] ? trim($data['sys_avatar_url'], '/').'/' : APP_PATH.trim($data['sys_avatar_path'], '/').'/');
			!$note && $note = $data['sys_avatar_url'] ? L('已使用自定义访问地址') : L('系统分配的URL地址');
		}

		dr_json(1, $note.'<br>'.L('存储目录：'.$path) .'<br>' . L('访问地址：'.$url));
	}
	
	// 远程附件缓存
	public function public_cache_remote() {
		$this->cache_api->cache('attachment_remote');
	}
}
?>