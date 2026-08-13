<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_func('global','admin');
if(param::get_cookie('sys_lang')) {
	define('SYS_STYLE',param::get_cookie('sys_lang'));
} else {
	define('SYS_STYLE','zh-cn');
}
class admin {
	public $userid;
	public $username;
	
	public function __construct() {
		self::check_url();
		self::check_admin();
		self::check_priv();
		self::manage_log();
		self::check_ip();
		self::lock_screen();
		self::check_hash();
	}
	
	/**
	 * 判断用户是否已经登录
	 */
	final public function check_admin() {
		if(ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array(SYS_ADMIN_PATH, 'sms', 'fclient', 'public_logout'))) {
			return true;
		} else {
			$cache = pc_base::load_sys_class('cache');
			$config = getcache('common','commons');
			$admin_db = pc_base::load_model('admin_model');
			$userid = param::get_session('userid');
			$login_attr = param::get_session('login_attr');
			$user = $admin_db->get_one(array('userid'=>$userid));
			if ($user && param::get_session('roleid')!=$user['roleid']) {
				param::set_session('roleid', $user['roleid']);
			}
			if ($user && $login_attr!=md5(SYS_KEY.$user['password'].(isset($user['login_attr']) ? $user['login_attr'] : ''))) {
				if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
					$cache->del_auth_data('admin_option_'.param::get_session('userid'), 1);
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
				dr_redirect('?m=admin&c=index&a='.SYS_ADMIN_PATH);
			}
			if(!param::get_session('userid') || !param::get_session('roleid') || $userid != param::get_session('userid')) dr_admin_msg(0,L('admin_login'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
		}
	}

	/**
	 * 加载后台模板
	 * @param string $file 文件名
	 * @param string $m 模型名
	 */
	final public static function admin_tpl($file, $m = '') {
		return admin_template($file, $m);
	}
	
	/**
	 * 按父ID查找菜单子项
	 * @param integer $parentid   父菜单ID
	 * @param integer $with_self  是否包括他自己
	 */
	final public static function admin_menu($parentid, $with_self = 0) {
		$parentid = intval($parentid);
		$menudb = pc_base::load_model('menu_model');
		$result = $menudb->select(array('parentid'=>$parentid,'display'=>1),'*',1000,'listorder ASC,id ASC');
		if($with_self) {
			$result2[] = $menudb->get_one(array('id'=>$parentid));
			$result = array_merge($result2,$result);
		}
		//权限检查
		if(cleck_admin(param::get_session('roleid'))) return $result;
		$array = array();
		$privdb = pc_base::load_model('admin_role_priv_model');
		$siteid = param::get_cookie('siteid');
		foreach($result as $v) {
			$action = $v['a'];
			if(preg_match('/^public_/',$action)) {
				$array[] = $v;
			} else {
				if(preg_match('/^ajax_([a-z]+)_/',$action,$_match)) $action = $_match[1];
				if ($v['id']<=290) {
					$r = $privdb->get_one(array('menuid'=>$v['id'],'m'=>$v['m'],'c'=>$v['c'],'a'=>$action,'roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
				} else {
					$r = $privdb->get_one(array('m'=>$v['m'],'c'=>$v['c'],'a'=>$action,'roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
				}
				if($r) $array[] = $v;
			}
		}
		return $array;
	}
	/**
	 * 获取菜单 头部菜单导航
	 * 
	 * @param $parentid 菜单id
	 */
	final public static function submenu($parentid = '', $big_menu = false) {
		$input = pc_base::load_sys_class('input');
		$s = $input->get('s');
		if(empty($parentid)) {
			$menudb = pc_base::load_model('menu_model');
			$r = $menudb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>ROUTE_A));
			$parentid = isset($r['id']) ? $r['id'] : $input->get('menuid');
			$result = $menudb->count(array('parentid'=>$parentid,'display'=>1));
			if(empty($result)) {
				$parentid = isset($r['parentid']) ? $r['parentid'] : $input->get('menuid');
			}
		}
		$array = self::admin_menu($parentid,1);
		
		$numbers = dr_count($array);
		if($numbers==1 && !$big_menu) return '';
		$string = '';
		$pc_hash = dr_get_csrf_token();
		foreach($array as $_value) {
			if (isset($s)) {
				$_s = !empty($_value['data']) ? str_replace('=', '', strstr($_value['data'], '=')) : '';
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == str_replace(array('add:', 'ajax:', 'ajax_', 'blank:', 'show:', 'help:', 'js:', 'hide:', 'url:'), '', $_value['a']) && $input->get('s') == $_s ? ' on' : '';
			} else {
				$classname = ROUTE_M == $_value['m'] && ROUTE_C == $_value['c'] && ROUTE_A == str_replace(array('add:', 'ajax:', 'ajax_', 'blank:', 'show:', 'help:', 'js:', 'hide:', 'url:'), '', $_value['a']) ? ' on' : '';
			}
			if (isset($_value['data']) && $_value['data']) {
				if (strstr($_value['data'], '&') && substr($_value['data'], 0, 1)=='&') {
					$_valuedata = $_value['data'];
				} else {
					$_valuedata = '&'.$_value['data'];
				}
			} else {
				$_valuedata = '';
			}
			if($_value['parentid'] == 0 || $_value['m']=='') continue;
			// 获取URL
			$uri = $_value['a'];
			$_li_class = '';
			if (strpos($uri, 'ajax:') === 0 || strpos($uri, 'ajax_') === 0) {
				$url = 'javascript:dr_admin_menu_ajax(\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.substr($uri, 5).$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash.'\');';
			} elseif (strpos($uri, 'blank:') === 0) {
				$url = '?m='.$_value['m'].'&c='.$_value['c'].'&a='.substr($uri, 6).$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash.'" target="_blank';
			} elseif (strpos($uri, 'add:') === 0) {
				list($a, $b, $c, $d) = explode(',', $uri);
				$w = isset($b) ? (is_numeric($b) ? $b : '\''.$b.'\'') : '';
				$h = isset($c) ? (is_numeric($c) ? $c : '\''.$c.'\'') : '';
				$url = 'javascript:dr_iframe(\''.L($_value['name']).'\', \'' . '?m='.$_value['m'].'&c='.$_value['c'].'&a='.substr($a, 4).$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash . '\', ' . $w . ', ' . $h . ''.($d ? ', \'' . $d . '\'' : '').');';
			} elseif (strpos($uri, 'show:') === 0) {
				list($a, $b, $c, $d) = explode(',', $uri);
				$w = isset($b) ? (is_numeric($b) ? $b : '\''.$b.'\'') : '';
				$h = isset($c) ? (is_numeric($c) ? $c : '\''.$c.'\'') : '';
				$url = 'javascript:dr_iframe_show(\''.L($_value['name']).'\', \'' . '?m='.$_value['m'].'&c='.$_value['c'].'&a='.substr($a, 5).$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash . '\', ' . $w . ', ' . $h . ''.($d ? ', \'' . $d . '\'' : '').');';
			} elseif (strpos($uri, 'help:') === 0) {
				if (CI_DEBUG) {
					$url = 'javascript:dr_help(\''.'?m='.$_value['m'].'&c='.$_value['c'].'&a='.substr($uri, 5).$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash.'\');';
				} else {
					continue;
				}
			} elseif (strpos($uri, 'js:') === 0) {
				$url = 'javascript:'.substr($uri, 3).'();';
			} elseif (strpos($uri, 'hide:') === 0) {
				$url = dr_now_url();
				$_li_class = substr($uri, 5) == ROUTE_A ? '' : '{HIDE}';
			} elseif (strpos($uri, 'url:') === 0) {
				$url = substr($uri, 4);
				if (!$url) {
					continue;
				}
			} else {
				$url = '?m='.$_value['m'].'&c='.$_value['c'].'&a='.$uri.$_valuedata.'&menuid='.$parentid.'&pc_hash='.$pc_hash;
			}
			if(is_mobile()) {
				$string .= "<li".($_li_class ? " class=\"" . $_li_class . "\"" : "")."><a href=\"".$url."\" class=\"tooltips".$classname."\" data-container=\"body\" data-placement=\"bottom\" data-original-title=\"".L($_value['name'])."\"><i class=\"".$_value['icon']."\"></i> ".L($_value['name'])."</a></li><div class=\"dropdown-line\"></div>";
			} else {
				if($_li_class) {
					$string .= "<span class=\"" . $_li_class . "\"><a href=\"".$url."\" class=\"tooltips".$classname."\" data-container=\"body\" data-placement=\"bottom\" data-original-title=\"".L($_value['name'])."\"><i class=\"".$_value['icon']."\"></i> ".L($_value['name'])."</a><i class=\"fa fa-circle\"></i></span>";
				} else {
					$string .= "<a href=\"".$url."\" class=\"tooltips".$classname."\" data-container=\"body\" data-placement=\"bottom\" data-original-title=\"".L($_value['name'])."\"><i class=\"".$_value['icon']."\"></i> ".L($_value['name'])."</a><i class=\"fa fa-circle\"></i>";
				}
			}
		}
		return str_replace('{HIDE}', 'hidden', $string);
	}
	final public static function child_menu($parentid, $self = 0) {
		$datas = self::admin_menu($parentid);
		if($datas) {
			$i = 0;
			$child = ',"child": [';
			foreach($datas as $value) {
				if (isset($value['data']) && $value['data']) {
					if (strstr($value['data'], '&') && substr($value['data'], 0, 1)=='&') {
						$valuedata = $value['data'];
					} else {
						$valuedata = '&'.$value['data'];
					}
				} else {
					$valuedata = '';
				}
				if ($self==1) {
					if ($i==0) {
						$child .= '{"id": "'.$value['id'].'","title": "'.L($value['name']).'","href": "","icon": "'.$value['icon'].'","target": "_self"'.self::child_menu($value['id']).'}';
					} else {
						$child .= ',{"id": "'.$value['id'].'","title": "'.L($value['name']).'","href": "","icon": "'.$value['icon'].'","target": "_self"'.self::child_menu($value['id']).'}';
					}
				} else {
					// 获取URL
					$uri = $value['a'];
					if (strpos($uri, 'ajax:') === 0 || strpos($uri, 'ajax_') === 0 || strpos($uri, 'hide:') === 0 || strpos($uri, 'help:') === 0) {
						$url = substr($uri, 5);
					} elseif (strpos($uri, 'blank:') === 0) {
						$url = substr($uri, 6);
					} elseif (strpos($uri, 'add:') === 0) {
						list($a, $b, $c) = explode(',', $uri);
						$url = substr($a, 4);
					} elseif (strpos($uri, 'show:') === 0) {
						list($a, $b, $c) = explode(',', $uri);
						$url = substr($a, 5);
					} elseif (strpos($uri, 'url:') === 0) {
						$url = substr($uri, 4);
					} elseif (strpos($uri, 'js:') === 0) {
						$url = substr($uri, 3);
					} else {
						$url = $uri;
					}
					if ($i==0) {
						$child .= '{"id": "'.$value['id'].'","title": "'.L($value['name']).'","href": "?m='.$value['m'].'&c='.$value['c'].'&a='.$url.$valuedata.'&menuid='.$value['id'].'&pc_hash='.dr_get_csrf_token().'","icon": "'.$value['icon'].'","target": "_self"}';
					} else {
						$child .= ',{"id": "'.$value['id'].'","title": "'.L($value['name']).'","href": "?m='.$value['m'].'&c='.$value['c'].'&a='.$url.$valuedata.'&menuid='.$value['id'].'&pc_hash='.dr_get_csrf_token().'","icon": "'.$value['icon'].'","target": "_self"}';
					}
				}
				$i ++;
			}
			$child .= ']';
		}
		return $child;
	}
	/**
	 * 当前位置
	 * 
	 * @param $id 菜单id
	 */
	final public static function current_pos($id) {
		$menudb = pc_base::load_model('menu_model');
		$r =$menudb->get_one(array('id'=>$id),'id,name,parentid');
		$str = '';
		if($r['parentid']) {
			$str = self::current_pos($r['parentid']);
		}
		return $str.L($r['name']).' > ';
	}
	
	/**
	 * 获取当前的站点ID
	 */
	final public static function get_siteid() {
		return get_siteid();
	}
	
	/**
	 * 获取当前站点信息
	 * @param integer $siteid 站点ID号，为空时取当前站点的信息
	 * @return array
	 */
	final public static function get_siteinfo($siteid = '') {
		if ($siteid == '') $siteid = self::get_siteid();
		if (empty($siteid)) return false;
		$sites = pc_base::load_app_class('sites', 'admin');
		return $sites->get_by_id($siteid);
	}
	
	final public static function return_siteid() {
		$sites = pc_base::load_app_class('sites', 'admin');
		$siteid = explode(',',$sites->get_role_siteid(param::get_session('roleid')));
		return current($siteid);
	}
	/**
	 * 权限判断
	 */
	final public function check_priv() {
		if(ROUTE_M =='admin' && ROUTE_C =='index' && in_array(ROUTE_A, array(SYS_ADMIN_PATH, 'sms', 'init', 'fclient'))) return true;
		if(cleck_admin(param::get_session('roleid'))) return true;
		$siteid = param::get_cookie('siteid');
		$action = ROUTE_A;
		$privdb = pc_base::load_model('admin_role_priv_model');
		if(preg_match('/^public_/',ROUTE_A)) return true;
		if(preg_match('/^ajax_([a-z]+)_/',ROUTE_A,$_match)) {
			$action = $_match[1];
		}
		$r = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>$action,'roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$ajax = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%ajax:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$ajaxs = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%ajax_'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$blank = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%blank:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$add = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%add:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$show = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%show:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$url = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%url:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$js = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%js:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$hide = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%hide:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		$help = $privdb->get_one(array('m'=>ROUTE_M,'c'=>ROUTE_C,'a'=>'%help:'.$action.'%','roleid'=>is_array(dr_string2array(param::get_session('roleid'))) ? dr_string2array(param::get_session('roleid')) : param::get_session('roleid'),'siteid'=>$siteid));
		if(!$r && !$ajax && !$ajaxs && !$blank && !$add && !$show && !$url && !$js && !$hide && !$help) dr_admin_msg(0,L('您没有权限操作该项'));
	}

	/**
	 * 
	 * 记录日志 
	 */
	private function manage_log() {
		//判断是否记录
 		if(SYS_ADMIN_LOG){
 			$action = ROUTE_A;
 			if($action == '' || strchr($action,'public') || $action == 'init' || $action=='public_current_pos') {
				return false;
			}else {
				$ip = ip();
				$log = pc_base::load_model('log_model');
				$username = param::get_cookie('admin_username');
				$userid = param::get_session('userid');
				$time = date('Y-m-d H-i-s',SYS_TIME);
				$url = '?m='.ROUTE_M.'&c='.ROUTE_C.'&a='.ROUTE_A;
				$log->insert(array('module'=>ROUTE_M,'username'=>$username,'userid'=>$userid,'action'=>ROUTE_C, 'querystring'=>$url,'time'=>$time,'ip'=>$ip));
			}
		}
	}
	
	/**
	 * 
	 * 后台IP禁止判断 ...
	 */
	private function check_ip(){
		$ipbanned = pc_base::load_model('ipbanned_model');
		$ipbanned->check_ip();
 	}
 	/**
 	 * 检查锁屏状态
 	 */
	private function lock_screen() {
		if(param::get_session('lock_screen')==1) {
			if(preg_match('/^public_/', ROUTE_A) || (ROUTE_M == 'content' && ROUTE_C == 'create_html') || (ROUTE_M == 'release') || (ROUTE_A == SYS_ADMIN_PATH) || (ROUTE_M == 'search' && ROUTE_C == 'search_admin' && ROUTE_A=='createindex')) return true;
			$cache = pc_base::load_sys_class('cache');
			$config = getcache('common','commons');
			if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
				$cache->del_auth_data('admin_option_'.param::get_session('userid'), 1);
			}
			param::del_session('userid');
			param::del_session('login_attr');
			param::del_session('roleid');
			param::del_session(COOKIE_PRE.ip().'pc_hash');
			param::set_cookie('admin_username','');
			param::set_cookie('userid',0);
			param::set_cookie('login_attr', '');
			dr_admin_msg(0,L('admin_login'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
		}
	}

	/**
 	 * 检查hash值，验证用户数据安全性
 	 */
	private function check_hash() {
		$input = pc_base::load_sys_class('input');
		if(preg_match('/^public_/', ROUTE_A) || ROUTE_M =='admin' && ROUTE_C =='index' || in_array(ROUTE_A, array(SYS_ADMIN_PATH))) {
			return true;
		}
		if($input->get('pc_hash') && dr_get_csrf_token() != '' && (dr_get_csrf_token() == $input->get('pc_hash'))) {
			return true;
		} elseif($input->post('pc_hash') && dr_get_csrf_token() != '' && (dr_get_csrf_token() == $input->post('pc_hash'))) {
			return true;
		} else {
			dr_admin_msg(0,L('hash_check_false'),HTTP_REFERER);
		}
	}

	/**
	 * 后台信息列表模板
	 * @param string $id 被选中的模板名称
	 * @param string $str form表单中的属性名
	 */
	final public function admin_list_template($id = '', $str = '') {
		$templatedir = PC_PATH.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
		$pre = 'content_list';
		$templates = glob($templatedir.$pre.'*.tpl.php');
		if(empty($templates)) return false;
		$files = @array_map('basename', $templates);
		$templates = array();
		if(is_array($files)) {
			foreach($files as $file) {
				$key = substr($file, 0, -8);
				$templates[$key] = $file;
			}
		}
		ksort($templates);
		return form::select($templates, $id, $str,L('please_select'));
	}
	
	// 验证操作其他用户身份权限
	public function cleck_edit_member($uid) {

		if (ADMIN_FOUNDERS && !dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {
			// 此账号属于管理账号，禁止操作
			return false;
		}

		return true;
	}
	
	/**
	 * 是否需要检查外部访问
	 */
	final public function check_url() {
		if (defined('NeedCheckComeUrl') && !NeedCheckComeUrl) {
			return true;
		}
		if(ROUTE_M =='admin' && ROUTE_C == 'index' && in_array(ROUTE_A, array(SYS_ADMIN_PATH, 'init', 'fclient'))) {
			return true;
		}
		if (!HTTP_REFERER) {
			CI_DEBUG && log_message('error', '直接地址输入访问后台: （'.FC_NOW_URL.'）');
			exit('<!DOCTYPE html><html lang="zh-cn"><head><meta charset="utf-8"><title>'.L('提示信息').'</title><style>        div.logo {            height: 200px;            width: 155px;            display: inline-block;            opacity: 0.08;            position: absolute;            top: 2rem;            left: 50%;            margin-left: -73px;        }        body {            height: 100%;            background: #fafafa;            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;            color: #777;            font-weight: 300;        }        h1 {            font-weight: lighter;            letter-spacing: 0.8;            font-size: 3rem;            margin-top: 0;            margin-bottom: 0;            color: #222;        }        .wrap {            max-width: 1024px;            margin: 5rem auto;            padding: 2rem;            background: #fff;            text-align: center;            border: 1px solid #efefef;            border-radius: 0.5rem;            position: relative;            word-wrap:break-word;            word-break:normal;        }        pre {            white-space: normal;            margin-top: 1.5rem;        }        code {            background: #fafafa;            border: 1px solid #efefef;            padding: 0.5rem 1rem;            border-radius: 5px;            display: block;        }        p {            margin-top: 1.5rem;        }        .footer {            margin-top: 2rem;            border-top: 1px solid #efefef;            padding: 1em 2em 0 2em;            font-size: 85%;            color: #999;        }        a:active,        a:link,        a:visited {            color: #dd4814;        }</style></head><body><div class="wrap"><p><font color="red">'.L('对不起，为了系统安全，不允许直接输入地址访问本系统的后台管理页面。').'</font></p></div></body></html>');
		} else {
			$curl = trim(trim(FC_NOW_HOST, '/'));
			if (strtolower(substr(HTTP_REFERER, 0, strlen($curl))) != strtolower($curl)) {
				CI_DEBUG && log_message('error', '外部链接: （'.HTTP_REFERER.'）访问后台: （'.FC_NOW_URL.'）');
				exit('<!DOCTYPE html><html lang="zh-cn"><head><meta charset="utf-8"><title>'.L('提示信息').'</title><style>        div.logo {            height: 200px;            width: 155px;            display: inline-block;            opacity: 0.08;            position: absolute;            top: 2rem;            left: 50%;            margin-left: -73px;        }        body {            height: 100%;            background: #fafafa;            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;            color: #777;            font-weight: 300;        }        h1 {            font-weight: lighter;            letter-spacing: 0.8;            font-size: 3rem;            margin-top: 0;            margin-bottom: 0;            color: #222;        }        .wrap {            max-width: 1024px;            margin: 5rem auto;            padding: 2rem;            background: #fff;            text-align: center;            border: 1px solid #efefef;            border-radius: 0.5rem;            position: relative;            word-wrap:break-word;            word-break:normal;        }        pre {            white-space: normal;            margin-top: 1.5rem;        }        code {            background: #fafafa;            border: 1px solid #efefef;            padding: 0.5rem 1rem;            border-radius: 5px;            display: block;        }        p {            margin-top: 1.5rem;        }        .footer {            margin-top: 2rem;            border-top: 1px solid #efefef;            padding: 1em 2em 0 2em;            font-size: 85%;            color: #999;        }        a:active,        a:link,        a:visited {            color: #dd4814;        }</style></head><body><div class="wrap"><p><font color="red">'.L('对不起，为了系统安全，不允许从外部链接地址访问本系统的后台管理页面。').'</font></p></div></body></html>');
			}
		}
	}
}