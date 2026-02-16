<?php
/**
 * 参数处理类
 */
class param {

	private $route_config = '';

	public function __construct() {
		$this->route_config = pc_base::load_config('route', DOMAIN_NAME) ? pc_base::load_config('route', DOMAIN_NAME) : pc_base::load_config('route', 'default');
		if(isset($this->route_config['data']['POST']) && is_array($this->route_config['data']['POST'])) {
			foreach($this->route_config['data']['POST'] as $_key => $_value) {
				if(!self::post($_key)) $_POST[$_key] = $_value;
			}
		}
		if(isset($this->route_config['data']['GET']) && is_array($this->route_config['data']['GET'])) {
			foreach($this->route_config['data']['GET'] as $_key => $_value) {
				if(!self::get($_key)) $_GET[$_key] = $_value;
			}
		}
	}

	/**
	 * 获取模型
	 */
	public function route_m() {
		$m = self::get('m') && !empty(self::get('m')) ? self::get('m') : (self::post('m') && !empty(self::post('m')) ? self::post('m') : '');
		$m = $this->safe_deal($m);
		if (IS_ADMIN) {
			if (!self::get_session('userid') && $m!='content') {
				$m = 'admin';
			} else {
				if (!self::get()) {
					dr_redirect(SELF.'?m=admin&c=index');
				}
			}
		}
		if (!IS_ADMIN) {
			if ($m == 'admin') {
				$m = 'content';
			}
		}
		if (empty($m)) {
			return $this->route_config['m'];
		} else {
			if(is_string($m)) return $m;
		}
	}

	/**
	 * 获取控制器
	 */
	public function route_c() {
		$c = self::get('c') && !empty(self::get('c')) ? self::get('c') : (self::post('c') && !empty(self::post('c')) ? self::post('c') : '');
		$c = $this->safe_deal($c);
		if (IS_ADMIN) {
			if (!self::get_session('userid') && $c!='database') {
				$c = 'index';
			}
		}
		if (empty($c)) {
			return $this->route_config['c'];
		} else {
			if(is_string($c)) return $c;
		}
	}

	/**
	 * 获取事件
	 */
	public function route_a() {
		$a = self::get('a') && !empty(self::get('a')) ? self::get('a') : (self::post('a') && !empty(self::post('a')) ? self::post('a') : '');
		$a = $this->safe_deal($a);
		if (IS_ADMIN) {
			if (!self::get_session('userid') && $a!='sms' && $a!='fclient' && $a!='public_logout' && $a!='public_login_screenlock' && $a!='recovery') {
				$a = SYS_ADMIN_PATH;
			}
		}
		if (!IS_ADMIN) {
			if (self::get('m') == 'admin') {
				if ($a == 'login') {
					$a = '';
				}
			}
		}
		if (empty($a)) {
			return $this->route_config['a'];
		} else {
			if(is_string($a)) return $a;
		}
	}

	/**
	 * 设置 cookie
	 */
	public static function set_cookie($name, $value = '', $expire = 0) {
		$expire = intval($expire) > 0 ? SYS_TIME + $expire : ($value == '' ? SYS_TIME - 3600 : 0);
		$name = SYS_KEY.COOKIE_PRE.$name;
		if (is_array($value)) {
			foreach($value as $k=>$v) {
				setcookie($name.'['.$k.']', $v, $expire, COOKIE_PATH, COOKIE_DOMAIN, (SITE_PROTOCOL == 'https://' ? true : false), true);
			}
		} else {
			setcookie($name, $value, $expire, COOKIE_PATH, COOKIE_DOMAIN, (SITE_PROTOCOL == 'https://' ? true : false), true);
		}
	}

	/**
	 * 获取通过 set_cookie 设置的 cookie 变量
	 */
	public static function get_cookie($name, $default = false) {
		$var_base = $name;
		$name = SYS_KEY.COOKIE_PRE.$name;
		$value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
		if(in_array($var_base, array('_userid','userid','siteid','_groupid'))) {
			$value = intval($value);
		} elseif(in_array($var_base, array('_username','username','_nickname','admin_username','sys_lang'))) {
			$value = safe_replace($value);
		}
		return $value;
	}

	public static function set_session($key, $value = '') {
		pc_base::load_sys_class('session')->set($key, $value);
	}

	public static function get_session($key) {
		return pc_base::load_sys_class('session')->get($key);
	}

	public static function del_session($key) {
		return pc_base::load_sys_class('session')->remove($key);
	}

	public static function setTempdata($key, $value = '', $time = 300) {
		pc_base::load_sys_class('session')->setTempdata($key, $value, $time);
	}

	public static function getTempdata($key) {
		return pc_base::load_sys_class('session')->getTempdata($key);
	}

	// get post解析
	public static function request($name, $xss = true) {
		return pc_base::load_sys_class('input')->request($name, $xss);
	}

	// post解析
	public static function post($name, $xss = true) {
		return pc_base::load_sys_class('input')->post($name, $xss);
	}

	// get解析
	public static function get($name = '', $xss = true) {
		return pc_base::load_sys_class('input')->get($name, $xss);
	}

	// 获取当前页面的URI
	public function uri() {
		$m = self::route_m().'/';
		$c = self::route_c().'/';
		$a = self::route_a().'/';
		return trim($m . $c . $a, '/');
	}

	// 自动识别的跳转动作
	public static function auto_redirect($url) {

		if (self::get('page') && intval(self::get('page')) > 1) {
			return;
		}

		// 跳转
		self::redirect($url, true);
	}

	// 执行跳转动作
	public static function redirect($url, $auto = false) {

		// 跳转
		if ($url != FC_NOW_URL) {
			if (!$auto) {
				if (IS_DEV) {
					if (defined('SYS_URL_ONLY') && SYS_URL_ONLY) {
						if (IS_ADMIN) {
							dr_admin_msg(0, '当前URL['.dr_now_url().']<br>与其本身地址['.$url.']不符<br>关闭开发者模式时本页面将成为404');
						} else {
							dr_msg(0, '当前URL['.dr_now_url().']<br>与其本身地址['.$url.']不符<br>关闭开发者模式时本页面将成为404');
						}
					}
					if (IS_ADMIN) {
						dr_admin_msg(1, '开发者模式：<br>当前URL['.dr_now_url().']<br>与其本身地址['.$url.']不符<br>正在自动跳转本身地址（关闭开发者模式时即可自动跳转）', $url, 9);
					} else {
						dr_msg(1, '开发者模式：<br>当前URL['.dr_now_url().']<br>与其本身地址['.$url.']不符<br>正在自动跳转本身地址（关闭开发者模式时即可自动跳转）', $url, 9);
					}
				} elseif (defined('SYS_URL_ONLY') && SYS_URL_ONLY) {
					self::goto_404_page('匹配地址与实际地址不符');
				}
			} elseif (IS_DEV) {
				// 自动识别
				if (IS_ADMIN) {
					dr_admin_msg(1, '开发者模式：<br>当前URL['.dr_now_url().']<br>自动识别为['.$url.']<br>若不需要识别功能可在后台-设置-站点管理-手机设置-关闭自动识别（如果开启了CDN请关闭自动识别）<br>正在自动跳转本身地址（关闭开发者模式时即可自动跳转）', $url, 9);
				} else {
					dr_msg(1, '开发者模式：<br>当前URL['.dr_now_url().']<br>自动识别为['.$url.']<br>若不需要识别功能可在后台-设置-站点管理-手机设置-关闭自动识别（如果开启了CDN请关闭自动识别）<br>正在自动跳转本身地址（关闭开发者模式时即可自动跳转）', $url, 9);
				}
			}
			dr_redirect($url, 'location', '301');
		}
	}

	/**
	 * 引用404页面
	 */
	public static function goto_404_page($msg = '') {

		pc_base::load_sys_class('hooks')::trigger('cms_404', $msg);

		if (IS_API) {
			dr_json(0, $msg);
		}

		// 调试模式下不进行404状态码
		if (!CI_DEBUG && defined('IS_HTML') && !IS_HTML) {
			http_response_code(404);
		}

		// 开启跳转404页面功能
		if (defined('SYS_GO_404') && SYS_GO_404) {
			if (CMSURI != '404.html') {
				if (IS_DEV) {
					$msg.= '（开发者模式下不跳转到404.html页面）';
				} else {
					dr_redirect(WEB_PATH.'404.html');
				}
			} else {
				$msg = L('你访问的页面不存在');
			}
		}

		if(intval(self::get('siteid'))) {
			$siteid = intval(self::get('siteid'));
		} else if(defined('SITE_ID') && SITE_ID!=1) {
			$siteid = SITE_ID;
		} else {
			$siteid = get_siteid();
		}
		$siteid = $GLOBALS['siteid'] = max($siteid,1);
		define('SITEID', $siteid);
		$SEO = seo($siteid, 0, L('你访问的页面不存在'));
		$default_style = dr_site_info('default_style', $siteid);
		if(!$default_style) $default_style = 'default';
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'msg' => $msg,
		]);
		pc_base::load_sys_class('service')->display('404', 'index', $default_style);
		defined('IS_HTML') && !IS_HTML && exit();
	}

	/**
	 * 安全处理函数
	 * 处理m,a,c
	 */
	private function safe_deal($str) {
		return str_replace(array('/', '.'), '', $str);
	}

}
?>