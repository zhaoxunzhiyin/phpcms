<?php

class foreground {
	public $db, $memberinfo, $ipbanned, $menu_db;
	private $input, $cache, $_member_modelinfo;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		self::check_ip();
		$this->db = pc_base::load_model('member_model');
		//ajax验证信息不需要登录
		if(substr(ROUTE_A, 0, 7) != 'public_') {
			self::check_member();
		}
	}
	
	/**
	 * 判断用户是否已经登录
	 */
	final public function check_member() {
		$cms_auth = param::get_cookie('auth');
		if(ROUTE_M =='member' && ROUTE_C =='index' && in_array(ROUTE_A, array('login', 'alogin', 'logout', 'register', 'mini', 'send_newmail'))) {
			if ($cms_auth && ROUTE_A != 'mini' && ROUTE_A != 'alogin' && ROUTE_A != 'logout') {
				showmessage(L('login_success', '', 'member'), APP_PATH.'index.php?m=member&c=index');
			} else {
				$this->memberinfo['siteid'] = 1;
				$this->memberinfo['groupid'] = 8;
				return true;
			}
		} else {
			//判断是否存在auth cookie
			if ($cms_auth) {
				$auth_key = get_auth_key('login');
				$login_attr = param::get_cookie('_login_attr');
				list($userid, $password) = explode("\t", sys_auth($cms_auth, 'DECODE', $auth_key));
				$userid = intval($userid);
				//验证用户，获取用户信息
				$this->memberinfo = $this->db->get_one(array('userid'=>$userid));
				if($this->memberinfo['islock']) dr_msg(0, L('user_is_lock'));
				//获取用户模型信息
				$this->db->set_model($this->memberinfo['modelid']);

				$this->_member_modelinfo = $this->db->get_one(array('userid'=>$userid));
				$this->_member_modelinfo = $this->_member_modelinfo ? $this->_member_modelinfo : array();
				$this->db->set_model();
				if(is_array($this->memberinfo)) {
					$this->memberinfo = array_merge($this->memberinfo, $this->_member_modelinfo);
				}
				
				if($this->memberinfo && $this->memberinfo['password'] === $password) {
					
					if ($login_attr!=md5(SYS_KEY.$this->memberinfo['password'].(isset($this->memberinfo['login_attr']) ? $this->memberinfo['login_attr'] : ''))) {
						$config = getcache('common', 'commons');
						if (isset($config['login_use']) && dr_in_array('member', $config['login_use'])) {
							$this->cache->del_auth_data('member_option_'.$userid, 1);
						}
						param::set_cookie('auth', '');
						param::set_cookie('_userid', '');
						param::set_cookie('_login_attr', '');
						param::set_cookie('_username', '');
						param::set_cookie('_groupid', '');
						param::set_cookie('_nickname', '');
						dr_redirect(APP_PATH.'index.php?m=member&c=index&a=login');
					}
					
					if (!defined('SITEID')) {
						define('SITEID', $this->memberinfo['siteid']);
					}
					
					if($this->memberinfo['groupid'] == 1) {
						param::set_cookie('auth', '');
						param::set_cookie('_userid', '');
						param::set_cookie('_username', '');
						param::set_cookie('_groupid', '');
						showmessage(L('userid_banned_by_administrator', '', 'member'), APP_PATH.'index.php?m=member&c=index&a=login');
					} elseif($this->memberinfo['groupid'] == 7) {
						param::set_cookie('auth', '');
						param::set_cookie('_userid', '');
						param::set_cookie('_groupid', '');
						
						//设置当前登录待验证账号COOKIE，为重发邮件所用
						param::set_cookie('_regusername', $this->memberinfo['username']);
						param::set_cookie('_reguserid', $this->memberinfo['userid']);
						
						param::set_cookie('email', $this->memberinfo['email']);
						showmessage(L('need_emial_authentication', '', 'member'), APP_PATH.'index.php?m=member&c=index&a=register&t=2');
					}
				} else {
					param::set_cookie('auth', '');
					param::set_cookie('_userid', '');
					param::set_cookie('_login_attr', '');
					param::set_cookie('_username', '');
					param::set_cookie('_groupid', '');
					param::set_cookie('_nickname', '');
					dr_redirect(APP_PATH.'index.php?m=member&c=index&a=login');
				}
				unset($userid, $password, $cms_auth, $auth_key);
			} else {
				if(ROUTE_M =='member' && ROUTE_C =='content' && ROUTE_A=='publish') {
					$this->memberinfo['siteid'] = 1;
					if (!param::get_cookie('_groupid')) {
						$this->memberinfo['groupid'] = 8;
					}
				} else {
					$forward= $this->input->get('forward') ? urlencode($this->input->get('forward')) : urlencode(dr_now_url());
					showmessage(L('please_login', '', 'member'), APP_PATH.'index.php?m=member&c=index&a=login&forward='.$forward);
				}
			}
		}
	}
	/**
	 * 
	 * IP禁止判断 ...
	 */
	private function check_ip(){
		$this->ipbanned = pc_base::load_model('ipbanned_model');
		$this->ipbanned->check_ip();
 	}
}