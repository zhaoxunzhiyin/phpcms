<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class admin_manage extends admin {
	private $input,$db,$admin_login_db,$role_db,$op,$role;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('admin_model');
		$this->admin_login_db = pc_base::load_model('admin_login_model');
		$this->role_db = pc_base::load_model('admin_role_model');
		$this->op = pc_base::load_app_class('admin_op');
		$this->role = $this->get_role_all();
	}
	
	/**
	 * 管理员管理列表
	 */
	public function init() {
		$userid = param::get_session('userid');
		$admin_username = param::get_cookie('admin_username');
		$page = max(1, intval($this->input->get('page')));
		$infos = $this->db->listinfo('', '', $page, SYS_ADMIN_PAGESIZE);
		if ($infos) {
			foreach ($infos as $i => $t) {
				$role = $this->role_db->select(array('roleid'=>is_array(dr_string2array($t['roleid'])) ? dr_string2array($t['roleid']) : $t['roleid'], 'disabled'=>0));
				if ($role) {
					foreach ($role as $r) {
						$infos[$i]['role'][$r['roleid']] = $this->role[$r['roleid']]['rolename'];
					}
				}
			}
		}
		$pages = $this->db->pages;
		$roles = getcache('role','commons');
		include $this->admin_tpl('admin_list');
	}

	// 修改账号
	public function username_edit() {
		$show_header = true;
		$userid = intval($this->input->get('userid'));
		$info = $this->db->get_one(array('userid'=>$userid));
		extract($info);	
		if (!$info) {
			dr_json(0, L('该用户不存在'));
		}

		if (IS_POST) {
			$name = trim(dr_safe_filename($this->input->post('name')));
			if (is_badword($name)) {
				dr_json(0, L('username_illegal'));
			}
			if (!$name) {
				dr_json(0, L('新账号不能为空'), array('field' => 'name'));
			} elseif ($info['username'] == $name) {
				dr_json(0, L('新账号不能和原始账号相同'), array('field' => 'name'));
			} elseif ($this->db->count(array('username'=>$name))) {
				dr_json(0, L('新账号'.$name.'已经注册'), array('field' => 'name'));
			}
			$rt = $this->check_username($name);
			if (!$rt['code']) {
				dr_json(0, $rt['msg'], array('field' => 'name'));
			}

			$this->db->update(array('username'=>$name), array('userid'=>$userid));

			dr_json(1, L('操作成功'));
		}

		include $this->admin_tpl('admin_edit_username');exit;
	}
	
	/**
	 * 添加管理员
	 */
	public function add() {
		if(IS_AJAX_POST) {
			if($this->check_admin_manage_code()==false){
				dr_json(0, "error auth code");
			}
			$info = $this->input->post('info');
			$info['password'] = dr_safe_password($info['password']);
			if (is_badword($info['username'])) {
				dr_json(0, L('username_illegal'));
			}
			$rs = $this->check_username($info['username']);
			if (!$rs['code']) {
				dr_json(0, $rs['msg'], array('field' => 'username'));
			}
			if(!$this->op->checkname($info['username'])){
				dr_json(0, L('admin_already_exists'), array('field' => 'username'));
			}
			if(!$info['password']){
				dr_json(0, L('password').L('empty'), array('field' => 'password'));
			}
			if(!$info['pwdconfirm']){
				dr_json(0, L('cofirmpwd').L('empty'), array('field' => 'pwdconfirm'));
			}
			if($info['password']!=$info['pwdconfirm']){
				dr_json(0, L('两次密码不同'), array('field' => 'pwdconfirm'));
			}
			$rs = $this->check_password($info['password'], $info['username']);
			if (!$rs['code']) {
				dr_json(0, $rs['msg'], array('field' => 'password'));
			}
			if(!$info['email']){
				dr_json(0, L('email').L('empty'), array('field' => 'email'));
			}
			if(!check_email($info['email'])){
				dr_json(0, L('email').L('格式不正确'), array('field' => 'email'));
			}
			$check = $this->db->get_one(array('email'=>$info['email']));
			if ($check){
				dr_json(0, L('email').L('email_already_exists'), array('field' => 'email'));
			}
			if (isset($info['phone']) && $info['phone']) {
				$check_phone = $this->db->get_one(array('phone'=>$info['phone']));
				if ($check_phone){
					dr_json(0, L('phone_already_exists'), array('field' => 'phone'));
				}
			}
			if (!$info['roleid']) {
				dr_json(0, L('至少要选择一个角色组'), array('field' => 'roleid'));
			}
			$passwordinfo = password($info['password']);
			$info['password'] = $passwordinfo['password'];
			$info['encrypt'] = $passwordinfo['encrypt'];
			
			$admin_fields = array('username', 'email', 'phone', 'password', 'encrypt', 'roleid', 'realname');
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			$info['roleid'] = dr_array2string($info['roleid']);
			$this->db->insert($info);
			if($this->db->insert_id()){
				dr_json(1, L('operation_success'));
			}
		}
		$roles = $this->role_db->select(array('disabled'=>'0'));
		$admin_manage_code = $this->get_admin_manage_code();
		$reply_url = '?m=admin&c=admin_manage&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		include $this->admin_tpl('admin_add');
	}
	
	/**
	 * 修改管理员
	 */
	public function edit() {
		if(IS_AJAX_POST) {
			if($this->check_admin_manage_code()==false){
				dr_json(0, "error auth code");
			}
			$memberinfo = array();
			$info = $this->input->post('info');
			$info['password'] = dr_safe_password($info['password']);
			if(!$info['email']){
				dr_json(0, L('email').L('empty'), array('field' => 'email'));
			}
			if(!check_email($info['email'])){
				dr_json(0, L('email').L('格式不正确'), array('field' => 'email'));
			}
			$check = $this->db->get_one(array('email'=>$info['email'],'userid<>'=>$info['userid']),'userid');
			if ($check && $check['userid']!=$info['userid']){
				dr_json(0, L('email').L('email_already_exists'), array('field' => 'email'));
			}
			if (isset($info['phone']) && $info['phone']) {
				$check_phone = $this->db->get_one(array('phone'=>$info['phone'],'userid<>'=>$info['userid']),'userid');
				if ($check_phone && $check_phone['userid']!=$info['userid']){
					dr_json(0, L('phone_already_exists'), array('field' => 'phone'));
				}
			}
			if (!$info['roleid']) {
				dr_json(0, L('至少要选择一个角色组'), array('field' => 'roleid'));
			}
			if(isset($info['password']) && !empty($info['password'])){
				$rs = $this->check_password($info['password'], $info['username']);
				if (!$rs['code']) {
					dr_json(0, $rs['msg'], array('field' => 'password'));
				}
				if($info['password']!=$info['pwdconfirm']){
					dr_json(0, L('两次密码不同'), array('field' => 'pwdconfirm'));
				}
				$this->op->edit_password($info['userid'], $info['password']);
				// 钩子
				pc_base::load_sys_class('hooks')::trigger('admin_edit_password_after', $info);
			}
			$userid = $info['userid'];
			$admin_fields = array('username', 'email', 'phone', 'roleid', 'realname');
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			if ($userid > 1) {
				$info['roleid'] = dr_array2string($info['roleid']);
			} else {
				unset($info['roleid']);
			}
			$this->db->update($info,array('userid'=>$userid));
			dr_json(1, L('operation_success'));
		}
		$info = $this->db->get_one(array('userid'=>$this->input->get('userid')));
		$role = $this->role_db->select(array('roleid'=>is_array(dr_string2array($info['roleid'])) ? dr_string2array($info['roleid']) : $info['roleid'], 'disabled'=>0));
		if ($role) {
			foreach ($role as $r) {
				$info['role'][$r['roleid']] = $this->role[$r['roleid']]['rolename'];
			}
		}
		extract($info);
		$roles = $this->role_db->select(array('disabled'=>'0'));
		$admin_manage_code = $this->get_admin_manage_code();
		$post_url = '?m=admin&c=admin_manage&a=add&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		$reply_url = '?m=admin&c=admin_manage&a=init&menuid='.$this->input->get('menuid').'&pc_hash='.dr_get_csrf_token();
		include $this->admin_tpl('admin_edit');		
	}
	
	/**
	 * 删除管理员
	 */
	public function delete() {
		$userid = intval($this->input->get('userid'));
		if($userid == '1') dr_admin_msg(0,L('this_object_not_del'), HTTP_REFERER);
		$this->db->delete(array('userid'=>$userid));
		$this->admin_login_db->delete(array('uid'=>$userid));
		dr_admin_msg(1,L('admin_cancel_succ'), HTTP_REFERER);
	}

	/**
	 * 锁定管理员
	 */
	function lock() {
		$userid = intval($this->input->get('userid'));
		if(!$userid) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(ADMIN_FOUNDERS && !dr_in_array($userid, ADMIN_FOUNDERS)) {
				$this->db->update(array('islock'=>1), array('userid'=>$userid));
				dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
			} else {
				dr_admin_msg(0,L('founder_cannot_locked'), HTTP_REFERER);
			}
		}
	}
	
	/**
	 * 解锁管理员
	 */
	function unlock() {
		$userid = intval($this->input->get('userid'));
		if(!$userid) {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		} else {
			if($this->db->update(array('islock'=>0), array('userid'=>$userid))) {
				$config = getcache('common','commons');
				if ($config) {
					if (isset($config['safe_wdl']) && $config['safe_wdl']) {
						$time = $config['safe_wdl'] * 3600 * 24;
						$login_where[] = 'logintime < '.(SYS_TIME - $time);
						$login_where[] = 'uid = '.$userid;
						$this->admin_login_db->update(array('logintime'=>SYS_TIME), implode(' AND ', $login_where));
					}
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		}
	}
	
	/**
	 * 管理员自助修改密码
	 */
	public function public_edit_pwd() {
		$show_header = true;
		$userid = param::get_session('userid');
		if(IS_AJAX_POST) {
			$old_password = dr_safe_password($this->input->post('old_password'));
			$new_password = dr_safe_password($this->input->post('new_password'));
			$new_pwdconfirm = dr_safe_password($this->input->post('new_pwdconfirm'));
			$r = $this->db->get_one(array('userid'=>$userid),'username,password,encrypt');
			if (password($old_password,$r['encrypt']) !== $r['password']) dr_json(0,L('old_password_wrong'), array('field' => 'old_password'));
			if ($old_password == $new_password) {
				dr_json(0, L('旧密码不能与新密码相同'), array('field' => 'new_password'));
			}
			$rs = $this->check_password($new_password, $r['username']);
			if (!$rs['code']) {
				dr_json(0, $rs['msg'], array('field' => 'new_password'));
			}
			if($new_password!=$new_pwdconfirm){
				dr_json(0, L('两次密码不同'), array('field' => 'new_pwdconfirm'));
			}
			if($new_password) {
				if($this->op->edit_password($userid, $new_password)) {
					$member['userid'] = $userid;
					// 钩子
					pc_base::load_sys_class('hooks')::trigger('admin_edit_password_after', $member);
				}
			}
			dr_json(1,L('password_edit_succ_logout'), array('url' => '?m=admin&c=index&a=public_logout'));
		} else {
			$info = $this->db->get_one(array('userid'=>$userid));
			extract($info);
			include $this->admin_tpl('admin_edit_pwd');			
		}

	}
	/*
	 * 编辑用户信息
	 */
	public function public_edit_info() {
		$show_header = true;
		$userid = param::get_session('userid');
		if(IS_AJAX_POST) {
			$admin_fields = array('email','phone','realname','lang');
			$info = array();
			$info = $this->input->post('info');
			if(!$info['realname']){
				dr_json(0, L('realname').L('empty'), array('field' => 'realname'));
			}
			if(!$info['email']){
				dr_json(0, L('email').L('empty'), array('field' => 'email'));
			}
			if(!check_email($info['email'])){
				dr_json(0, L('email').L('格式不正确'), array('field' => 'email'));
			}
			$check = $this->db->get_one(array('email'=>$info['email'],'userid<>'=>$userid),'userid');
			if ($check && $check['userid']!=$userid){
				dr_json(0, L('email').L('email_already_exists'), array('field' => 'email'));
			}
			if (isset($info['phone']) && $info['phone']) {
				$check_phone = $this->db->get_one(array('phone'=>$info['phone'],'userid<>'=>$userid),'userid');
				if ($check_phone && $check_phone['userid']!=$userid){
					dr_json(0, L('phone_already_exists'), array('field' => 'phone'));
				}
			}
			if(trim($info['lang'])=='') $info['lang'] = 'zh-cn';
			foreach ($info as $k=>$value) {
				if (!in_array($k, $admin_fields)){
					unset($info[$k]);
				}
			}
			$this->db->update($info,array('userid'=>$userid));
			$member_setting = getcache('member_setting', 'member');
			param::set_cookie('sys_lang', $info['lang'],($member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0)));
			dr_json(1,L('operation_success'));
		} else {
			$info = $this->db->get_one(array('userid'=>$userid));
			extract($info);
			
			$lang_dirs = glob(PC_PATH.'languages/*');
			$dir_array = array();
			foreach($lang_dirs as $dirs) {
				$dir_array[] = str_replace(PC_PATH.'languages/','',$dirs);
			}
			include $this->admin_tpl('admin_edit_info');			
		}	
	
	}

	//添加修改用户 验证串验证
	private function check_admin_manage_code(){
		$admin_manage_code = $this->input->post('info')['admin_manage_code'];
		$pc_auth_key = md5(SYS_KEY.'adminuser');
		$admin_manage_code = sys_auth($admin_manage_code, 'DECODE', $pc_auth_key);	
		if($admin_manage_code==""){
			return false;
		}
		$admin_manage_code = explode("_", $admin_manage_code);
		if($admin_manage_code[0]!="adminuser" || $admin_manage_code[1]!=$this->input->post('pc_hash')){
			return false;
		}
		return true;
	}
	//添加修改用户 生成验证串
	private function get_admin_manage_code(){
		$pc_auth_key = md5(SYS_KEY.'adminuser');
		$code = sys_auth("adminuser_".$this->input->get('pc_hash')."_".SYS_TIME, 'ENCODE', $pc_auth_key);
		return $code;
	}

	// 验证账号
	public function check_username($value) {

		if (!$value) {
			return dr_return_data(0, L('用户名不能为空'), array('field' => 'username'));
		} elseif (strpos($value, '"') !== false || strpos($value, '\'') !== false) {
			// 引号判断
			return dr_return_data(0, L('用户名存在非法字符'), array('field' => 'username'));
		} elseif (mb_strlen($value) < 2) {
			// 验证用户名长度
			return dr_return_data(0, L('用户名长度不能小于2位，当前'.mb_strlen($value).'位'), array('field' => 'username'));
		} elseif (mb_strlen($value) > 20) {
			// 验证用户名长度
			return dr_return_data(0, L('用户名长度不能大于20位，当前'.mb_strlen($value).'位'), array('field' => 'username'));
		}

		return dr_return_data(1, 'ok');
	}

	// 验证账号的密码
	public function check_password($value, $username) {

		if (!$value) {
			return dr_return_data(0, L('密码不能为空'), array('field' => 'password'));
		} elseif ($value == $username) {
			return dr_return_data(0, L('密码不能与用户名相同'), array('field' => 'password'));
		} elseif (mb_strlen($value) < 6) {
			return dr_return_data(0, L('密码长度不能小于6位，当前'.mb_strlen($value).'位'), array('field' => 'password'));
		} elseif (mb_strlen($value) > 20) {
			return dr_return_data(0, L('密码长度不能大于20位，当前'.mb_strlen($value).'位'), array('field' => 'username'));
		}

		return dr_return_data(1, 'ok');
	}

	// 获取角色组
	public function get_role_all($rid = array()) {

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