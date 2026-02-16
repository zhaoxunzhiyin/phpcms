<?php
// 是否是开发者模式（TRUE开启、FALSE关闭）
define('IS_DEV', FALSE);
// 入口文件名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

define('IS_INSTALL', TRUE);
set_time_limit(0);
include '../cms/base.php';
defined('IN_CMS') or exit('No permission resources.');
if (is_file(CACHE_PATH.'install.lock')) exit('安装程序已经被锁定，重新安装请删除：caches/install.lock');
// 判断环境
if (version_compare(PHP_VERSION, MIN_PHP_VERSION) < 0) {
	exit('<font color=red>PHP版本必须在'.MIN_PHP_VERSION.'及以上，当前'.PHP_VERSION.'</font>');
}
if (preg_match('/[\x{4e00}-\x{9fff}]+/u', CMS_PATH)) {
	exit('<font color=red>WEB目录['.CMS_PATH.']不允许出现中文或全角符号</font>');
}
foreach (array(' ', '[', ']') as $t) {
	if (strpos(CMS_PATH, $t) !== false) {
		exit('<font color=red>WEB目录['.CMS_PATH.']不允许出现'.($t ? $t : '空格').'符号</font>');
	}
}
$steps = include CMS_PATH.'install/step.inc.php';
$step = trim(pc_base::load_sys_class('input')->request('step')) ? trim(pc_base::load_sys_class('input')->request('step')) : 1;
$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
$rootpath = str_replace('\\','\/',dirname($PHP_SELF));
$rootpath = substr($rootpath,0,-7);
$rootpath = strlen($rootpath)>1 ? $rootpath : '/';
if (is_file(CACHE_PATH.'configs/version.php')) {
	$app = pc_base::load_config('version');
	define('PC_VERSION', $app['pc_version']);
	define('PC_RELEASE', $app['pc_release']);
	define('CMS_VERSION', $app['cms_version']);
	define('CMS_RELEASE', $app['cms_release']);
} else {
	define('PC_VERSION', '');
	define('PC_RELEASE', '');
	define('CMS_VERSION', '');
	define('CMS_RELEASE', '');
}

switch($step) {
	case '1': //安装许可协议
		$license = file_get_contents(CMS_PATH.'install/license.txt');
		
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';

		break;
	
	case '2': //环境检测
		$error = 0;
		$php = array(
			array(
				'name' => 'PHP 版本',
				'value' => 'PHP '.PHP_VERSION,
				'error_value' => 'PHP '.MIN_PHP_VERSION.' 及以上',
				'code' => PHP_VERSION >= MIN_PHP_VERSION,
				'help' => '&nbsp;无法安装',
				'error' => 1,
			),
			array(
				'name' => 'MYSQLI 扩展',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => extension_loaded('mysqli'),
				'help' => '&nbsp;无法安装',
				'error' => 1,
			),
			array(
				'name' => 'ICONV 扩展',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => extension_loaded('iconv'),
				'help' => '&nbsp;字符集转换效率低',
				'error' => 1,
			),
			array(
				'name' => 'JSON 扩展',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => extension_loaded('json') && function_exists('json_decode') && function_exists('json_encode'),
				'help' => '&nbsp;不支持json,<a href="http://pecl.php.net/package/json" target="_blank">安装 PECL扩展</a>',
				'error' => 1,
			),
			array(
				'name' => 'GD 扩展',
				'value' => '√（支持 png jpg gif）',
				'error_value' => '必须开启',
				'code' => extension_loaded('gd'),
				'help' => '&nbsp;不支持缩略图和水印',
				'error' => 1,
			),
			array(
				'name' => 'mb string扩展',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => function_exists('mb_substr'),
				'help' => '&nbsp;无法安装',
				'error' => 1,
			),
			array(
				'name' => 'Curl扩展',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => function_exists('curl_init'),
				'help' => '&nbsp;无法安装',
				'error' => 1,
			),
			array(
				'name' => 'allow_url_fopen',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => ini_get('allow_url_fopen'),
				'help' => '&nbsp;不支持保存远程图片',
				'error' => 1,
			),
			array(
				'name' => 'fsockopen',
				'value' => '√',
				'error_value' => '必须开启',
				'code' => function_exists('fsockopen'),
				'help' => '&nbsp;不支持fsockopen函数',
				'error' => 1,
			),
			array(
				'name' => 'ZLIB 扩展',
				'value' => '√',
				'error_value' => '建议开启',
				'code' => extension_loaded('zlib'),
				'help' => '&nbsp;不支持Gzip功能',
				'error' => 0,
			),
			array(
				'name' => 'FTP 扩展',
				'value' => '√',
				'error_value' => '建议开启',
				'code' => extension_loaded('ftp'),
				'help' => '&nbsp;不支持FTP形式文件传送',
				'error' => 0,
			),
			array(
				'name' => 'DNS解析',
				'value' => '√',
				'error_value' => '建议开启',
				'code' => preg_match('/^[0-9.]{7,15}$/', @gethostbyname('www.baidu.com')) ? 1 : 0,
				'help' => '&nbsp;不支持采集和保存远程图片',
				'error' => 0,
			),
		);
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;
	
	case '3': //检测目录属性
		$error = 0;
		// 目录权限检查
		$dir = array(
			CACHE_PATH,
			CACHE_PATH.'configs/',
			CACHE_PATH.'caches_admin/',
			CACHE_PATH.'caches_attach/',
			CACHE_PATH.'caches_commons/',
			CACHE_PATH.'caches_content/',
			CACHE_PATH.'caches_data/',
			CACHE_PATH.'caches_file/',
			CACHE_PATH.'caches_linkage/',
			CACHE_PATH.'caches_member/',
			CACHE_PATH.'caches_model/',
			CACHE_PATH.'caches_scan/',
			CACHE_PATH.'caches_template/',
			CACHE_PATH.'poster_js/',
			CACHE_PATH.'vote_js/',
			CACHE_PATH.'sessions/',
			CMS_PATH.'html/',
			CMS_PATH.'uploadfile/',
			CMS_PATH,
		);
		$path = array();
		foreach ($dir as $t) {
			$path[$t] = dr_check_put_path($t);
			if (!$path[$t]) {
				$error = 1;
			}
		}
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;

	case '4': //配置帐号 （MYSQL帐号、管理员帐号、）
		$database = pc_base::load_config('database');
		extract($database['default']);
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;

	case '5': //安装详细过程
		extract($_POST);
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;

	case '6': //安装详细过程
		extract($_POST);
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;

	case '7': //完成安装
		$data = dr_string2array(file_get_contents(CACHE_PATH.'install.info'));
		unlink(CACHE_PATH.'install.info');
		file_put_contents(CACHE_PATH.'install.lock', time());
		include CMS_PATH.'install/step/step'.$step.'.tpl.php';
		break;
	
	case 'sql': //安装数据
		$page = intval(pc_base::load_sys_class('input')->post('page'));
		$data = dr_string2array(file_get_contents(CACHE_PATH.'install.info'));
		if (empty($data)) {
			dr_json(0, '临时数据获取失败，请返回前一页重新执行');
		}
		$GLOBALS['dbcharset'] = $data['dbcharset'];
		$GLOBALS['tablepre'] = $data['tablepre'];
		
		$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
		if (!$mysqli) {
			dr_json(0, 'PHP环境必须启用Mysqli扩展！');
		} elseif (!mysqli_real_connect($mysqli, $data['dbhost'], $data['dbuser'], $data['dbpw'], null, $data['dbport'])) {
			dr_json(0, '['.mysqli_connect_errno().'] - 无法连接到数据库服务器（'.$data['dbhost'].'），请检查端口（'.$data['dbport'].'）和用户名（'.$data['dbuser'].'）和密码（'.$data['dbpw'].'）是否正确！');
		} elseif (!mysqli_select_db($mysqli, $data['dbname'])) {
			if (!mysqli_query($mysqli, 'CREATE DATABASE `'.$data['dbname'].'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
				dr_json(0, '指定的数据库（'.$data['dbname'].'）不存在，系统尝试创建失败，请先通过其他方式建立好数据库！');
			}
		}
		if (mysqli_get_server_version($mysqli) < 50500) {
			dr_json(0, '数据库版本低于Mysql 5.5，无法安装CMS，请升级数据库版本！');
		}
		if (!mysqli_set_charset($mysqli, 'utf8mb4')) {
			dr_json(0, '当前MySQL不支持utf8mb4编码（'.mysqli_error($mysqli).'）建议升级MySQL版本！');
		}
		$dbfile = 'cms_db.sql';
		if(file_exists(CMS_PATH.'install/main/'.$dbfile)) {
			// 导入数据结构
			if ($page) {
				$sql = file_get_contents(CMS_PATH.'install/main/'.$dbfile);
				$sql = str_replace('CMS演示站', $data['name'] , $sql);
				$sql = str_replace('http://www.kaixin100.cn/', FC_NOW_HOST.substr($rootpath, 1), $sql);
				$sql = str_replace('{dbprefix}', $data['tablepre'], $sql);
				$rows = query_rows($sql, 10);
				$key = $page - 1;
				if (isset($rows[$key]) && $rows[$key]) {
					// 安装本次结构
					foreach($rows[$key] as $query){
						if (!$query) {
							continue;
						}
						$ret = '';
						$queries = explode('SQL_CMS_EOL', trim($query));
						foreach($queries as $query) {
							$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
						}
						if (!$ret) {
							continue;
						}
						_sql_execute($mysqli, $ret);
					}
					dr_json(1, '正在执行：'.str_cut($ret, 70), ['page' => $page + 1]);
				} else {
					//创建网站创始人
					$username = $data['username'];
					$password_arr = password($data['password']);
					$password = $password_arr['password'];
					$encrypt = $password_arr['encrypt'];
					$email = trim($data['email']);
					_sql_execute($mysqli, "INSERT INTO ".$data['tablepre']."admin (`userid`,`username`,`password`,`roleid`,`encrypt`,`lastloginip`,`email`,`phone`,`realname`,`lang`) VALUES ('1','$username','$password','[\"1\"]','$encrypt','','$email','','创始人','zh-cn')");
					dr_json(1, '执行完成，即将安装数据信息...', ['page' => 99]);
				}
			}
		} else {
			dr_json(0, '/install/main/cms_db.sql 数据库文件不存在');
		}
		break;
	
	case 'cache':
		// 完成
		$page = intval(pc_base::load_sys_class('input')->post('page'));
		$cache = pc_base::load_app_class('cache_api','admin');
		$cache->cache('category');
		$cache->cache('cache_site');
		$cache->cache('downservers');
		$cache->cache('badword');
		$cache->cache('ipbanned');
		$cache->cache('keylink');
		$cache->cache('position');
		$cache->cache('admin_role');
		$cache->cache('urlrule');
		$cache->cache('module');
		$cache->cache('sitemodel');
		$cache->cache('workflow');
		$cache->cache('dbsource');
		$cache->cache('member_group');
		$cache->cache('membermodel');
		$cache->cache('type','search');
		$cache->cache('special');
		$cache->cache('setting');
		$cache->cache('database');
		$cache->cache('member_setting');
		$cache->cache('member_model_field');
		$cache->cache('search_setting');
		$cache->cache('attachment_remote');
		dr_json(1, '缓存更新成功......<img src="images/correct.png" /></p><p>安装完成......<img src="images/correct.png" />', ['page' => 99]);

		break;
		
	//数据库测试
	case 'dbtest':
		$data = pc_base::load_sys_class('input')->post('data');
		$data['adminpath'] = dr_clear_empty($data['adminpath']);
		if($data['adminpath'] && (is_numeric($data['adminpath']) || preg_match('/^[0-9]+$/', $data['adminpath'][0]) || !preg_match('/^[A-Za-z0-9]+$/', $data['adminpath']))) {
			dr_json(0, '后台登录口地址不能是数字开头或不能包含中文和特殊字符！');
		}
		$noname_arr = array('admin','api','caches','cms','html','login','mobile','statics','uploadfile');
		if(in_array($data['adminpath'],$noname_arr)) {
			dr_json(0, '后台登录口地址不能使用CMS默认目录名（admin，api，caches，cms，login，html，mobile，statics，uploadfile），请重新设置！');
		}
		if (is_numeric(substr($data['dbname'], 0, 1))) {
			dr_json(0, '数据库名称（'.$data['dbname'].'）不能是数字开头');
		} elseif (strpos($data['dbname'], '.') !== false) {
			dr_json(0, '数据库名称（'.$data['dbname'].'）不能存在.号');
		}
		$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
		if (!$mysqli) {
			dr_json(0, 'PHP环境必须启用Mysqli扩展！');
		}
		$conn = mysqli_connect($data['dbhost'], $data['dbuser'], $data['dbpw'], null, $data['dbport']);
		if (!$conn) {
			dr_json(0, '['.mysqli_connect_error().'] - 无法连接到数据库服务器（'.$data['dbhost'].'），请检查端口（'.$data['dbport'].'）和用户名（'.$data['dbuser'].'）和密码（'.$data['dbpw'].'）是否正确！');
		} elseif (mysqli_get_server_version($conn) < 50500) {
			dr_json(0, '数据库版本低于Mysql 5.5，无法安装CMS，请升级数据库版本！');
		} elseif (!mysqli_set_charset($conn, 'utf8mb4')) {
			dr_json(0, '当前MySQL不支持utf8mb4编码（'.mysqli_error($conn).'）建议升级MySQL版本！');
		} elseif (!mysqli_query($conn, 'CREATE DATABASE IF NOT EXISTS `'.$data['dbname'].'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
			dr_json(0, '指定的数据库（'.$data['dbname'].'）不存在，系统尝试创建失败，请先通过其他方式建立好数据库！');
		}
		$data['tablepre'] = dr_safe_filename(strtolower($data['tablepre']));
		// 存储缓存文件中
		$size = file_put_contents(CACHE_PATH.'install.info', dr_array2string($data));
		if (!$size || $size < 10) {
			dr_json(0, '临时数据存储失败，caches目录无法写入');
		}
		$sys_config = array('cookie_pre'=>token().'_',
			'auth_key'=>token($data['name']),
			'web_path'=>$rootpath,
			'js_path'=>FC_NOW_HOST.substr($rootpath, 1).'statics/js/',
			'css_path'=>FC_NOW_HOST.substr($rootpath, 1).'statics/css/',
			'img_path'=>FC_NOW_HOST.substr($rootpath, 1).'statics/images/',
			'mobile_js_path'=>FC_NOW_HOST.substr($rootpath, 1).'mobile/statics/js/',
			'mobile_css_path'=>FC_NOW_HOST.substr($rootpath, 1).'mobile/statics/css/',
			'mobile_img_path'=>FC_NOW_HOST.substr($rootpath, 1).'mobile/statics/images/',
			'app_path'=>FC_NOW_HOST.substr($rootpath, 1),
			'mobile_path'=>FC_NOW_HOST.substr($rootpath, 1).'mobile/',
			'needcheckcomeurl'=>(HTTP_REFERER ? 1 : 0),
		);
		$db_config = array('hostname'=>$data['dbhost'],
			'port'=>$data['dbport'],
			'username'=>$data['dbuser'],
			'password'=>$data['dbpw'],
			'database'=>$data['dbname'],
			'tablepre'=>$data['tablepre'],
			'pconnect'=>$data['pconnect'],
			'charset'=>$data['dbcharset'],
		);
		set_config($sys_config,'system');
		set_config($db_config,'database');
		if ($data['adminpath']) {
			//设置后台登录地址
			if(pc_base::load_config('system','admin_login_path')) {
				//建立自定义后台登录目录
				dr_mkdirs(CMS_PATH.$data['adminpath']);
				$admin = file_get_contents(CMS_PATH.pc_base::load_config('system','admin_login_path').'/index.php');
				file_put_contents(CMS_PATH.$data['adminpath'].'/index.php',$admin);
				//删除原后台登录地址
				dr_dir_delete(CMS_PATH.pc_base::load_config('system','admin_login_path'), TRUE);
				$index = file_get_contents(PC_PATH.'modules/admin/index.php');
				$index = str_replace('public function '.pc_base::load_config('system','admin_login_path').'()','public function '.$data['adminpath'].'()',$index);
				$index = str_replace('m=admin&c=index&a='.pc_base::load_config('system','admin_login_path'),'m=admin&c=index&a='.$data['adminpath'],$index);
				file_put_contents(PC_PATH.'modules/admin/index.php',$index);
			} else {
				//建立自定义后台登录目录
				dr_mkdirs(CMS_PATH.$data['adminpath']);
				$admin = file_get_contents(CMS_PATH.'admin.php');
				$admin = str_replace('index.php','../index.php',$admin);
				file_put_contents(CMS_PATH.$data['adminpath'].'/index.php',$admin);
				//删除原后台登录地址
				@unlink(CMS_PATH.'admin.php');
				$index = file_get_contents(PC_PATH.'modules/admin/index.php');
				$index = str_replace('public function login()','public function '.$data['adminpath'].'()',$index);
				$index = str_replace('m=admin&c=index&a=login','m=admin&c=index&a='.$data['adminpath'],$index);
				file_put_contents(PC_PATH.'modules/admin/index.php',$index);
			}
			$sys_config = array('admin_login_path'=>$data['adminpath'],);
			set_config($sys_config,'system');
		}
		$tables = array();
		$query = mysqli_query($conn, 'SHOW TABLES FROM `'.$data['dbname'].'`');
		while($r = mysqli_fetch_row($query)) {
			$tables[] = $r[0];
		}
		if($tables && in_array($data['tablepre'].'admin', $tables) || in_array($data['tablepre'].'module', $tables)) {
			dr_json(2, '您已经安装过CMS，系统会自动删除老数据！是否继续？');
		} else {
			dr_json(1, '成功');
		}
		break;
		
	//数据库删除
	case 'dbdel':
		$data = pc_base::load_sys_class('input')->post('data');
		if (is_numeric(substr($data['dbname'], 0, 1))) {
			dr_json(0, '数据库名称（'.$data['dbname'].'）不能是数字开头');
		} elseif (strpos($data['dbname'], '.') !== false) {
			dr_json(0, '数据库名称（'.$data['dbname'].'）不能存在.号');
		}
		$mysqli = function_exists('mysqli_init') ? mysqli_init() : 0;
		if (!$mysqli) {
			dr_json(0, 'PHP环境必须启用Mysqli扩展！');
		}
		$conn = mysqli_connect($data['dbhost'], $data['dbuser'], $data['dbpw'], null, $data['dbport']);
		if (!$conn) {
			dr_json(0, '['.mysqli_connect_error().'] - 无法连接到数据库服务器（'.$data['dbhost'].'），请检查端口（'.$data['dbport'].'）和用户名（'.$data['dbuser'].'）和密码（'.$data['dbpw'].'）是否正确！');
		} elseif (mysqli_get_server_version($conn) < 50500) {
			dr_json(0, '数据库版本低于Mysql 5.5，无法安装CMS，请升级数据库版本！');
		} elseif (!mysqli_set_charset($conn, 'utf8mb4')) {
			dr_json(0, '当前MySQL不支持utf8mb4编码（'.mysqli_error($conn).'）建议升级MySQL版本！');
		} elseif (!mysqli_query($conn, 'DROP DATABASE IF EXISTS `'.$data['dbname'].'`')) {
			dr_json(0, '指定的数据库（'.$data['dbname'].'）删除失败，请手动删除数据库（'.$data['dbname'].'）或者你可以尝试修改数据库名或者数据表前缀！');
		} elseif (!mysqli_query($conn, 'CREATE DATABASE IF NOT EXISTS `'.$data['dbname'].'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci')) {
			dr_json(0, '指定的数据库（'.$data['dbname'].'）不存在，系统尝试创建失败，请先通过其他方式建立好数据库！');
		}
		dr_json(1, '成功');
		break;
		
	case 'alpha':
		dr_json(1, build('alpha'));
		break;

}

function format_textarea($string) {
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string,ENT_COMPAT,'utf-8')));
}

// 数据分组
function query_rows($sql, $num = 0) {

	if (!$sql) {
		return '';
	}

	$rt = [];
	$sql = format_create_sql($sql);
	$sql_data = explode(';SQL_CMS_EOL', trim(str_replace(array(PHP_EOL, chr(13), chr(10)), 'SQL_CMS_EOL', $sql)));

	foreach($sql_data as $query){
		if (!$query) {
			continue;
		}
		$ret = '';
		$queries = explode('SQL_CMS_EOL', trim($query));
		foreach($queries as $query) {
			$ret.= $query[0] == '#' || $query[0].$query[1] == '--' ? '' : $query;
		}
		if (!$ret) {
			continue;
		}
		$rt[] = $ret;
	}

	return $num ? array_chunk($rt, $num) : $rt;
}

function _sql_execute($mysqli,$sql,$r_tablepre = '',$s_tablepre = 'cms_') {
	$sqls = _sql_split($mysqli,$sql,$r_tablepre,$s_tablepre);
	if(is_array($sqls)){
		foreach($sqls as $sql){
			if(trim($sql) != ''){
				mysqli_query($mysqli,$sql);
			}
		}
	}else{
		mysqli_query($mysqli,$sqls);
	}
	return true;
}

function _sql_split($mysqli,$sql,$r_tablepre = '',$s_tablepre='cms_') {
	global $dbcharset,$tablepre;
	$r_tablepre = $r_tablepre ? $r_tablepre : $tablepre;
	$sql = str_replace('phpcms_', $s_tablepre, $sql);
	if($r_tablepre != $s_tablepre) $sql = str_replace($s_tablepre, $r_tablepre, $sql);
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query){
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query){
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}

function set_config($config,$cfgfile) {
	if(!$config || !$cfgfile) return false;
	$configfile = CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.$cfgfile.'.php';
	if(!is_writable($configfile)) dr_json(0, '文件['.$configfile.']无法写入，请修改权限！');
	$pattern = $replacement = array();
	foreach($config as $k=>$v) {
		$v = trim($v);
		$configs[$k] = $v;
		$pattern[$k] = "/'".$k."'\s*=>\s*([']?)[^']*([']?)(\s*),/is";
		$replacement[$k] = "'".$k."' => \${1}".$v."\${2}\${3},";
	}
	$str = file_get_contents($configfile);
	$str = preg_replace($pattern, $replacement, $str);
	return file_put_contents($configfile, $str);
}
?>