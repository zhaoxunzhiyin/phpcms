<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
class index extends admin {
	
	private $input;
	protected $safe = array('file_type' => 'php|js','code' => '','func' => 'com|system|exec|eval|escapeshell|cmd|passthru|base64_decode|gzuncompress','dir' => '', 'md5_file'=>'');
	private $option = array(
		'1' => '后台入口名称',
		'2' => '数据缓存cache目录',
		'3' => '核心程序cms目录',
		'4' => '前端模板template目录',
		'5' => '文件上传存储目录',
		'6' => '用户头像上传目录',
		'7' => 'web目录权限',
		'8' => '跨站提交验证',
		'9' => '检查外部访问',
		'10' => '入口文件权限',
	);
	
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
	}
	
	public function init() {
		$list = glob(CMS_PATH.'*');
		if (file_exists(CACHE_PATH.'caches_scan'.DIRECTORY_SEPARATOR.'caches_data')) {
			$md5_file_list = glob(CACHE_PATH.'caches_scan'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR.'md5_*.cache');
			if(is_array($md5_file_list)) {
				foreach ($md5_file_list as $k=>$v) {
					$md5_file_list[$v] = basename($v);
					unset($md5_file_list[$k]);
				}
			}
		}
		$scan = getcache('scan_config', 'scan');
		if (is_array($scan)) {
			$scan = array_merge($this->safe, $scan);
		} else {
			$scan = $this->safe;
		}
		$scan['dir'] = string2array($scan['dir']);
		pc_base::load_sys_class('form', '', 0);
		include $this->admin_tpl('scan_index');
	}
	
	//进行配置文件更新
	public function public_update_config() {
		$info = $this->input->post('info') ? $this->input->post('info') : dr_admin_msg(0,L('illegal_action'));
		$dir = $this->input->post('dir') ? $this->input->post('dir') : dr_admin_msg(0,L('please_select_the_content'));
		$info['dir'] = array2string($dir);
		setcache('scan_config', $info, 'scan');
		dr_admin_msg(1,L('configuration_file_save_to_the'), array('url'=>'?m=scan&c=index&a=public_file_count&menuid='.$this->input->post('menuid').'&pc_hash='.dr_get_csrf_token()));
	}
	
	//对要进行扫描的文件进行统计
	public function public_file_count() {
		$scan = getcache('scan_config', 'scan');
		pc_base::load_app_func('global');
		set_time_limit(120);
		$scan['dir'] = string2array($scan['dir']);
		$scan['file_type'] = explode('|', $scan['file_type']);
		$list = array();
		foreach ($scan['dir'] as $v) {
			if (is_dir($v)) {
				foreach ($scan['file_type'] as $k ) {
					$list = array_merge($list, scan_file_lists($v.DIRECTORY_SEPARATOR, 1, $k, 0, 1, 1));
				}
			} else {
				$list = array_merge($list, array(str_replace(CMS_PATH, '', $v)=>md5_file($v)));
			}
		}
		setcache('scan_list', $list, 'scan');
		dr_admin_msg(1,L('documents_to_file_the_statistics'), '?m=scan&c=index&a=public_file_filter');
	}
	
	//对文件进行筛选
	public function public_file_filter() {
		$scan_list = getcache('scan_list', 'scan');
		$scan = getcache('scan_config', 'scan');
		if ($scan['md5_file'] && file_exists($scan['md5_file'])) {
			$old_md5 = include $scan['md5_file'];
			foreach ($scan_list as $k=>$v) {
				if ($v == $old_md5[$k]) {
					unset($scan_list[$k]);
				}
			}
		}
		setcache('scan_list', $scan_list, 'scan');
		dr_admin_msg(1,L('file_through_a_feature_the_function_is'), '?m=scan&c=index&a=public_file_func');
	}
	
	//进行特征函数过滤
	public function public_file_func() {
		@set_time_limit(600);
		$file_list = getcache('scan_list', 'scan');
		$scan = getcache('scan_config', 'scan');
		if (isset($scan['func']) && !empty($scan['func'])) {
			foreach ($file_list as $key=>$val) {
				$html = file_get_contents(CMS_PATH.$key);
				if(stristr($key,'.php.') != false || preg_match_all('/[^a-z]?('.$scan['func'].')\s*\(/i', $html, $state, PREG_SET_ORDER)) {
					$badfiles[$key]['func'] = $state;
				}
			}
		}
		if(!isset($badfiles)) $badfiles = array();
		setcache('scan_bad_file', $badfiles, 'scan');
		dr_admin_msg(1,L('feature_function_complete_a_code_used_by_filtration'), '?m=scan&c=index&a=public_file_code');
	}
	
	//进行特征代码过滤
	public function public_file_code() {
		@set_time_limit(600);
		$file_list = getcache('scan_list', 'scan');
		$scan = getcache('scan_config', 'scan');
		$badfiles = getcache('scan_bad_file', 'scan');
		if (isset($scan['code']) && !empty($scan['code'])) {
			foreach ($file_list as $key=>$val) {
				$html = file_get_contents(CMS_PATH.$key);
				if(stristr($key, '.php.') != false || preg_match_all('/[^a-z]?('.$scan['code'].')/i', $html, $state, PREG_SET_ORDER)) {
					$badfiles[$key]['code'] = $state;
				}
				if(strtolower(substr($key, -4)) == '.php' && function_exists('zend_loader_file_encoded') && zend_loader_file_encoded(CMS_PATH.$key)) {
					$badfiles[$key]['zend'] = 'zend encoded';
				}
			}
		}
		setcache('scan_bad_file', $badfiles, 'scan');
		dr_admin_msg(1,L('scan_completed'), '?m=scan&c=index&a=scan_report&menuid='.$this->input->get('menuid'));
	}
	
	public function scan_report() {
		$badfiles = getcache('scan_bad_file', 'scan');
		if (empty($badfiles)) {
			dr_admin_msg(0,L('scan_to_find_a_result_please_to_scan'), '?m=scan&c=index&a=init');
		}
		include $this->admin_tpl('scan_report');
	}
	
	public function view() {
		$url = $this->input->get('url') && trim($this->input->get('url')) ? urldecode(trim($this->input->get('url'))) : dr_admin_msg(0,L('illegal_action'), HTTP_REFERER);
		$url = str_replace("..","",$url);
		
		if (!file_exists(CMS_PATH.$url)) {
			dr_admin_msg(0,L('file_not_exists'));
		}
		$html = file_get_contents(CMS_PATH.$url);
		//判断文件名，如果是database.php 对里面的关键字符进行替换
		$basename = basename($url);
		if($basename == "database.php" || $basename == "system.php"){
			//$html = str_replace();
			dr_admin_msg(0,L('重要文件，不允许在线查看！'));
		}
		$file_list = getcache('scan_bad_file', 'scan');
		if (isset($file_list[$url]['func']) && is_array($file_list[$url]['func']) && !empty($file_list[$url]['func'])) foreach ($file_list[$url]['func'] as $key=>$val)
		{
			$func[$key] = strtolower($val[1]);
		}
		if (isset($file_list[$url]['code']) && is_array($file_list[$url]['code']) && !empty($file_list[$url]['code'])) foreach ($file_list[$url]['code'] as $key=>$val)
		{
			$code[$key] = strtolower($val[1]);
		}
		if (isset($func)) $func = array_unique($func);
		if (isset($code)) $code = array_unique($code);
		$show_header = true;
 		include $this->admin_tpl('public_view');
	}
	
	public function safe_index() {
		$path = CACHE_PATH.'caches_scan/safe/';
		$is_ok = $count = 0;
		foreach ($this->option as $key => $v) {
			if (is_file($path.$key.'.txt')) {
				$count++;
			}
		}
		if ($count == count($this->option)) {
			$is_ok = 1;
		}
		$list = $this->option;
		include $this->admin_tpl('safe_index');
	}

	public function public_do_index() {

		$id = trim($this->input->get('id'));

		switch ($id) {

			case '1':

				if (SELF == 'admin.php') {
					$this->_is_ok($id, 0);
					dr_json(0, '修改根目录admin.php的文件名，可以有效的防止被猜疑破解');
				}
				$this->_is_ok($id);
				break;


			case '2':
				if (strpos(CACHE_PATH, CMS_PATH) !== false) {
					$this->_is_ok($id, 0);
					dr_json(0, '将/cache/目录转移到Web目录之外的目录，可以防止缓存数据不被Web读取');
				}
				$this->_is_ok($id);
				break;


			case '3':
				if (strpos(PC_PATH, CMS_PATH) !== false) {
					$this->_is_ok($id, 0);
					dr_json(0, '将/cms/目录转移到Web目录之外的目录，可以防止核心程序不被Web读取');
				}
				$this->_is_ok($id);
				break;


			case '4':
				if (strpos(TPLPATH, CMS_PATH) !== false) {
					$this->_is_ok($id, 0);
					dr_json(0, '将/template/目录转移到Web目录之外的目录，可以防止模板文件不被下载');
				}
				$this->_is_ok($id);
				break;


			case '5':
				if (strpos(SYS_UPLOAD_PATH, CMS_PATH) !== false) {
					$this->_is_ok($id, 0);
					dr_json(0, '将/uploadfile/目录转移到Web目录之外的目录，可以防止非法上传恶意文件');
				}

				if (!file_put_contents(SYS_UPLOAD_PATH.'test.php', '<?php echo "php8";')) {
					$this->_is_ok($id, 0);
					dr_json(0,'目录['.SYS_UPLOAD_PATH.']无法写入文件');
				}

				$url = SYS_UPLOAD_URL.'test.php';
				if (!function_exists('stream_context_create')) {
					$this->_is_ok($id, 0);
					dr_json(0, '函数没有被启用：stream_context_create');
				}
				$context = stream_context_create(array(
					'http' => array(
						'timeout' => 5 //超时时间，单位为秒
					)
				));
				$code = file_get_contents($url, 0, $context);
				if ($code == '<?php echo "php8";') {
					$this->_is_ok($id);
					dr_json(1, '安全');
				} elseif ($code == 'php8') {
					$this->_is_ok($id, 0);
					dr_json(0, '必须将附件域名使用纯静态网站');
				} else {
					$this->_is_ok($id);
					dr_json(1, '域名绑定异常，无法访问：'.$url.'，可以尝试手动访问此地址，<br>如果提示<？php echo "php8";就表示成功', 0);
				}

				break;


			case '6':
				if (strpos(SYS_AVATAR_PATH, CMS_PATH) !== false) {
					$this->_is_ok($id, 0);
					dr_json(0, '将头像目录转移到Web目录之外的目录，可以防止非法上传恶意文件');
				}

				if (!file_put_contents(SYS_AVATAR_PATH.'test.php', '<?php echo "php8";')) {
					$this->_is_ok($id, 0);
					dr_json(0,'目录['.SYS_AVATAR_PATH.']无法写入文件');
				}

				$url = SYS_AVATAR_URL.'test.php';
				if (!function_exists('stream_context_create')) {
					$this->_is_ok($id, 0);
					dr_json(0, '函数没有被启用：stream_context_create');
				}
				$context = stream_context_create(array(
					'http' => array(
						'timeout' => 5 //超时时间，单位为秒
					)
				));
				$code = file_get_contents($url, 0, $context);
				if ($code == '<?php echo "php8";') {
					$this->_is_ok($id);
					dr_json(1, '安全');
				} elseif ($code == 'php8') {
					$this->_is_ok($id, 0);
					dr_json(0, '必须将头像域名使用纯静态网站');
				} else {
					$this->_is_ok($id);
					dr_json(1, '域名绑定异常，无法访问：'.$url.'，可以尝试手动访问此地址，<br>如果提示<？php echo "php8";就表示成功', 0);
				}

				break;


			case '7':

				if (IS_EDIT_TPL) {
					$this->_is_ok($id, 0);
					dr_json(0, '系统开启了在线编辑模板权限，建议关闭此权限');
				}
				$this->_is_ok($id);
				if (file_put_contents(CMS_PATH.'test_cms.php', '<?php echo "test_cms";')) {
					unlink(CMS_PATH.'test_cms.php');
					dr_json(1 ,'WEB根目录['.CMS_PATH.']：除了静态生成目录之外，建议赋予只读权限，Linux555权限');
				}
				break;


			case '8':

				if (defined('SYS_CSRF') && !SYS_CSRF) {
					$this->_is_ok($id, 0);
					dr_json(0, '系统没有开启跨站验证，建议开启');
				}
				$this->_is_ok($id);
				break;


			case '9':

				if (defined('NeedCheckComeUrl') && !NeedCheckComeUrl) {
					$this->_is_ok($id, 0);
					dr_json(0, '系统没有开启检查外部访问，建议开启');
				}
				$this->_is_ok($id);
				break;


			case '10':

				$v = substr(sprintf('%o', fileperms(CMS_PATH.'index.php')), -3);
				if (!in_array($v, ['444', '555', '400'])) {
					$this->_is_ok($id, 0);
					dr_json(0, '入口文件['.CMS_PATH.'index.php]需要设置为只读权限，Linux555权限');
				}
				$this->_is_ok($id);
				break;

		}

		dr_json(1, '合格');
	}

	private function _is_ok($key, $is_save = 1) {
		$path = CACHE_PATH.'caches_scan/safe/';
		dr_mkdirs($path);
		if (!$is_save) {
			unlink($path.$key.'.txt');
		} else {
			file_put_contents($path.$key.'.txt', 'ok');
		}
	}

	public function public_book_index() {
		$id = intval($this->input->get('id'));
		include $this->admin_tpl('book_'.$id);exit;
	}
	
	public function md5_creat() {
		set_time_limit(120);
		pc_base::load_app_func('global');
		$list = scan_file_lists(CMS_PATH, 1, 'php', 0, 1);
		setcache('md5_'.date('Y-m-d'), $list, 'scan');
		dr_admin_msg(1,L('viewreporttrue'),'?m=scan&c=index&a=init');
	}
}