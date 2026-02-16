<?php
/**
 *  application.class.php CMS应用程序创建类
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2010-6-7
 */

use OutOfBoundsException;

class application {

	private $debug;

	private $param;

	static private $filters = [
		'home' => [],
		'member' => [],
		'admin' => [],
	];
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		//判断环境
		if (version_compare(PHP_VERSION, MIN_PHP_VERSION) < 0) {
			exit('<font color=red>PHP版本必须在'.MIN_PHP_VERSION.'及以上，当前'.PHP_VERSION.'</font>');
		}
		$this->debug = pc_base::load_sys_class('debug');
		set_exception_handler([$this->debug, 'exceptionHandler']);
		set_error_handler([$this->debug, 'errorHandler']);
		register_shutdown_function([$this->debug, 'shutdownHandler']);
		$this->param = pc_base::load_sys_class('param');
		define('ROUTE_M', $this->param->route_m());
		define('ROUTE_C', $this->param->route_c());
		define('ROUTE_A', $this->param->route_a());
		// 访客唯一标识
		!defined('USER_HTTP_CODE') && define('USER_HTTP_CODE', md5($this->param->get_cookie('_userid').pc_base::load_sys_class('input')->ip_address().pc_base::load_sys_class('input')->get_user_agent()));
		// 是否会员
		if (!IS_API && defined('ROUTE_M') && ROUTE_M && preg_match('/^[a-z_]+$/i', ROUTE_M)) {
			if (!IS_ADMIN && ROUTE_M == 'member') {
				define('IS_MEMBER', TRUE);
			} else {
				define('IS_MEMBER', FALSE);
			}
		} else {
			define('IS_MEMBER', FALSE);
		}
		// 是否前端
		define('IS_HOME', !IS_ADMIN && !IS_MEMBER);
		if (defined('IS_CLIENT')) {
			// 存在自定义终端
			pc_base::load_sys_class('service')->init(defined('IS_CLIENT_TPL') && IS_CLIENT_TPL ? IS_CLIENT_TPL : IS_CLIENT);
			!defined('CLIENT_URL') && define('CLIENT_URL', get_cache('site', SITE_ID, 'client', IS_CLIENT));
			define('CLIENT_NAME', IS_CLIENT);
		} elseif ((defined('IS_MOBILE') && IS_MOBILE) || (is_mobile(SITE_ID) && dr_site_info('mobileauto', SITE_ID))) {
			// 移动端模板 // 开启自动识别移动端
			pc_base::load_sys_class('service')->init('mobile');
			define('CLIENT_URL', SITE_MURL);
			define('CLIENT_NAME', 'mobile');
		} else {
			// 默认情况下pc模板
			pc_base::load_sys_class('service')->init('pc');
			define('CLIENT_URL', SITE_URL);
			define('CLIENT_NAME', 'pc');
		}
		!defined('IS_CLIENT') && define('IS_CLIENT', '');
		if (module_exists('content') && is_file(PC_PATH.'modules/content/config/run.php')) {
			require PC_PATH.'modules/content/config/run.php';
		}
		$this->init();
		// 挂钩点 程序结束之后
		pc_base::load_sys_class('hooks')::trigger('cms_close');
	}
	
	/**
	 * 调用件事
	 */
	private function init() {
		$local = pc_base::load_sys_class('service')::apps();
		if ($local) {
			foreach ($local as $dir => $path) {
				if (!module_exists($dir)) {
					continue;
				}
				// 加载钩子
				if (is_file($path.'config/hooks.php')) {
					require $path.'config/hooks.php';
				}
				// 判断是否存在CSRF白名单
				if (is_file($path.'config/filters.php')) {
					$Filters = require $path.'config/filters.php';
					if ($Filters && is_array($Filters)) {
						$Filters['home'] && static::$filters['home'] = array_merge(static::$filters['home'], $Filters['home']);
						$Filters['member'] && static::$filters['member'] = array_merge(static::$filters['member'], $Filters['member']);
						$Filters['admin'] && static::$filters['admin'] = array_merge(static::$filters['admin'], $Filters['admin']);
					}
				}
			}
		}
		$this->verify();
		// 挂钩点 程序运行之前
		pc_base::load_sys_class('hooks')::trigger('cms_run');
		IS_API != 'api' && $controller = $this->load_controller();
		// 挂钩点 程序加载之后
		pc_base::load_sys_class('hooks')::trigger('init');
		// 挂钩点 程序初始化之后
		pc_base::load_sys_class('hooks')::trigger('cms_init');
		if (IS_API === 'api') {
			if($this->param->get_cookie('_userid')) {
				if(pc_base::load_model('member_model')->get_one(array('userid'=>$this->param->get_cookie('_userid')),'islock')['islock']) dr_msg(0, L('user_is_lock', '', 'member'));
			}
			$op = pc_base::load_sys_class('input')->get('op') && trim(pc_base::load_sys_class('input')->get('op')) ? trim(pc_base::load_sys_class('input')->get('op')) : dr_msg(0, '操作不能为空');
			if (pc_base::load_sys_class('input')->get('callback') && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]+$/', pc_base::load_sys_class('input')->get('callback'))) '';
			if (!preg_match('/([^a-z_]+)/i', $op) && file_exists(CMS_PATH.'api/'.$op.'.php')) {
				include CMS_PATH.'api/'.$op.'.php';
				if (!IS_ADMIN && IS_DEV && !IS_AJAX) {
					$this->debug->message();
				}
			} else {
				throw new \OutOfBoundsException('API处理程序不存在<br>检查此文件是否存在：'.CMS_PATH.'api/'.$op.'.php，检查地址是否正确，注意控制器文件');
			}
		} else {
			if (method_exists($controller, ROUTE_A)) {
				if (preg_match('/^[_]/i', ROUTE_A)) {
					dr_exit_msg(0, 'You are visiting the action is to protect the private action');
				} else {
					call_user_func(array($controller, ROUTE_A));
					if (IS_ADMIN && CI_DEBUG && !IS_AJAX) {
						if (!in_array(ROUTE_M, array('admin')) || !in_array(ROUTE_C, array('index')) && !in_array(ROUTE_A, array('public_main'))) {
							$this->debug->message();
						}
					}
					if (!IS_ADMIN && IS_DEV && !IS_AJAX) {
						$this->debug->message();
					}
				}
			} else {
				throw new \OutOfBoundsException('Controller method is not found: '.ROUTE_A);
			}
		}
	}

	/**
	 * 加载控制器
	 * @param string $filename
	 * @param string $m
	 * @return obj
	 */
	private function load_controller($filename = '', $m = '') {
		if (empty($filename)) $filename = ROUTE_C;
		if (empty($m)) $m = ROUTE_M;
		$filepath = PC_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR;
		if(!is_dir($filepath)){
			dr_show_error('应用程序('.$filepath.')不存在');
		}
		if (!module_exists($m)) {
			if (IS_ADMIN) {
				if ($m=='vote') {
					dr_admin_msg(0,L('module_not_exists', array('module' => $m)),'close',3,1);
				} else {
					dr_admin_msg(0,L('module_not_exists', array('module' => $m)),'close');
				}
			} else {
				dr_msg(0,L('module_not_exists', array('module' => $m)));
			}
		}
		$filepath = PC_PATH.'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.$filename.'.php';
		if (file_exists($filepath)) {
			$classname = $filename;
			include $filepath;
			if ($mypath = pc_base::my_path($filepath)) {
				$classname = 'MY_'.$filename;
				include $mypath;
			}
			
			if(class_exists($classname)){
				return new $classname;
			}else{
				throw new \OutOfBoundsException('Controller or its method is not found: modules/'.$m.'/'.$filename.'('.$classname.')::'.ROUTE_A);
 			}
		} else {
			throw new \OutOfBoundsException('Controller or its method is not found: modules/'.$m.'/'.$filename.'::'.ROUTE_A);
		}
	}

	// 读取Filters白名单
	public static function Filters($type = 'auto') {

		if ($type == 'auto') {
			if (IS_ADMIN) {
				$type = 'admin';
			} elseif (IS_MEMBER) {
				$type = 'member';
			} else {
				$type = 'home';
			}
		} elseif ($type == '') {
			return static::$filters;
		}

		return isset(static::$filters[$type]) ? static::$filters[$type] : [];
	}
	
	/**
	 * CSRF Verify
	 */
	public function verify() {
		if (defined('SYS_CSRF') && !SYS_CSRF) {
			return $this;
		} elseif (defined('SYS_CSRF') && SYS_CSRF == 1 && IS_ADMIN) {
			return $this;
		} elseif (in_array($this->param->uri(), static::Filters())) {
			return $this;
		} elseif (defined('IS_API') && IS_API) {
			// api 请求下不做验证
			return $this;
		} elseif (defined('IS_INSTALL')) {
			return $this;
		}
		if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
			return $this;
		}
		$token = pc_base::load_sys_class('input')->post(SYS_TOKEN_NAME);
		$hash = csrf_hash();
		if (!isset($token, $hash) || !hash_equals($hash, $token)) {
			CI_DEBUG && log_message('debug', '跨站验证拦截（'.$hash.' / '.$token.'）');
			dr_exit_msg(0, '跨站验证超时请重试', '', array('name' => SYS_TOKEN_NAME, 'value' => $hash));
		}
		if (defined('SYS_CSRF_TIME') && SYS_CSRF_TIME) {
			pc_base::load_sys_class('cache')->del_auth_data(COOKIE_PRE.ip().'csrf_token', 1);
		}
		unset($token);
		if (isset($hash)) {
			unset($hash);
		}
		return $this;
	}
}