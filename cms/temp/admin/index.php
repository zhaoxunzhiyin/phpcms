<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);

class index extends admin {
	public function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		pc_base::load_sys_class('form');
		$this->db = pc_base::load_model('admin_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->panel_db = pc_base::load_model('admin_panel_model');
	}
	
	public function init () {
		$siteid = param::get_cookie('siteid');
		$userid = $_SESSION['userid'];
		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		if ($_SESSION['roleid']==1) {
			$rolename = '<span style="color:#ff0000;">'.$roles[$_SESSION['roleid']].'</span>';
		} else {
			$rolename = '<span style="color:#0000ff;">'.$roles[$_SESSION['roleid']].'</span>';
		}
		$site = pc_base::load_app_class('sites');
		$sitelist = $site->get_list($_SESSION['roleid']);
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		/*管理员收藏栏*/
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), "*",20 , 'datetime');
		$site_model = param::get_cookie('site_model');
		include $this->admin_tpl('index');
	}
	
	public function login() {
		$setting = getcache('common','commons');
		$sysadmincode = isset($setting['sysadmincode']) ? (int)$setting['sysadmincode'] : '';
		$maxloginfailedtimes = isset($setting['maxloginfailedtimes']) ? (int)$setting['maxloginfailedtimes'] : '';
		$sysadminlogintimes = isset($setting['sysadminlogintimes']) ? (int)$setting['sysadminlogintimes'] : 10;
		if($this->input->get('dosubmit')) {
			$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : dr_json(0, L('nameerror'));
			if (!$sysadmincode) {
				$code = $this->input->post('code') && trim($this->input->post('code')) ? trim($this->input->post('code')) : dr_json(0, L('input_code'));
				if ($_SESSION['code'] != strtolower($code)) {
					$_SESSION['code'] = '';
					param::set_cookie('code','');
					dr_json(4, L('code_error'));
				}
				$_SESSION['code'] = '';
				param::set_cookie('code','');
			}
			if(!is_username($username)){
				dr_json(2, L('username_illegal'));
			}
			//密码错误剩余重试次数
			$this->times_db = pc_base::load_model('times_model');
			$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
			
			if ($rtime) {
				if ($maxloginfailedtimes) {
					if ($sysadminlogintimes && (int)$rtime['logintime'] && SYS_TIME - (int)$rtime['logintime'] > ($sysadminlogintimes * 60)) {
						// 超过时间了
						$this->times_db->delete(array('username'=>$username));
					}
				}
				
				if ($maxloginfailedtimes) {
					if((int)$rtime['times'] && (int)$rtime['times'] >= $maxloginfailedtimes) {
						dr_json(0, L('失败次数已达到'.$rtime['times'].'次，已被禁止登录'));
					}
				}
			}
			//查询帐号
			$r = $this->db->get_one(array('username'=>$username));
			if(!$r) dr_json(0, L('user_not_exist'));
			$password = md5(trim($this->input->post('password')).$r['encrypt']);
			
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
					dr_json(3, str_replace('{times}',$times,L('password_error')));
				} else {
					dr_json(3, L('密码错误'));
				}
			}
			$this->times_db->delete(array('username'=>$username));
			
			$this->db->update(array('lastloginip'=>ip(),'lastlogintime'=>SYS_TIME),array('userid'=>$r['userid']));
			$_SESSION['userid'] = $r['userid'];
			$_SESSION['roleid'] = $r['roleid'];
			$_SESSION['pc_hash'] = bin2hex(random_bytes(16));
			$_SESSION['lock_screen'] = 0;
			$site = pc_base::load_app_class('sites');
			$sitelist = $site->get_list_login($_SESSION['roleid']);
			$default_siteid = self::return_siteid_login();
			$cookie_time = SYS_TIME+86400*30;
			if(!$r['lang']) $r['lang'] = 'zh-cn';
			param::set_cookie('admin_username',$username,$cookie_time);
			param::set_cookie('siteid', $default_siteid,$cookie_time);
			param::set_cookie('userid', $r['userid'],$cookie_time);
			param::set_cookie('admin_email', $r['email'],$cookie_time);
			param::set_cookie('sys_lang', $r['lang'],$cookie_time);
			dr_json(1, L('login_success'), array('url' => '?m=admin&c=index&pc_hash='.$_SESSION['pc_hash']));
		} else {
			pc_base::load_sys_class('form', '', 0);
			include $this->admin_tpl('login');
		}
	}
	
	// 子站客户端自动登录
	public function fclient() {

		if (!is_file(CMS_PATH.'api/fclient/sync.php')) {
			showmessage(L('fclient_not_exist'));
		}

		$sync = require CMS_PATH.'api/fclient/sync.php';
		if (!$this->input->get('id') || !$this->input->get('sync')) {
			showmessage(L('fclient_not_sn'));
		} elseif ($this->input->get('id') != md5($sync['id'])) {
			showmessage(L('fclient_not_id'));
		} elseif ($this->input->get('sync') != $sync['sn']) {
			showmessage(L('fclient_sn_exist'));
		}

		$this->role_db = pc_base::load_model('admin_role_model');
		$role = $this->role_db->get_one(array('disabled'=>0),'roleid','roleid asc');
		$member = $this->db->get_one(array('roleid'=>$role['roleid']),'*','roleid asc');
		if (!$member) {
			showmessage(L('fclient_user_not_role'));
		}

		$_SESSION['userid'] = $member['userid'];
		$_SESSION['roleid'] = $member['roleid'];
		$_SESSION['pc_hash'] = bin2hex(random_bytes(16));
		$_SESSION['lock_screen'] = 0;
		$default_siteid = self::return_siteid();
		$cookie_time = SYS_TIME+86400*30;
		if(!$member['lang']) $member['lang'] = 'zh-cn';
		param::set_cookie('admin_username',$member['username'],$cookie_time);
		param::set_cookie('siteid', $default_siteid,$cookie_time);
		param::set_cookie('userid', $member['userid'],$cookie_time);
		param::set_cookie('admin_email', $member['email'],$cookie_time);
		param::set_cookie('sys_lang', $member['lang'],$cookie_time);

		showmessage(L('fclient_sn_succ'),'?m=admin&c=index');
	}
	
	public function public_logout() {
		$_SESSION['userid'] = 0;
		$_SESSION['roleid'] = 0;
		param::set_cookie('admin_username','');
		param::set_cookie('userid',0);
		
		showmessage(L('logout_success'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
	}
	
	//左侧菜单
	public function public_menu_left() {
		$menuid = intval($this->input->get('menuid'));
		$datas = admin::admin_menu($menuid);
		if ($this->input->get('parentid') && $parentid = intval($this->input->get('parentid')) ? intval($this->input->get('parentid')) : 10) {
			foreach($datas as $_value) {
	        	if($parentid==$_value['id']) {
	        		echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        		
	        	} else {
	        		echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\'?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
	        	}      	
	        }
		} else {
			include $this->admin_tpl('left');
		}
		
	}
	public function public_menu() {
		$currentsite = $this->get_siteinfo(param::get_cookie('siteid'));
		//$logoInfo['href'] = $currentsite['domain'];
		$array = admin::admin_menu(0);
		$app = pc_base::load_config('version');
		if ($app['update'] || !is_file(CACHE_PATH.'configs/version.php')) {
			$menu_home = '?m=admin&c=check&a=init&pc_hash='.$_SESSION['pc_hash'];
		} else {
			$menu_home = '?m=admin&c=index&a=public_main';
		}
		$menu = '{"homeInfo": {"title": "首页","href": "'.$menu_home.'"},"logoInfo": {"title": "后台管理系统","image": "","icon": "fa fa-cog","href": "","target": "_self"},"menuInfo": [';
		$valuedata = '';
        foreach($array as $_value) {
			if ($_value['data']) {
				if (strstr($value['data'], '&') && substr($value['data'], 0, 1)=='&') {
					$valuedata = $value['data'];
				} else {
					$valuedata = '&'.$value['data'];
				}
			}
        	if($_value['id']==10) {
        		$menu .= '{"id": "'.$_value['id'].'","title": "'.L($_value['name']).'","icon": "'.$_value['icon'].'","href": "?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].$valuedata.'&menuid='.$_value['id'].'&pc_hash='.$_SESSION['pc_hash'].'","target": "_self"'.admin::child_menu($_value['id'], 1).'}';
        	} else {
        		$menu .= ',{"id": "'.$_value['id'].'","title": "'.L($_value['name']).'","icon": "'.$_value['icon'].'","href": "?m='.$_value['m'].'&c='.$_value['c'].'&a='.$_value['a'].$valuedata.'&menuid='.$_value['id'].'&pc_hash='.$_SESSION['pc_hash'].'","target": "_self"'.admin::child_menu($_value['id'], 1).'}';
        	}
        }
		$menu .= ']}';
		echo $menu;
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
		$tablename = $this->panel_db->db_tablepre.'admin_panel';
		if (!$this->panel_db->field_exists('icon')) {
			$this->panel_db->query('ALTER TABLE `'.$tablename.'` ADD `icon` varchar(255) NULL DEFAULT NULL COMMENT \'图标标示\' AFTER `name`');
		}
		$menuid = $this->input->post('menuid') ? $this->input->post('menuid') : exit('0');
		$menuarr = $this->menu_db->get_one(array('id'=>$menuid));
		if ($menuarr['data']) {
			$menudata = $menuarr['data'];
		}
		$url = '?m='.$menuarr['m'].'&c='.$menuarr['c'].'&a='.$menuarr['a'].$menudata;
		$data = array('menuid'=>$menuid, 'userid'=>$_SESSION['userid'], 'name'=>$menuarr['name'], 'icon'=>$menuarr['icon'], 'url'=>$url, 'datetime'=>SYS_TIME);
		$this->panel_db->insert($data, '', 1);
		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo '<span><a href="javascript:paneladdclass(this);" layuimini-content-href="'.$v['url'].'&menuid='.$v['menuid'].'&pc_hash='.$_SESSION['pc_hash'].'" data-title="'.L($v['name']).'" data-icon="'.$v['icon'].'"><i class="'.$v['icon'].'"></i><cite>'.L($v['name']).'</cite></a><a class="panel-delete" href="javascript:delete_panel('.$v['menuid'].');"></a></span>';
		}
		exit;
	}
	
	public function public_ajax_delete_panel() {
		$menuid = $this->input->post('menuid') ? $this->input->post('menuid') : exit('0');
		$this->panel_db->delete(array('menuid'=>$menuid, 'userid'=>$_SESSION['userid']));

		$panelarr = $this->panel_db->listinfo(array('userid'=>$_SESSION['userid']), "datetime");
		foreach($panelarr as $v) {
			echo '<span><a href="javascript:paneladdclass(this);" layuimini-content-href="'.$v['url'].'&menuid='.$v['menuid'].'&pc_hash='.$_SESSION['pc_hash'].'" data-title="'.L($v['name']).'" data-icon="'.$v['icon'].'"><i class="'.$v['icon'].'"></i><cite>'.L($v['name']).'</cite></a><a class="panel-delete" href="javascript:delete_panel('.$v['menuid'].');"></a></span>';
		}
		exit;
	}
	public function public_main() {
		pc_base::load_app_func('global');
		pc_base::load_app_func('admin');
		define('PC_VERSION', pc_base::load_config('version','pc_version'));
		define('PC_RELEASE', pc_base::load_config('version','pc_release'));
		define('CMS_VERSION', pc_base::load_config('version','cms_version'));
		define('CMS_RELEASE', pc_base::load_config('version','cms_release'));

		$admin_username = param::get_cookie('admin_username');
		$roles = getcache('role','commons');
		$userid = $_SESSION['userid'];
		if ($_SESSION['roleid']==1) {
			$rolename = '<span style="color:#ff0000;">'.$roles[$_SESSION['roleid']].'</span>';
		} else {
			$rolename = '<span style="color:#0000ff;">'.$roles[$_SESSION['roleid']].'</span>';
		}
		$r = $this->db->get_one(array('userid'=>$userid));
		$logintime = $r['lastlogintime'];
		$loginip = $r['lastloginip'];
		$sysinfo = get_sysinfo();
		$sysinfo['mysqlv'] = $this->db->version();
		$show_header = $show_pc_hash = 1;
		/*检测框架目录可写性*/
		$pc_writeable = is_writable(PC_PATH.'base.php');
		$common_cache = getcache('common','commons');
		$logsize_warning = errorlog_size() > $common_cache['errorlog_size'] ? '1' : '0';
		$adminpanel = $this->panel_db->select(array('userid'=>$userid), '*',20 , 'datetime');
		$programmer = '（zhaoxunzhiyin）';
 		$designer = '找寻知音';
 		$qqgroup = '551419699';
 		$qq = '297885395';
 		$tel = '17684313488';
		ob_start();
		include $this->admin_tpl('main');
		$data = ob_get_contents();
		ob_end_clean();
		system_information($data);
	}
	public function public_icon() {
		$show_header = $show_pc_hash = 1;
		include $this->admin_tpl('icon');
	}
	public function public_error_log() {
		$show_header = $show_pc_hash = 1;
		$time = (int)strtotime($this->input->get('time'));
		!$time && $time = SYS_TIME;
		$list = array();
		$page = max(1, (int)$this->input->get('page'));
		$total = 0;
		$file = CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d',$time).'.php';
		if (is_file($file)) {
			$c = file_get_contents($file);
			$data = explode(PHP_EOL, trim(str_replace('<?php defined(\'IN_CMS\') OR exit(\'No direct script access allowed\'); ?>'.PHP_EOL.PHP_EOL, '', str_replace(array(chr(13), chr(10)), PHP_EOL, $c)), PHP_EOL));
			$data && $data = array_reverse($data);
			//$total = max(0, count($data));$total = $data ? max(0, count($data) - 1) : 0;
			$total = max(0, substr_count($c, '- '.date('Y-m-d', $time).' '));
			$limit = ($page - 1) * 10;
			$i = $j = 0;
			foreach ($data as $t) {
				if ($t && $i >= $limit && $j < 10) {
					$v = explode(' --> ', $t);
					$time2 = $v ? explode(' - ', $v[0]) : [1=>''];
					if ($time2[1]) {
						$value = array(
							'time' => $time2[1] ? $time2[1] : '',
						);
						$value['id'] = $i + 1;
						$value['message'] = str_replace([PHP_EOL, chr(13), chr(10)], ' ', htmlentities($v[1]));
						if (preg_match('/'.$value['time'].' \-\->(.*)\{main\}/sU', $c, $mt)) {
							$value['info'] = str_replace("'", '\\\'', str_replace([PHP_EOL, chr(13), chr(10)], '<br>', $mt[1]));
						}
						$value['message'] = str_replace("'", '\\\'', $value['message']);
						$list[] = $value;
						$j ++;
					}
				}
				$i ++;
			}
		}
		$time = date('Y-m-d', $time);
		$pages = pages($total, $page, 10);
		include $this->admin_tpl('error_log');
	}
	public function public_error_log_show() {
		$show_header = $show_pc_hash = 1;
		$time = (int)strtotime($this->input->get('time'));
		$file = CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d',$time).'.php';
		if (!is_file($file)) {
			showmessage(L('文件不存在：'.$file),'','','edit');
		}
		$code = file_get_contents($file);
		
		include $this->admin_tpl('error_file');
	}
	public function public_error_log_del() {
		$show_header = $show_pc_hash = 1;
		$time = (int)strtotime($this->input->get('time'));
		$file = CACHE_PATH.'caches_error/caches_data/log-'.date('Y-m-d',$time).'.php';
		if (!is_file($file)) {
			showmessage(L('文件不存在：'.$file),'?m=admin&c=index&a=public_error_log');
		}
		unlink($file);
		showmessage(L('operation_success'),'?m=admin&c=index&a=public_error_log');
	}
	public function public_error() {
		$show_header = $show_pc_hash = 1;
		$time = (int)strtotime($this->input->get('time'));
		!$time && $time = SYS_TIME;
		$list = array();
		$page = max(1, (int)$this->input->get('page'));
		$total = 0;
		$file = CACHE_PATH.'error_log.php';
		if (is_file($file)) {
			$c = file_get_contents($file);
			$data = @explode('<?php exit;?>', trim(str_replace(array(chr(13), chr(10)), PHP_EOL, $c), PHP_EOL));
			$data && $data = @array_reverse($data);
			//$total = max(0, count($data));$total = $data ? max(0, count($data) - 1) : 0;
			$total = max(0, substr_count($c, '<?php exit;?>'));
			$limit = ($page - 1) * 10;
			$i = $j = 0;
			foreach ($data as $t) {
				if ($t && $i >= $limit && $j < 10) {
					$v = @explode(' | ', $t);
					if ($v[0]) {
						$value['id'] = $i + 1;
						$value['time'] = $v[0] ? $v[0] : '';
						$value['message'] = str_replace(array(PHP_EOL, chr(13), chr(10)), ' ', htmlentities($v[2]));
						$value['message'] = str_replace("'", '\\\'', $value['message']);
						$value['info'] = str_replace(array(PHP_EOL, chr(13), chr(10)), ' ', htmlentities($v[3]));
						$value['line'] = str_replace(array(PHP_EOL, chr(13), chr(10)), ' ', htmlentities($v[4]));
						$list[] = $value;
						$j ++;
					}
				}
				$i ++;
			}
		}
		$pages = pages($total, $page, 10);
		include $this->admin_tpl('error_index');
	}
	public function public_log_show() {
		$show_header = $show_pc_hash = 1;
		$file = CACHE_PATH.'error_log.php';
		if (!is_file($file)) {
			showmessage(L('文件不存在：'.$file),'','','edit');
		}
		$code = file_get_contents($file);
		
		include $this->admin_tpl('error_file');
	}
	public function public_error_del() {
		$show_header = $show_pc_hash = 1;
		$file = CACHE_PATH.'error_log.php';
		if (!is_file($file)) {
			showmessage(L('文件不存在：'.$file),'?m=admin&c=index&a=public_error');
		}
		unlink($file);
		showmessage(L('operation_success'),'?m=admin&c=index&a=public_error');
	}
	/**
	 * 维持 session 登陆状态
	 */
	public function public_session_life() {
		$userid = $_SESSION['userid'];
		return true;
	}
	/**
	 * 锁屏
	 */
	public function public_lock_screen() {
		$_SESSION['lock_screen'] = 1;
	}
	public function public_login_screenlock() {
		if(empty($this->input->get('lock_password'))) showmessage(L('password_can_not_be_empty'));
		//密码错误剩余重试次数
		$this->times_db = pc_base::load_model('times_model');
		$username = param::get_cookie('admin_username');
		$setting = getcache('common','commons');
		$maxloginfailedtimes = isset($setting['maxloginfailedtimes']) ? (int)$setting['maxloginfailedtimes'] : '';
		$sysadminlogintimes = isset($setting['sysadminlogintimes']) ? (int)$setting['sysadminlogintimes'] : 10;
		
		$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>1));
		if ($rtime) {
			if ($maxloginfailedtimes) {
				if ($sysadminlogintimes && (int)$rtime['logintime'] && SYS_TIME - (int)$rtime['logintime'] > ($sysadminlogintimes * 60)) {
					// 超过时间了
					$this->times_db->delete(array('username'=>$username));
				}
			}
			
			if ($maxloginfailedtimes) {
				if((int)$rtime['times'] && (int)$rtime['times'] >= $maxloginfailedtimes) {
					exit('3');
				}
			}
		}
		//查询帐号
		$r = $this->db->get_one(array('userid'=>$_SESSION['userid']));
		$password = md5(md5($this->input->get('lock_password')).$r['encrypt']);
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
			exit('2|'.$times);//密码错误
		}
		$this->times_db->delete(array('username'=>$username));
		$_SESSION['lock_screen'] = 0;
		exit('1');
	}

	/**
	 * @设置网站模式 设置了模式后，后台仅出现在此模式中的菜单
	 */
	public function public_set_model() {
		$model = $this->input->get('site_model');
		if (!$model) {
			param::set_cookie('site_model','');
		} else {
			$models = pc_base::load_config('model_config');
			if (in_array($model, array_keys($models))) {
				param::set_cookie('site_model', $model);
			} else {
				param::set_cookie('site_model','');
			}
		}
		$menudb = pc_base::load_model('menu_model');
		$where = array('parentid'=>0,'display'=>1);
		if ($model) {
			$where[$model] = 1;
 		}
		$result =$menudb->select($where,'id',1000,'listorder ASC');
		$menuids = array();
		if (is_array($result)) {
			foreach ($result as $r) {
				$menuids[] = $r['id'];
			}
		}
		exit(json_encode($menuids));
	}

}
?>