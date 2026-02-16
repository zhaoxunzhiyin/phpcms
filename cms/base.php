<?php
/**
 *  base.php CMS框架入口文件
 *
 * @copyright			(C) 2005-2010
 * @lastmodify			2021-06-06
 */
define('IN_CMS', TRUE);
define('IN_PHPCMS', IN_CMS);

// 是否是开发者模式
!defined('IS_DEV') && define('IS_DEV', FALSE);

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

// 移动入口标识
!defined('IS_MOBILE') && define('IS_MOBILE', FALSE);

// API接口项目标识
!defined('IS_API') && define('IS_API', FALSE);

// 采集入口标识
!defined('IS_COLLAPI') && define('IS_COLLAPI', FALSE);

//CMS框架路径
!defined('PC_PATH') && define('PC_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

!defined('CMS_PATH') && define('CMS_PATH', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('PHPCMS_PATH', CMS_PATH);

//缓存文件夹地址
!defined('CACHE_PATH') && define('CACHE_PATH', CMS_PATH.'caches'.DIRECTORY_SEPARATOR);
//主配置目录
!defined('CONFIGPATH') && define('CONFIGPATH', CACHE_PATH.'configs'.DIRECTORY_SEPARATOR);
//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

//系统开始时间
define('SYS_START_TIME', microtime(true));
//系统内存初始占用
define('SYS_START_MEM', memory_get_usage());

//PHP最低版本
define('MIN_PHP_VERSION', '7.1.0');

// 设置时区
define('SYS_TIMEZONE', pc_base::load_config('system','timezone'));
if (is_numeric(SYS_TIMEZONE) && strlen(SYS_TIMEZONE) > 0) {
	function_exists('date_default_timezone_set') && date_default_timezone_set('Etc/GMT'.(SYS_TIMEZONE > 0 ? '-' : '+').abs(SYS_TIMEZONE)); // 设置时区
}

define('CHARSET', pc_base::load_config('system','charset'));
//输出页面字符集
header('Content-Type: text/html; charset='.CHARSET);

// 定义模板目录
define('SYS_TPL_ROOT', pc_base::load_config('system','tpl_root'));
!defined('TPLPATH') && define('TPLPATH', PC_PATH.SYS_TPL_ROOT);
//网站时间显示格式与date函数一致，默认Y-m-d H:i:s
define('SYS_TIME_FORMAT', pc_base::load_config('system','sys_time_format'));
// 后台数据分页显示数量
define('SYS_ADMIN_PAGESIZE', intval(pc_base::load_config('system','sys_admin_pagesize')));
//temp目录
define('TEMPPATH', PC_PATH.'temp/');
//是否来自ajax提交
define('IS_AJAX', (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));
//是否来自post提交
define('IS_POST', isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST' ? TRUE : FALSE);
define('IS_AJAX_POST', IS_POST);
//当前系统时间戳
define('SYS_TIME', $_SERVER['REQUEST_TIME'] ? $_SERVER['REQUEST_TIME'] : time());
//百度地图API
define('SYS_BDMAP_API', pc_base::load_config('system','bdmap_api'));
//定义网站根路径
define('WEB_PATH', pc_base::load_config('system','web_path'));
//风格模式
define('SITE_THEME', intval(pc_base::load_config('system','site_theme')));
//js 路径
define('JS_PATH', pc_base::load_config('system','js_path'));
//css 路径
define('CSS_PATH', pc_base::load_config('system','css_path'));
//img 路径
define('IMG_PATH', pc_base::load_config('system','img_path'));
//手机js 路径
define('MOBILE_JS_PATH', pc_base::load_config('system','mobile_js_path'));
//手机css 路径
define('MOBILE_CSS_PATH', pc_base::load_config('system','mobile_css_path'));
//手机img 路径
define('MOBILE_IMG_PATH', pc_base::load_config('system','mobile_img_path'));
//动态程序路径
define('APP_PATH', pc_base::load_config('system','app_path'));
//动态程序手机路径
define('MOBILE_PATH', pc_base::load_config('system','mobile_path'));
// 调试器开关
define('SYS_DEBUG', pc_base::load_config('system','debug'));
//编辑器模式
define('SYS_EDITOR', intval(pc_base::load_config('system','sys_editor')));
//Session类型
define('SESSION_STORAGE', pc_base::load_config('system','session_storage'));
//Session生命周期
define('SESSION_TTL', pc_base::load_config('system','session_ttl'));
//Session目录
define('SESSION_SAVEPATH', pc_base::load_config('system','session_savepath'));
//Cookie前缀
define('COOKIE_PRE', pc_base::load_config('system','cookie_pre'));
//Cookie作用域
define('COOKIE_DOMAIN', pc_base::load_config('system','cookie_domain'));
//Cookie作用路径
define('COOKIE_PATH', pc_base::load_config('system','cookie_path'));
//自定义的后台登录地址
define('SYS_ADMIN_PATH', pc_base::load_config('system','admin_login_path') ? pc_base::load_config('system','admin_login_path') : 'login');
//是否需要检查外部访问
define('NeedCheckComeUrl', pc_base::load_config('system','needcheckcomeurl'));
//安全密匙
define('SYS_KEY', pc_base::load_config('system','auth_key'));
//网站语言包
define('SYS_LANGUAGE', pc_base::load_config('system','lang'));
//404页面跳转开关
define('SYS_GO_404', pc_base::load_config('system','sys_go_404'));
//内容地址唯一模式
define('SYS_301', pc_base::load_config('system','sys_301'));
//地址匹配规则
define('SYS_URL_ONLY', pc_base::load_config('system','sys_url_only'));
//CSRF令牌名称
define('SYS_TOKEN_NAME', pc_base::load_config('system','token_name') ? pc_base::load_config('system','token_name') : 'csrf_test_name');
//跨站验证
define('SYS_CSRF', pc_base::load_config('system','sys_csrf'));
//CSRF验证有效期
define('SYS_CSRF_TIME', pc_base::load_config('system','sys_csrf_time'));
//当前模板方案目录
define('SYS_TPL_NAME', pc_base::load_config('system','tpl_name'));
//是否允许在线编辑模板
define('IS_EDIT_TPL', pc_base::load_config('system','tpl_edit'));
//是否记录后台操作日志
define('SYS_ADMIN_LOG', pc_base::load_config('system','admin_log'));
//是否Gzip压缩后输出
define('SYS_GZIP', pc_base::load_config('system','gzip'));
//网站创始人ID
define('ADMIN_FOUNDERS', explode(',', pc_base::load_config('system','admin_founders')));
//生成静态文件路径
define('SYS_HTML_ROOT', pc_base::load_config('system','html_root'));
//生成手机静态文件路径
define('SYS_MOBILE_ROOT', pc_base::load_config('system','mobile_root'));
//关键词提取
define('SYS_KEYWORDAPI', pc_base::load_config('system','keywordapi'));
//百度关键词提取 APPID
define('SYS_BAIDU_AID', pc_base::load_config('system','baidu_aid'));
//百度关键词提取 APIKey
define('SYS_BAIDU_SKEY', pc_base::load_config('system','baidu_skey'));
//百度关键词提取 Secret Key
define('SYS_BAIDU_ARCRETKEY', pc_base::load_config('system','baidu_arcretkey'));
//分词数量
define('SYS_BAIDU_QCNUM', pc_base::load_config('system','baidu_qcnum'));
//讯飞关键词提取 APPID
define('SYS_XUNFEI_AID', pc_base::load_config('system','xunfei_aid'));
//讯飞关键词提取 APIKey
define('SYS_XUNFEI_SKEY', pc_base::load_config('system','xunfei_skey'));
//是否记录附件使用状态
define('SYS_ATTACHMENT_STAT', pc_base::load_config('system','attachment_stat'));
//附件是否使用分站
define('SYS_ATTACHMENT_FILE', pc_base::load_config('system','attachment_file'));
//是否同步删除附件
define('SYS_ATTACHMENT_DEL', pc_base::load_config('system','attachment_del'));
// 本地附件上传目录和地址
define('SYS_ATTACHMENT_SAVE_ID', pc_base::load_config('system','sys_attachment_save_id'));
define('SYS_ATTACHMENT_CF', pc_base::load_config('system','sys_attachment_cf'));
define('SYS_ATTACHMENT_PAGESIZE', intval(pc_base::load_config('system','sys_attachment_pagesize')));
define('SYS_ATTACHMENT_SAFE', pc_base::load_config('system','sys_attachment_safe'));
define('SYS_ATTACHMENT_PATH', pc_base::load_config('system','sys_attachment_path'));
define('SYS_ATTACHMENT_URL', pc_base::load_config('system','sys_attachment_url'));
define('SYS_ATTACHMENT_SAVE_TYPE', pc_base::load_config('system','sys_attachment_save_type'));
define('SYS_ATTACHMENT_SAVE_DIR', pc_base::load_config('system','sys_attachment_save_dir'));

!defined('CI_DEBUG') && define('CI_DEBUG', IS_DEV ? 1 : IS_ADMIN && SYS_DEBUG);
!defined('IS_DEBUG') && define('IS_DEBUG', CI_DEBUG ? 0 : 1);

// 显示错误提示
IS_ADMIN || IS_DEV ? error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED) : error_reporting(0);

// 显示错误提示
if (CI_DEBUG) {
	ini_set('display_errors', 1);
	// 重置Zend OPcache
	function_exists('opcache_reset') && opcache_reset();
} else {
	ini_set('display_errors', 0);
}

// 缓存变量
$cache = [];
if (is_file(CONFIGPATH.'cache.php')) {
	$cache = require CONFIGPATH.'cache.php';
	IS_DEV && $cache['SYS_CACHE'] = 0; // 开发者模式下关闭缓存
}
foreach (array(
		'SYS_CACHE',
		'SYS_CACHE_TYPE',
		'SYS_CACHE_CLEAR',
		'SYS_CACHE_SHOW',
		'SYS_CACHE_SMS',
		'SYS_CACHE_CRON',
	) as $name) {
	define($name, floatval($cache[$name]));
}
unset($cache);

if (SYS_ATTACHMENT_PATH
	&& (strpos(SYS_ATTACHMENT_PATH, '/') === 0 || strpos(SYS_ATTACHMENT_PATH, ':') !== false)
	&& is_dir(SYS_ATTACHMENT_PATH)) {
	// 相对于根目录
	// 附件上传目录
	define('SYS_UPLOAD_PATH', rtrim(SYS_ATTACHMENT_PATH, DIRECTORY_SEPARATOR).'/');
	// 附件访问URL
	define('SYS_UPLOAD_URL', trim(SYS_ATTACHMENT_URL, '/').'/');
} else {
	// 在当前网站目录
	$path = trim(SYS_ATTACHMENT_PATH ? SYS_ATTACHMENT_PATH : 'uploadfile', '/');
	// 附件上传目录
	define('SYS_UPLOAD_PATH', CMS_PATH.$path.'/');
	// 附件访问URL
	define('SYS_UPLOAD_URL', (SYS_ATTACHMENT_URL ? trim(SYS_ATTACHMENT_URL, '/').'/' : APP_PATH.$path.'/'));
}
if (pc_base::load_config('system','sys_avatar_path')
	&& (strpos(pc_base::load_config('system','sys_avatar_path'), '/') === 0 || strpos(pc_base::load_config('system','sys_avatar_path'), ':') !== false)
	&& is_dir(pc_base::load_config('system','sys_avatar_path'))) {
	// 相对于根目录
	// 头像上传目录
	define('SYS_AVATAR_PATH', rtrim(pc_base::load_config('system','sys_avatar_path'), DIRECTORY_SEPARATOR).'/');
	// 头像访问URL
	define('SYS_AVATAR_URL', trim(pc_base::load_config('system','sys_avatar_url'), '/').'/');
} else {
	// 在当前网站目录
	$avatarpath = trim(pc_base::load_config('system','sys_avatar_path') ? pc_base::load_config('system','sys_avatar_path') : 'uploadfile/avatar', '/');
	// 头像上传目录
	define('SYS_AVATAR_PATH', CMS_PATH.$avatarpath.'/');
	// 头像访问URL
	define('SYS_AVATAR_URL', (pc_base::load_config('system','sys_avatar_url') ? trim(pc_base::load_config('system','sys_avatar_url'), '/').'/' : APP_PATH.$avatarpath.'/'));
}
if (pc_base::load_config('system','sys_thumb_path')
	&& (strpos(pc_base::load_config('system','sys_thumb_path'), '/') === 0 || strpos(pc_base::load_config('system','sys_thumb_path'), ':') !== false)
	&& is_dir(pc_base::load_config('system','sys_thumb_path'))) {
	// 相对于根目录
	// 缩略图上传目录
	define('SYS_THUMB_PATH', rtrim(pc_base::load_config('system','sys_thumb_path'), DIRECTORY_SEPARATOR).'/');
	// 缩略图访问URL
	define('SYS_THUMB_URL', trim(pc_base::load_config('system','sys_thumb_url'), '/').'/');
} else {
	// 在当前网站目录
	$thumbpath = trim(pc_base::load_config('system','sys_thumb_path') ? pc_base::load_config('system','sys_thumb_path') : 'uploadfile/thumb', '/');
	// 缩略图上传目录
	define('SYS_THUMB_PATH', CMS_PATH.$thumbpath.'/');
	// 缩略图访问URL
	define('SYS_THUMB_URL', (pc_base::load_config('system','sys_thumb_url') ? trim(pc_base::load_config('system','sys_thumb_url'), '/').'/' : APP_PATH.$thumbpath.'/'));
}
define('CMS_CLOUD', 'https://ceshi.kaixin100.cn/');
/*
 * 重写is_cli
 */
function is_cli(): bool {
	if (stripos(PHP_SAPI, 'cli') !== false || defined('STDIN')) {
		return true;
	}
	return false;
}
if (is_cli()) {
	// CLI命令行模式
	define('ADMIN_URL', 'http://localhost/');
	define('FC_NOW_URL', 'http://localhost/');
	define('FC_NOW_HOST', 'http://localhost/');
	define('DOMAIN_NAME', 'http://localhost/');
	define('WEB_DIR', WEB_PATH);
	if ($_SERVER["argv"]) {
		foreach ($_SERVER["argv"] as $val) {
			if (strpos($val, '=') !== false) {
				list($name) = explode('=', $val);
				$_GET[$name] = substr($val, strlen($name)+1);
			}
		}
	}
} else {
	// 正常访问模式
	// 当前URL
	$url = 'http';
	if ((defined('IS_HTTPS_FIX') && IS_HTTPS_FIX)
		|| (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443')
		|| (isset($_SERVER['HTTP_X_CLIENT_SCHEME']) && $_SERVER['HTTP_X_CLIENT_SCHEME'] == 'https')
		|| (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https')
		|| (isset($_SERVER['HTTP_EWS_CUSTOME_SCHEME']) && $_SERVER['HTTP_EWS_CUSTOME_SCHEME'] == 'https')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		|| (isset($_SERVER['HTTP_FROM_HTTPS']) && $_SERVER['HTTP_FROM_HTTPS'] == 'on')
		|| (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) != 'off')
	) {
		$url.= 's';
	}
	// 主机协议
	define('SITE_PROTOCOL', $url.'://');
	$host = strtolower($_SERVER['HTTP_HOST']);
	if (strpos($host, ':') !== false) {
		list($nhost, $port) = explode(':', $host);
		if ($port == 80) {
			$host = $nhost; // 排除80端口
		}
	}
	$url.= '://'.$host;
	IS_ADMIN && define('ADMIN_URL', $url.'/'); // 优先定义后台域名
	define('FC_NOW_URL', $url.($_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] ? $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'])));
	define('FC_NOW_HOST', $url.'/'); // 域名部分
	define('DOMAIN_NAME', $host); // 当前域名

	// 伪静态字符串
	$uu = isset($_SERVER['HTTP_X_REWRITE_URL']) || trim($_SERVER['REQUEST_URI'], '/') == SELF ? trim($_SERVER['HTTP_X_REWRITE_URL'], '/') : ($_SERVER['REQUEST_URI'] ? trim($_SERVER['REQUEST_URI'], '/') : NULL);
	if (defined('FIX_WEB_DIR') && FIX_WEB_DIR && strpos($uu, FIX_WEB_DIR) !== false && strpos($uu, FIX_WEB_DIR) === 0) {
		$uu = trim(substr($uu, strlen(FIX_WEB_DIR)), '/');
		define('WEB_DIR', WEB_PATH.trim(FIX_WEB_DIR, '/').'/');
	} else {
		define('WEB_DIR', WEB_PATH);
	}

	// 以index.php或者?开头的uri不做处理
	$uri = strpos($uu, SELF) === 0 || strpos($uu, '?') === 0 ? '' : $uu;

	// 当前URI
	define('CMSURI', $uri);

	// 根据自定义URL规则来识别路由
	if (!IS_ADMIN && CMSURI && !IS_API) {
		// 自定义URL解析规则
		$routes = [];
		$routes['index\.html(.*)'] = 'index.php?m=content&c=index';
		$routes['404\.html(.*)'] = 'index.php?m=404&uri='.CMSURI;
		$routes['rewrite-test.html(.*)'] = 'index.php?m=content&c=index&a=test';
		if (is_file(CONFIGPATH.'rewrite.php')) {
			$my = require CONFIGPATH.'rewrite.php';
			$my && is_array($my) && $routes = array_merge($routes, $my);
		}
		// 正则匹配路由规则
		$is_404 = 1;
		foreach ($routes as $key => $val) {
			$rewrite = $match = []; //(defined('SYS_URL_PREG') && SYS_URL_PREG ? '' : '$')
			if ($key == CMSURI || preg_match('/^'.$key.'$/U', CMSURI, $match)) {
				unset($match[0]);
				// 开始匹配
				$is_404 = 0;
				// 开始解析路由 URL参数模式
				$_GET = [];
				$queryParts = explode('&', str_replace(['index.php?', '/index.php?'], '', $val));
				if ($queryParts) {
					foreach ($queryParts as $param) {
						$item = explode('=', $param);
						$_GET[$item[0]] = $item[1];
						if (strpos($item[1], '$') !== FALSE) {
							$id = (int)substr($item[1], 1);
							$_GET[$item[0]] = isset($match[$id]) ? $match[$id] : $item[1];
						}
					}
				}
				!$_GET['m'] && $_GET['m'] = 'content';
				!$_GET['c'] && $_GET['c'] = 'index';
				// 结束匹配
				break;
			}
		}
		if (trim(WEB_PATH, '/') && strpos(CMSURI, trim(WEB_PATH, '/')) !== FALSE) {
			$is_404 = 0;
		}
		// 说明是404
		if ($is_404) {
			$_GET['m'] = '404';
			$_GET['uri'] = CMSURI;
		}
	}
	// 自定义路由模式
	if (is_file(CONFIGPATH.'router.php')) {
		require CONFIGPATH.'router.php';
	}
}

!defined('CMSURI') && define('CMSURI', '');

// 站点ID
!defined('SITE_ID') && define('SITE_ID', 1);

//加载公用函数库
pc_base::load_sys_func('global');
pc_base::load_sys_func('extention');
pc_base::auto_load_func();

// 主站URL
define('ROOT_URL', APP_PATH);
// 站点URL
define('SITE_URL', siteurl(SITE_ID));
define('SITE_MURL', sitemobileurl(SITE_ID));

// 自定义入口执行
if (function_exists('cms_init')) {
	cms_init();
}

if(SYS_GZIP && function_exists('ob_gzhandler')) {
	ob_start('ob_gzhandler');
} else {
	ob_start();
}

class pc_base {
	
	/**
	 * 初始化应用程序
	 */
	public static function creat_app() {
		return self::load_sys_class('application');
	}
	/**
	 * 加载系统类方法
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	public static function load_sys_class($classname, $path = '', $initialize = 1) {
		return self::_load_class($classname, $path, $initialize);
	}
	
	/**
	 * 加载应用类方法
	 * @param string $classname 类名
	 * @param string $m 模块
	 * @param intger $initialize 是否初始化
	 */
	public static function load_app_class($classname, $m = '', $initialize = 1) {
		$m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_class($classname, 'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'classes', $initialize);
	}
	
	/**
	 * 加载数据模型
	 * @param string $classname 类名
	 */
	public static function load_model($classname) {
		return self::_load_class($classname,'model');
	}
		
	/**
	 * 加载类文件函数
	 * @param string $classname 类名
	 * @param string $path 扩展地址
	 * @param intger $initialize 是否初始化
	 */
	private static function _load_class($classname, $path = '', $initialize = 1) {
		static $classes = array();
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'classes';

		$key = md5($path.$classname);
		if (isset($classes[$key])) {
			if (!empty($classes[$key])) {
				return $classes[$key];
			} else {
				return true;
			}
		}
		if (file_exists(PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
			include PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php';
			$name = $classname;
			if ($my_path = self::my_path(PC_PATH.$path.DIRECTORY_SEPARATOR.$classname.'.class.php')) {
				include $my_path;
				$name = 'MY_'.$classname;
			}
			if ($initialize) {
				$classes[$key] = new $name;
			} else {
				$classes[$key] = true;
			}
			return $classes[$key];
		} else {
			CI_DEBUG && log_message('debug', '类文件：'.$path.DIRECTORY_SEPARATOR.$classname.'.class.php不存在');
			return false;
		}
	}
	
	/**
	 * 加载系统的函数库
	 * @param string $func 函数库名
	 */
	public static function load_sys_func($func) {
		return self::_load_func($func);
	}
	
	/**
	 * 自动加载autoload目录下函数库
	 * @param string $func 函数库名
	 */
	public static function auto_load_func($path='') {
		return self::_auto_load_func($path);
	}
	
	/**
	 * 加载应用函数库
	 * @param string $func 函数库名
	 * @param string $m 模型名
	 */
	public static function load_app_func($func, $m = '') {
		$m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
		if (empty($m)) return false;
		return self::_load_func($func, 'modules'.DIRECTORY_SEPARATOR.$m.DIRECTORY_SEPARATOR.'functions');
	}
	
	/**
	 * 加载函数库
	 * @param string $func 函数库名
	 * @param string $path 地址
	 */
	private static function _load_func($func, $path = '') {
		static $funcs = array();
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'functions';
		$path .= DIRECTORY_SEPARATOR.$func.'.func.php';
		$key = md5($path);
		if (isset($funcs[$key])) return true;
		if (file_exists(PC_PATH.$path)) {
			include PC_PATH.$path;
		} else {
			$funcs[$key] = false;
			CI_DEBUG && log_message('debug', '函数文件不存在：'.$path);
			return false;
		}
		$funcs[$key] = true;
		return true;
	}
	
	/**
	 * 加载函数库
	 * @param string $func 函数库名
	 * @param string $path 地址
	 */
	private static function _auto_load_func($path = '') {
		if (empty($path)) $path = 'libs'.DIRECTORY_SEPARATOR.'functions'.DIRECTORY_SEPARATOR.'autoload';
		$path .= DIRECTORY_SEPARATOR.'*.func.php';
		$auto_funcs = glob(PC_PATH.DIRECTORY_SEPARATOR.$path);
		if(!empty($auto_funcs) && is_array($auto_funcs)) {
			foreach($auto_funcs as $func_path) {
				include $func_path;
			}
		}
	}
	/**
	 * 是否有自己的扩展文件
	 * @param string $filepath 路径
	 */
	public static function my_path($filepath) {
		$path = pathinfo($filepath);
		if (file_exists($path['dirname'].DIRECTORY_SEPARATOR.'MY_'.$path['basename'])) {
			return $path['dirname'].DIRECTORY_SEPARATOR.'MY_'.$path['basename'];
		} else {
			return false;
		}
	}
	
	/**
	 * 加载配置文件
	 * @param string $file 配置文件
	 * @param string $key 要获取的配置荐
	 * @param string $default 默认配置。当获取配置项目失败时该值发生作用。
	 * @param boolean $reload 强制重新加载。
	 */
	public static function load_config($file, $key = '', $default = '', $reload = false) {
		static $configs = array();
		if (!$reload && isset($configs[$file])) {
			if (empty($key)) {
				return $configs[$file];
			} elseif (isset($configs[$file][$key])) {
				return $configs[$file][$key];
			} else {
				return $default;
			}
		}
		$path = CONFIGPATH.$file.'.php';
		if (file_exists($path)) {
			$configs[$file] = include $path;
		}
		if (empty($key)) {
			return $configs[$file];
		} elseif (isset($configs[$file][$key])) {
			return $configs[$file][$key];
		} else {
			return $default;
		}
	}
}