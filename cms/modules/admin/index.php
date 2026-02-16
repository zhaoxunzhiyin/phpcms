<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
	private $input,$cache,$db,$menu_db,$panel_db,$role,$role_db,$times_db,$admin_url,$service_url;
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		pc_base::load_sys_class('form');
		$this->db = pc_base::load_model('admin_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->panel_db = pc_base::load_model('admin_panel_model');
		$this->role = $this->get_role_all();
	}
	
	public function init() {
		$setting = getcache('common','commons');
		$admin_login_aes = (int)$setting['admin_login_aes'];
		$siteid = param::get_cookie('siteid');
		$userid = param::get_session('userid');
		$admin_username = param::get_cookie('admin_username');
		$site = pc_base::load_app_class('sites');
		$sitelist = $site->get_list(param::get_session('roleid'));
		$currentsite = $this->get_siteinfo($siteid);
		!$currentsite && $currentsite = array();
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		$background = array('"'.IMG_PATH.'admin_img/bg-screen1.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen2.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen3.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen4.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen5.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen7.jpg"',
			'"'.IMG_PATH.'admin_img/bg-screen7.jpg"');
		shuffle($background);
		include $this->admin_tpl('index');
	}
	
	public function login() {
		$is_sms = $this->input->get('is_sms');
		$is_sms = module_exists('sms') && isset($is_sms) && $is_sms;
		$setting = getcache('common','commons');
		$sysadmincode = (int)$setting['sysadmincode'];
		$maxloginfailedtimes = (int)$setting['maxloginfailedtimes'];
		$sysadminlogintimes = isset($setting['sysadminlogintimes']) ? (int)$setting['sysadminlogintimes'] : 10;
		$admin_sms_login = module_exists('sms') && isset($setting['admin_sms_login']) ? (int)$setting['admin_sms_login'] : '';
		$admin_sms_check = module_exists('sms') && isset($setting['admin_sms_check']) ? (int)$setting['admin_sms_check'] : '';
		$admin_login_aes = (int)$setting['admin_login_aes'];
		if(IS_AJAX_POST) {
			$data = $this->input->post('data');
			$is_aes = $this->input->post('is_aes');
			// 回调钩子
			pc_base::load_sys_class('hooks')::trigger('admin_login_before', $data);
			if (isset($data['is_check']) && $data['is_check']) {
				// 二次短信验证
				$data['phone'] = dr_authcode($data['phone'], 'DECODE');
				if (!$data['phone']) {
					dr_json(0, L('手机号码解析失败'));
				}
				if (!$sysadmincode && !check_captcha('code')) {
					dr_json(0, L('验证码不正确'));
				}
				$is_sms = 1;
			}
			if ($is_sms) {
				// 验证码登录
				if (!$data['phone']) {
					dr_json(0, L('手机号码未填写'));
				} elseif (!$data['sms']) {
					dr_json(0, L('短信验证码未填写'));
				}
				// 验证操作间隔
				$name = 'admin-login-phone-'.$data['phone'];
				$code = $this->cache->check_auth_data($name,
					defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 60);
				if (!$code) {
					dr_json(0, L('短信验证码还没有发送'));
				} elseif ($code != $data['sms']) {
					dr_json(0, L('短信验证码不正确'));
				}
				//查询帐号
				$r = $this->db->get_one(array('phone'=>$data['phone']));
				if(!$r){
					dr_json(0, L('手机['.$data['phone'].']不存在'));
				} else {
					//如果账号被锁定
					if($r['islock']) {
						dr_json(0, L('管理员已经被锁定'));
					}
					$data['username'] = $r['username'];
				}
			} else {
				if (!$sysadmincode && !check_captcha('code')) {
					dr_json(0, L('code_error'));
				}
				!$data['username'] && dr_json(0, L('nameerror'));
				if (is_badword($data['username'])) {
					dr_json(0, L('username_illegal'));
				}
				//密码错误剩余重试次数
				$this->times_db = pc_base::load_model('times_model');
				$rtime = $this->times_db->get_one(array('username'=>$data['username'],'isadmin'=>1));
				
				if ($rtime) {
					if ($maxloginfailedtimes) {
						if ($sysadminlogintimes && (int)$rtime['logintime'] && SYS_TIME - (int)$rtime['logintime'] > ($sysadminlogintimes * 60)) {
							// 超过时间了
							$this->times_db->delete(array('username'=>$data['username'],'isadmin'=>1));
						}
					}
					
					if ($maxloginfailedtimes) {
						if((int)$rtime['times'] && (int)$rtime['times'] >= $maxloginfailedtimes) {
							dr_json(0, L('失败次数已达到'.$rtime['times'].'次，已被禁止登录，请'.$sysadminlogintimes.'分钟后登录'));
						}
					}
				}
				if ($admin_login_aes && !isset($is_aes)) {
					dr_json(0, L('当前登录模板不支持AES加密传输'));
				}
				//查询帐号
				$r = $this->db->get_one(array('username'=>$data['username']));
				if(!$r) dr_json(0, L('user_not_exist'));
				//如果账号被锁定
				if($r['islock']) {
					dr_json(0, L('管理员已经被锁定'));
				}
				if ($admin_login_aes) {
					if (!function_exists('openssl_decrypt')) {
						log_message('error', '由于服务器环境没有启用openssl_decrypt，因此后台登录密码加密验证不被启用');
						dr_json(0, L('服务器环境不支持加密传输'));
					} else {
						$old = trim($data['password']);
						$password = openssl_decrypt(
							$old,
							'AES-128-CBC',
							substr(md5(SYS_KEY), 0, 16), 0,
							substr(md5(SYS_KEY), 10, 16)
						);
						if (!$password) {
							dr_json(0, IS_DEV ? L('密码['.$old.']解析失败').openssl_error_string() : L('密码解析失败'));
						}
					}
					$password = md5(md5($password).$r['encrypt']);
				} else {
					$password = md5(trim($data['password']).$r['encrypt']);
				}
				
				if($r['password'] != $password) {
					$ip = ip();
					if ($maxloginfailedtimes) {
						if($rtime && $rtime['times'] < $maxloginfailedtimes) {
							$times = $maxloginfailedtimes-intval($rtime['times']);
							$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$data['username']));
						} else {
							$this->times_db->delete(array('username'=>$data['username'],'isadmin'=>1));
							$this->times_db->insert(array('username'=>$data['username'],'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
							$times = $maxloginfailedtimes;
						}
						dr_json(0, str_replace('{times}',$times,L('password_error')));
					} else {
						dr_json(0, L('密码错误'));
					}
				}
				$this->times_db->delete(array('username'=>$data['username'],'isadmin'=>1));
				if ($admin_sms_check && $r['phone']) {
					dr_json(9, L('向'.substr($r['phone'], 0, 3).'****'.substr($r['phone'],-4).'的手机发送验证码：'), dr_authcode($r['phone'], 'ENCODE'));
				}
			}
			
			$this->db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$login_attr = md5(SYS_KEY.$r['password'].(isset($r['login_attr']) ? $r['login_attr'] : ''));
			param::set_session('userid', $r['userid']);
			param::set_session('login_attr', $login_attr);
			param::set_session('roleid', $r['roleid']);
			param::set_session('lock_screen', 0);
			dr_get_csrf_token();
			$site = pc_base::load_app_class('sites');
			$sitelist = $site->get_list(param::get_session('roleid'));
			$default_siteid = self::return_siteid();
			$member_setting = getcache('member_setting', 'member');
			$cookie_time = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
			if(!$r['lang']) $r['lang'] = 'zh-cn';
			param::set_cookie('admin_username',$data['username'],$cookie_time);
			param::set_cookie('siteid', $default_siteid,$cookie_time);
			param::set_cookie('userid', $r['userid'],$cookie_time);
			param::set_cookie('login_attr',$login_attr,$cookie_time);
			param::set_cookie('admin_email', $r['email'],$cookie_time);
			param::set_cookie('sys_lang', $r['lang'],$cookie_time);
			// 登录后的钩子
			pc_base::load_sys_class('hooks')::trigger('admin_login_after', $r);
			dr_json(1, L('login_success'), array('url' => '?m=admin&c=index&pc_hash='.dr_get_csrf_token()));
		} else {
			$background = array('"'.IMG_PATH.'admin_img/bg-screen1.jpg"',
				'"'.IMG_PATH.'admin_img/bg-screen2.jpg"',
				'"'.IMG_PATH.'admin_img/bg-screen3.jpg"',
				'"'.IMG_PATH.'admin_img/bg-screen4.jpg"');
			shuffle($background);
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('login');
		}
	}

	/**
	 * 发送验证码
	 */
	public function sms() {

		$setting = getcache('common','commons');
		$sysadmincode = (int)$setting['sysadmincode'];
		if (IS_POST) {
			$data = $this->input->post('data');
			if (!$sysadmincode && !check_captcha('code')) {
				dr_json(0, L('code_error'));
			} elseif (!$data['phone']) {
				dr_json(0, L('手机号码未填写'));
			}
			$phone = dr_authcode($data['phone'], 'DECODE');
			if ($phone) {
				$data['phone'] = $phone;
			}

			// 验证操作间隔
			$name = 'admin-login-phone-'.$data['phone'];
			if ($this->cache->check_auth_data($name, defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 60)) {
				dr_json(0, L('已经发送稍后再试'));
			}

			$randcode = get_rand_value();
			$rt = pc_base::load_app_class('smsapi', 'sms')->send_sms($data['phone'], $randcode, 1);
			if (!$rt['code']) {
				dr_json(0, IS_DEV ? $rt['msg'] : L('发送失败'));
			}

			$this->cache->set_auth_data($name, $randcode);

			dr_json(1, L('验证码发送成功'));
		} else {
			dr_json(0, L('请求方式错误'));
		}
	}
	
	// 子站客户端自动登录
	public function fclient() {

		if (!is_file(CMS_PATH.'api/fclient/sync.php')) {
			dr_admin_msg(0,L('fclient_not_exist'));
		}

		$sync = require CMS_PATH.'api/fclient/sync.php';
		if (!$this->input->get('id') || !$this->input->get('sync')) {
			dr_admin_msg(0,L('fclient_not_sn'));
		} elseif ($this->input->get('id') != md5($sync['id'])) {
			dr_admin_msg(0,L('fclient_not_id'));
		} elseif ($this->input->get('sync') != $sync['sn']) {
			dr_admin_msg(0,L('fclient_sn_exist'));
		}

		$this->role_db = pc_base::load_model('admin_role_model');
		$role = $this->role_db->get_one(array('disabled'=>0),'roleid','roleid asc');
		$member = $this->db->get_one(array('roleid'=>'%'.$role['roleid'].'%', 'islock'=>0),'*','roleid asc');
		if (!$member) {
			dr_admin_msg(0,L('fclient_user_not_role'));
		}
		// 回调钩子
		pc_base::load_sys_class('hooks')::trigger('admin_login_before', $member);

		$login_attr = md5(SYS_KEY.$member['password'].(isset($member['login_attr']) ? $member['login_attr'] : ''));
		param::set_session('userid', $member['userid']);
		param::set_session('login_attr', $login_attr);
		param::set_session('roleid', $member['roleid']);
		param::set_session('lock_screen', 0);
		dr_get_csrf_token();
		$default_siteid = self::return_siteid();
		$member_setting = getcache('member_setting', 'member');
		$cookie_time = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
		if(!$member['lang']) $member['lang'] = 'zh-cn';
		param::set_cookie('admin_username',$member['username'],$cookie_time);
		param::set_cookie('siteid', $default_siteid,$cookie_time);
		param::set_cookie('userid', $member['userid'],$cookie_time);
		param::set_cookie('login_attr',$login_attr,$cookie_time);
		param::set_cookie('admin_email', $member['email'],$cookie_time);
		param::set_cookie('sys_lang', $member['lang'],$cookie_time);
		// 登录后的钩子
		pc_base::load_sys_class('hooks')::trigger('admin_login_after', $member);
		dr_admin_msg(1,L('fclient_sn_succ'),'?m=admin&c=index');
	}
	
	public function public_logout() {
		$config = getcache('common','commons');
		if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
			$this->cache->del_auth_data('admin_option_'.param::get_session('userid'), 1);
		}
		param::del_session('userid');
		param::del_session('login_attr');
		param::del_session('roleid');
		param::del_session('lock_screen');
		param::del_session(COOKIE_PRE.ip().'pc_hash');
		param::set_cookie('admin_username','');
		param::set_cookie('siteid','');
		param::set_cookie('userid',0);
		param::set_cookie('login_attr', '');
		param::set_cookie('admin_email', '');
		param::set_cookie('sys_lang', '');

		dr_admin_msg(1,L('logout_success'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
	}
	
	public function public_menu() {
		$menu_data = $this->menu_db->get_one(array('name' => 'check', 'm' => 'admin', 'c' => 'check', 'a' => 'init'));
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		!$currentsite && $currentsite = array();
		//$logoInfo['href'] = $currentsite['domain'];
		$array = admin::admin_menu(0);
		$app = pc_base::load_config('version');
		if ($app['update'] || !is_file(CONFIGPATH.'version.php')) {
			$menu_home = '?m=admin&c=check&a=init&menuid='.$menu_data['id'].'&pc_hash='.dr_get_csrf_token();
		} else {
			$menu_home = '?m=admin&c=index&a=public_main';
		}
		$i = 0;
		$valuedata = '';
		$menu = '{"homeInfo": {"title": "首页","href": "'.$menu_home.'"},"logoInfo": {"title": "后台管理系统","image": "'.IMG_PATH.'admin_img/logo.png","icon": "fa fa-home","href": "'.ROOT_URL.'","target": "_blank"},"menuInfo": [';
		foreach($array as $_value) {
			if ($_value['data']) {
				if (strstr($value['data'], '&') && substr($value['data'], 0, 1)=='&') {
					$valuedata = $value['data'];
				} else {
					$valuedata = '&'.$value['data'];
				}
			}
			$menu .= ($i==0 ? '' : ',').'{"id": "'.$_value['id'].'","title": "'.L($_value['name']).'","icon": "'.$_value['icon'].'","href": "?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].$valuedata.'&menuid='.$_value['id'].'&pc_hash='.dr_get_csrf_token().'","target": "_self"'.admin::child_menu($_value['id'], 1).'}';
			$i++;
		}
		$menu .= ']}';
		if (IS_AJAX) {
			exit($menu);
		}
	}
	
	//初始化菜单
	private function menu_init($parentid = 0, $menuid = 0) {
		$parentid = intval($parentid);
		$where = array('parentid'=>$parentid);
		$result = $this->menu_db->table('menu')->select($where,'*',1000,'listorder ASC,id ASC');
		$j = 0;
		$pid = 0;
		foreach($result as $v) {
			$info['name'] = $v['name'];
			$info['parentid'] = $menuid;
			$info['m'] = $v['m'];
			$info['c'] = $v['c'];
			$info['a'] = $v['a'];
			$info['data'] = $v['data'];
			$info['icon'] = $v['icon'];
			$info['listorder'] = $j;
			$info['display'] = $v['display'];
			$pid = $this->menu_db->table('menu2')->insert($info, true);
			$this->menu_init($v['id'],$pid);
			$j++;
		}
	}
	
	//当前位置
	public function public_current_pos() {
		echo admin::current_pos($this->input->get('menuid'));
		exit;
	}
	
	/**
	 * 设置站点ID COOKIE
	 */
	public function public_set_siteid() {
		$siteid = $this->input->get('siteid') && intval($this->input->get('siteid')) ? intval($this->input->get('siteid')) : exit('0'); 
		param::set_cookie('siteid', $siteid);
		exit('1');
	}
	
	public function public_ajax_add_panel() {
		$menuid = $this->input->post('menuid') ? $this->input->post('menuid') : dr_json(0, L('menuid不存在'));
		$menuarr = $this->menu_db->get_one(array('id'=>$menuid));
		if (!$menuarr) {
			dr_json(0, L('菜单不存在'));
		}
		if ($menuarr['data']) {
			$menudata = $menuarr['data'];
		}
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].$menudata;
		$data = array('menuid'=>$menuid, 'userid'=>param::get_session('userid'), 'name'=>$menuarr['name'], 'icon'=>$menuarr['icon'], 'url'=>$url, 'datetime'=>SYS_TIME);
		$this->panel_db->insert($data, '', 1);
		$panelarr = $this->panel_db->listinfo(array('userid'=>param::get_session('userid')), "datetime");
		foreach($panelarr as $v) {
			$jscode .= '<span><a href="javascript:paneladdclass(this);" layuimini-content-href="'.$v['url'].'&menuid='.$v['menuid'].'&pc_hash='.dr_get_csrf_token().'" data-title="'.L($v['name']).'" data-icon="'.$v['icon'].'"><i class="'.$v['icon'].'"></i><cite>'.L($v['name']).'</cite></a> <a class="panel-delete" href="javascript:delete_panel('.$v['menuid'].');"></a></span>';
		}
		dr_json(1, L('operation_success'), array('jscode' => $jscode));
	}
	
	public function public_ajax_delete_panel() {
		$menuid = $this->input->post('menuid') ? $this->input->post('menuid') : dr_json(0, L('menuid不存在'));
		$this->panel_db->delete(array('menuid'=>$menuid, 'userid'=>param::get_session('userid')));

		$panelarr = $this->panel_db->listinfo(array('userid'=>param::get_session('userid')), "datetime");
		foreach($panelarr as $v) {
			$jscode .= '<span><a href="javascript:paneladdclass(this);" layuimini-content-href="'.$v['url'].'&menuid='.$v['menuid'].'&pc_hash='.dr_get_csrf_token().'" data-title="'.L($v['name']).'" data-icon="'.$v['icon'].'"><i class="'.$v['icon'].'"></i><cite>'.L($v['name']).'</cite></a> <a class="panel-delete" href="javascript:delete_panel('.$v['menuid'].');"></a></span>';
		}
		dr_json(1, L('operation_success'), array('jscode' => $jscode));
	}
	
	public function public_main() {
		pc_base::load_app_func('global');
		define('PC_VERSION', pc_base::load_config('version','pc_version'));
		define('PC_RELEASE', pc_base::load_config('version','pc_release'));
		define('CMS_VERSION', pc_base::load_config('version','cms_version'));
		define('CMS_RELEASE', pc_base::load_config('version','cms_release'));
		$path = CACHE_PATH.'caches_scan/safe/';
		$is_ok = $count = 0;
		for($key=1; $key <= 10; $key++) {
			if (is_file($path.$key.'.txt')) {
				$count++;
			}
		}
		if ($count == 10) {
			$is_ok = 1;
		}
		$this->role_db = pc_base::load_model('admin_role_model');
		$role = $this->role_db->select(array('roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'), 'disabled'=>0));
		if ($role) {
			foreach ($role as $r) {
				$info['role'][$r['roleid']] = $this->role[$r['roleid']]['rolename'];
			}
		}
		if (cleck_admin(param::get_session('roleid'))) {
			$class = 'danger';
		} else {
			$class = 'success';
		}
		$rolename = '';
		if(is_array($info['role'])){
			foreach($info['role'] as $c){
				if(!$rolename){
					$rolename .= '<span class="badge badge-'.$class.'">'.$c.'</span>';
				} else {
					$rolename .= ' <span class="badge badge-'.$class.'">'.$c.'</span>';
				}
			}
		}
		$admin_username = param::get_cookie('admin_username');
		$userid = param::get_session('userid');
		$r = $this->db->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = $this->db->version();
		$show_header = $show_pc_hash = true;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(PC_PATH.'base.php');
		$common_cache = getcache('common','commons');
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), '*',20 , 'datetime');
		$programmer = '（zhaoxunzhiyin）';
		$designer = '找寻知音';
		$qqgroup = '551419699';
		$qq = '297885395';
		include $this->admin_tpl('main');
	}
	// 版本检查
	public function public_version_cms() {
		define('CMS_VERSION', pc_base::load_config('version','cms_version'));
		define('CMS_RELEASE', pc_base::load_config('version','cms_release'));
		define('CMS_ID', pc_base::load_config('license','cms_id'));
		define('CMS_LICENSE', pc_base::load_config('license','cms_license') ? pc_base::load_config('license','cms_license') : 'dev');
		define('CMS_UPDATETIME', pc_base::load_config('version','cms_updatetime'));
		define('CMS_DOWNTIME', pc_base::load_config('version','cms_downtime'));

		list($this->admin_url) = explode('?', FC_NOW_URL);
		$this->service_url = CMS_CLOUD.'index.php?m=cloud&c=index&a=cloud&domain='.dr_get_domain_name(ROOT_URL).'&admin='.urlencode($this->admin_url).'&version='.CMS_VERSION.'&cms='.(CMS_ID ? CMS_ID : 1).'&updatetime='.strtotime(CMS_UPDATETIME).'&downtime='.strtotime(CMS_DOWNTIME).'&sitename='.base64_encode(dr_site_info('name', 1)).'&siteurl='.urlencode(dr_site_info('domain', 1)).'&php='.PHP_VERSION.'&mysql='.$this->db->version().'&os='.PHP_OS;
		$surl = $this->service_url.'&action=new';
		exit(dr_catcher_data($surl));
	}
	public function public_icon() {
		$show_header = $show_pc_hash = true;
		include $this->admin_tpl('icon');
	}
	public function public_error_log() {
		$show_header = $show_pc_hash = true;
		$time = (int)strtotime($this->input->get('time'));
		!$time && $time = SYS_TIME;
		$list = array();
		$page = max(1, (int)$this->input->get('page'));
		$total = 0;
		$file = CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d',$time).'.php';
		if (is_file($file)) {
			if (filesize($file) > 1024*1024*2) {
				$list[] = [
					'id' => 1,
					'time' => date('Y-m-d', $time),
					'type' => '<span class="label label-warning"> '.L('提醒').' </span>',
					'message' => '此日志文件大于2MB，请使用Ftp等工具查看此文件：'.$file,
				];
			} else {
				$c = file_get_contents($file);
				$data = explode(PHP_EOL, trim(str_replace('<?php defined(\'IN_CMS\') OR exit(\'No direct script access allowed\'); ?>'.PHP_EOL.PHP_EOL, '', str_replace(array(chr(13), chr(10)), PHP_EOL, $c)), PHP_EOL));
				$data && $data = array_reverse($data);
				//$total = max(0, count($data));$total = $data ? max(0, count($data) - 1) : 0;
				$total = max(0, substr_count($c, '- '.date('Y-m-d', $time).' '));
				$limit = ($page - 1) * SYS_ADMIN_PAGESIZE;
				$i = $j = 0;
				foreach ($data as $t) {
					if ($t && $i >= $limit && $j < SYS_ADMIN_PAGESIZE) {
						$v = explode(' --> ', $t);
						$time2 = $v ? explode(' - ', $v[0]) : [1=>''];
						if ($time2[1]) {
							$value = array(
								'time' => $time2[1] ? $time2[1] : '',
								'type' => '',
							);
							if ($time2[0] == 'DEBUG') {
								$value['type'] = '<span class="label label-success"> '.L('调试').' </span>';
							} elseif ($time2[0] == 'INFO') {
								$value['type'] = '<span class="label label-default"> '.L('信息').' </span>';
							} elseif ($time2[0] == 'WARNING') {
								$value['type'] = '<span class="label label-warning"> '.L('提醒').' </span>';
							} else {
								$value['type'] = '<span class="label label-danger"> '.L('错误').' </span>';
							}
							$value['id'] = $i + 1;
							if (strpos((string)$v[1], '{br}')) {
								$vv = explode('{br}', $v[1]);
								$value['message'] = $vv[0];
								unset($vv[0]);
								$value['json'] = str_replace("'", '\\\'', $vv[1]);
								unset($vv[1]);
								$value['info'] = '错误：'.$value['message'].'<br>';
								foreach ($vv as $p) {
									$value['info'].= $p.'<br>';
								}
								$value['info'] = str_replace("'", '\\\'', $value['info']);
							} else {
								$value['message'] = str_replace([PHP_EOL, chr(13), chr(10)], ' ', htmlentities((string)$v[1]));
								if (preg_match('/'.$value['time'].' \-\->(.*)\{main\}/sU', $c, $mt)) {
									$value['info'] = str_replace("'", '\\\'', str_replace([PHP_EOL, chr(13), chr(10)], '<br>', $mt[1]));
								}
							}
							$value['message'] = str_replace("'", '\\\'', $value['message']);
							$list[] = $value;
							$j ++;
						}
					}
					$i ++;
				}
			}
		}
		$time = date('Y-m-d', $time);
		$pages = pages($total, $page, SYS_ADMIN_PAGESIZE);
		include $this->admin_tpl('error_log');
	}
	public function public_error_log_show() {
		$show_header = $show_pc_hash = true;
		$time = dr_safe_filename($this->input->get('time'));
		!$time && $time = date('Y-m-d');
		$file = CACHE_PATH.'caches_error/caches_data/log-'.$time.'.php';
		if (!is_file($file)) {
			dr_admin_msg(0,L('文件不存在：'.$file),'','','edit');
		}
		if (filesize($file) > 1024*1024*2) {
			exit('此日志文件大于2MB，请使用Ftp等工具查看此文件：'.$file);
		}
		$code = file_get_contents($file);
		
		include $this->admin_tpl('error_file');
	}
	public function public_error_log_del() {
		$show_header = $show_pc_hash = true;
		$time = dr_safe_filename($this->input->get('time'));
		!$time && $time = date('Y-m-d');
		$file = CACHE_PATH.'caches_error/caches_data/log-'.$time.'.php';
		unlink($file);
		dr_json(1, L('operation_success'));
	}
	public function public_email_log() {
		$show_header = $show_pc_hash = true;
		$data = $list = array();
		$file = CACHE_PATH.'email_log.php';
		if (is_file(CACHE_PATH.'email_log.php')) {
			$data = explode(PHP_EOL, str_replace(array(chr(13), chr(10)), PHP_EOL, file_get_contents($file)));
			$data = $data ? array_reverse($data) : [];
			$page = max(1, (int)$this->input->get('page'));
			$limit = ($page - 1) * SYS_ADMIN_PAGESIZE;
			$i = $j = 0;
			foreach ($data as $v) {
				if ($v && $i >= $limit && $j < SYS_ADMIN_PAGESIZE) {
					$list[] = $v;
					$j ++;
				}
				$i ++;
			}
		}
		$total = $data ? max(0, count($data) - 1) : 0;
		$pages = pages($total, $page, SYS_ADMIN_PAGESIZE);
		include $this->admin_tpl('email_log');
	}
	public function public_email_log_del() {
		$show_header = $show_pc_hash = true;
		$file = CACHE_PATH.'email_log.php';
		unlink($file);
		dr_json(1, L('operation_success'));
	}
	/**
	 * 锁屏
	 */
	public function public_lock_screen() {
		param::set_session('lock_screen', 1);
	}
	public function public_login_screenlock() {
		$setting = getcache('common','commons');
		$admin_login_aes = (int)$setting['admin_login_aes'];
		if(empty($this->input->post('lock_password'))) dr_admin_msg(0,L('password_can_not_be_empty'));
		$is_aes = $this->input->post('is_aes');
		//密码错误剩余重试次数
		$this->times_db = pc_base::load_model('times_model');
		$username = param::get_cookie('admin_username');
		$setting = getcache('common','commons');
		$maxloginfailedtimes = (int)$setting['maxloginfailedtimes'];
		$sysadminlogintimes = isset($setting['sysadminlogintimes']) ? (int)$setting['sysadminlogintimes'] : 10;
		
		$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
		if ($rtime) {
			if ($maxloginfailedtimes) {
				if ($sysadminlogintimes && (int)$rtime['logintime'] && SYS_TIME - (int)$rtime['logintime'] > ($sysadminlogintimes * 60)) {
					// 超过时间了
					$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
				}
			}
			
			if ($maxloginfailedtimes) {
				if((int)$rtime['times'] && (int)$rtime['times'] >= $maxloginfailedtimes) {
					dr_admin_msg(0,L('wait_1_hour_lock'));
				}
			}
		}
		if ($admin_login_aes && !isset($is_aes)) {
			dr_json(0, L('当前登录模板不支持AES加密传输'));
		}
		//查询帐号
		$r = $this->db->get_one(array('userid'=>param::get_session('userid')));
		if ($admin_login_aes) {
			if (!function_exists('openssl_decrypt')) {
				log_message('error', '由于服务器环境没有启用openssl_decrypt，因此后台登录密码加密验证不被启用');
				dr_json(0, L('服务器环境不支持加密传输'));
			} else {
				$old = trim($this->input->post('lock_password'));
				$password = openssl_decrypt(
					$old,
					'AES-128-CBC',
					substr(md5(SYS_KEY), 0, 16), 0,
					substr(md5(SYS_KEY), 10, 16)
				);
				if (!$password) {
					dr_json(0, IS_DEV ? L('密码['.$old.']解析失败').openssl_error_string() : L('密码解析失败'));
				}
			}
			$password = md5(md5($password).$r['encrypt']);
		} else {
			$password = md5(trim($this->input->post('lock_password')).$r['encrypt']);
		}
		if($r['password'] != $password) {
			$ip = ip();
			if ($maxloginfailedtimes) {
				if($rtime && $rtime['times'] < $maxloginfailedtimes) {
					$times = $maxloginfailedtimes-intval($rtime['times']);
					$this->times_db->update(array('ip'=>$ip,'isadmin'=>1,'times'=>'+=1'),array('username'=>$username));
				} else {
					$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
					$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>1,'logintime'=>SYS_TIME,'times'=>1));
					$times = $maxloginfailedtimes;
				}
			}
			dr_admin_msg(0,L('password_error_lock').$times.L('password_error_lock2'));
		}
		$this->times_db->delete(array('username'=>$username,'isadmin'=>1));
		param::set_session('lock_screen', 0);
		dr_admin_msg(1,L('login_success'));
	}

	// 获取角色组
	public function get_role_all($rid = []) {
		$this->role_db = pc_base::load_model('admin_role_model');
		$role = array();
		$data = $this->role_db->select(array('disabled'=>'0'));
		if ($data) {
			foreach ($data as $t) {
				$role[$t['roleid']] = $t;
			}
		}
		return $role;
	}
}
?>