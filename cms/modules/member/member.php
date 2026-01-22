<?php
/**
 * 管理员后台会员操作类
 */

defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('util', 'content');

class member extends admin {
	
	private $input, $cache, $db, $member_login_db, $module_db, $member_cache, $list_field, $verify_db;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('member_model');
		$this->member_login_db = pc_base::load_model('member_login_model');
		$this->module_db = pc_base::load_model('module_model');
		$this->member_cache = getcache('member_setting', 'member');
		$this->list_field = $this->member_cache['list_field'];
	}

	/**
	 * 会员列表
	 */
	function init() {
		//搜索框
		$keyword = $this->input->get('keyword') ? $this->input->get('keyword') : '';
		$type = $this->input->get('type') ? $this->input->get('type') : '';
		$groupid = $this->input->get('groupid') ? $this->input->get('groupid') : '';
		$modelid = $this->input->get('modelid') ? $this->input->get('modelid') : '';
		
		//站点信息
		$sitelistarr = getcache('sitelist', 'commons');
		$siteid = $this->input->get('siteid') ? $this->input->get('siteid') : '';
		
		foreach ($sitelistarr as $k=>$v) {
			$sitelist[$k] = $v['name'];
		}
		
		$status = $this->input->get('status') ? $this->input->get('status') : '';
		$amount_from = $this->input->get('amount_from') ? $this->input->get('amount_from') : '';
		$amount_to = $this->input->get('amount_to') ? $this->input->get('amount_to') : '';
		$point_from = $this->input->get('point_from') ? $this->input->get('point_from') : '';
		$point_to = $this->input->get('point_to') ? $this->input->get('point_to') : '';
				
		$start_time = $this->input->get('start_time') ? $this->input->get('start_time') : '';
		$end_time = $this->input->get('end_time') ? $this->input->get('end_time') : '';
		$grouplist = getcache('grouplist');
		foreach($grouplist as $k=>$v) {
			$grouplist[$k] = $v['name'];
		}
		//会员所属模型		
		$modellistarr = getcache('member_model', 'commons');
		foreach ($modellistarr as $k=>$v) {
			$modellist[$k] = $v['name'];
		}

		if ($this->input->get('dosubmit')) {
			
			//默认选取一个月内的用户，防止用户量过大给数据造成灾难
			$where_start_time = strtotime($start_time) ? strtotime($start_time) : 0;
			$where_end_time = strtotime($end_time) + 86400;
			//开始时间大于结束时间，置换变量
			if($where_start_time > $where_end_time) {
				$tmp = $where_start_time;
				$where_start_time = $where_end_time;
				$where_end_time = $tmp;
				$tmptime = $start_time;
				
				$start_time = $end_time;
				$end_time = $tmptime;
				unset($tmp, $tmptime);
			}
			
			//如果是超级管理员角色，显示所有用户，否则显示当前站点用户
			if(cleck_admin(param::get_session('roleid'))) {
				if(!empty($siteid)) {
					if ($siteid && is_array($siteid)) {
						$sidin = [];
						foreach ($siteid as $tid) {
							$tid = intval($tid);
							if ($tid) {
								$sidin[] = $tid;
							}
						}
						if ($sidin) {
							$where[] = "`siteid` in (".implode(',', $sidin).")";
						}
					}
				}
			} else {
				$siteid = get_siteid();
				$where[] = "`siteid` = '$siteid'";
			}
			
			if ($status && is_array($status)) {
				$sin = [];
				foreach ($status as $sid) {
					$sid = intval($sid);
					$sin[] = $sid;
				}
				if ($sin) {
					$where[] = "`islock` in (".implode(',', $sin).")";
				}
			}
			
			if ($groupid && is_array($groupid)) {
				$in = [];
				foreach ($groupid as $gid) {
					$gid = intval($gid);
					if ($gid) {
						$in[] = $gid;
					}
				}
				if ($in) {
					$where[] = "`groupid` in (".implode(',', $in).")";
				}
			}
			
			if ($modelid && is_array($modelid)) {
				$min = [];
				foreach ($modelid as $mid) {
					$mid = intval($mid);
					if ($mid) {
						$min[] = $mid;
					}
				}
				if ($min) {
					$where[] = "`modelid` in (".implode(',', $min).")";
				}
			}
			$start_time && $end_time && $where[] = "`regdate` BETWEEN '$where_start_time' AND '$where_end_time'";

			//资金范围
			if($amount_from) {
				if($amount_to) {
					if($amount_from > $amount_to) {
						$tmp = $amount_from;
						$amount_from = $amount_to;
						$amount_to = $tmp;
						unset($tmp);
					}
					$where[] = "`amount` BETWEEN '$amount_from' AND '$amount_to'";
				} else {
					$where[] = "`amount` > '$amount_from'";
				}
			}
			//点数范围
			if($point_from) {
				if($point_to) {
					if($point_from > $point_to) {
						$tmp = $amount_from;
						$point_from = $point_to;
						$point_to = $tmp;
						unset($tmp);
					}
					$where[] = "`point` BETWEEN '$point_from' AND '$point_to'";
				} else {
					$where[] = "`point` > '$point_from'";
				}
			}
		
			if($keyword) {
				if ($type == '1') {
					$where[] = "`username` LIKE '%".$this->db->escape($keyword)."%'";
				} elseif($type == '2') {
					$where[] = "`userid` = '".$this->db->escape($keyword)."'";
				} elseif($type == '3') {
					$where[] = "`email` LIKE '%".$this->db->escape($keyword)."%'";
				} elseif($type == '4') {
					$where[] = "`regip` = '".$this->db->escape($keyword)."'";
				} elseif($type == '5') {
					$where[] = "`nickname` LIKE '%".$this->db->escape($keyword)."%'";
				} else {
					$where[] = "`username` LIKE '%".$this->db->escape($keyword)."%'";
				}
			}
		}

		$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
		$memberlist = $this->db->listinfo(($where ? implode(' AND ', $where) : ''), $this->input->get('order') ? $this->input->get('order') : 'userid DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$list_field = $this->list_field;
		if (!$list_field) {
			$list_field = array (
				'avatar' =>
					array (
						'use' => '1',
						'name' => '头像',
						'width' => '60',
						'func' => 'avatar',
					),
				'username' =>
					array (
						'use' => '1',
						'name' => '账号',
						'width' => '110',
						'func' => 'author',
					),
				'nickname' =>
					array (
						'use' => '1',
						'name' => '昵称',
						'width' => '120',
						'func' => '',
					),
				'amount' =>
					array (
						'use' => '1',
						'name' => '余额',
						'width' => '120',
						'func' => 'money',
					),
				'point' =>
					array (
						'use' => '1',
						'name' => '积分',
						'width' => '120',
						'func' => 'score',
					),
				'regip' =>
					array (
						'use' => '1',
						'name' => '注册IP',
						'width' => '140',
						'func' => 'ip',
					),
				'regdate' =>
					array (
						'use' => '1',
						'name' => '注册时间',
						'width' => '160',
						'func' => 'datetime',
					),
			);
		}
		$clink = module_clink('member', 'member');
		$foot_tpl = '';
		$foot_tpl .= '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>'.PHP_EOL;
		$foot_tpl .= '<label><button type="button" onclick="delall();" class="btn red btn-sm"> <i class="fa fa-trash"></i> '.L('delete').'</button></label>'.PHP_EOL;
		$data = [];
		$data[] = [
			'icon' => 'fa fa-arrows',
			'name' => L('move'),
			'url' => 'javascript:move();',
			'displayorder' => 0,
		];
		$data[] = [
			'icon' => 'fa fa-lock',
			'name' => L('lock'),
			'url' => 'javascript:document.myform.action=\'?m=member&c=member&a=lock\';$(\'#myform\').submit();',
			'displayorder' => 0,
		];
		$data[] = [
			'icon' => 'fa fa-unlock',
			'name' => L('unlock'),
			'url' => 'javascript:document.myform.action=\'?m=member&c=member&a=unlock\';$(\'#myform\').submit();',
			'displayorder' => 0,
		];
		$cbottom = module_cbottom('member', 'member', $data);
		if ($cbottom) {
			$foot_tpl .= '<label>
				<div class="btn-group dropup">
					<a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-cogs"></i> '.L('批量操作').'
						<i class="fa fa-angle-up"></i>
					</a>
					<ul class="dropdown-menu">';
			foreach ($cbottom as $i => $a) {
				$foot_tpl .= '<li><a href="'.str_replace(['{modelid}', '{siteid}', '{m}'], [$modelid, $siteid, ROUTE_M], urldecode($a['url'])).'"> <i class="'.$a['icon'].'"></i> '.$a['name'].' </a></li>';
				if ($i) {
					$foot_tpl .= '<div class="dropdown-line"></div>';
				}
			}
			$foot_tpl .= '</ul>
				</div>
			</label>';
		}
		include $this->admin_tpl('member_list');
	}

	// 修改账号
	public function username_edit() {
		$show_header = true;

		$userid = intval($this->input->get('userid'));
		$member = $this->db->get_one(array('userid'=>$userid));
		if (!$member) {
			dr_json(0, L('该用户不存在'));
		}

		if (IS_POST) {
			$name = trim(dr_safe_filename($this->input->post('name')));
			if (!$name) {
				dr_json(0, L('新账号不能为空'), array('field' => 'name'));
			} elseif ($member['username'] == $name) {
				dr_json(0, L('新账号不能和原始账号相同'), array('field' => 'name'));
			} elseif ($this->db->count(array('username'=>$name))) {
				dr_json(0, L('新账号'.$name.'已经注册'), array('field' => 'name'));
			}
			$rt = check_username($name);
			if (!$rt['code']) {
				dr_json(0, $rt['msg'], array('field' => 'name'));
			}

			$this->db->update(array('username'=>$name), array('userid'=>$userid));

			dr_json(1, L('操作成功'));
		}

		include $this->admin_tpl('member_edit_username');exit;
	}
		
	/**
	 * add member
	 */
	function add() {
		header("Cache-control: private");
		if(IS_POST) {
			$info = $this->input->post('info');
			!$info['username'] && dr_admin_msg(0, L('input').L('username'), array('field' => 'username'));
			if ($this->db->count(array('username'=>$info['username']))) {
				dr_admin_msg(0, L('member_exist'), array('field' => 'username'));
			}
			$info['password'] = dr_safe_password($info['password']);
			$rt = check_password((string)$info['password'], (string)$info['username']);
			if (!$rt['code']) {
				dr_admin_msg(0, $rt['msg'], ['field' => 'password']);
			}
			!$info['email'] && dr_admin_msg(0, L('input').L('email'), array('field' => 'email'));
			if (!check_email($info['email'])) {
				dr_admin_msg(0, L('email_format_incorrect'), array('field' => 'email'));
			}
			if ($this->db->count(array('email'=>$info['email']))) {
				dr_admin_msg(0, L('email_already_exist'), array('field' => 'email'));
			}
			!$info['nickname'] && dr_admin_msg(0, L('input').L('nickname'), array('field' => 'nickname'));
			if ($this->db->count(array('nickname'=>$info['nickname']))) {
				dr_admin_msg(0, L('nickname').L('exists'), array('field' => 'nickname'));
			}
			$info['regip'] = ip_info();
			$info['overduedate'] = strtotime($info['overduedate']);

			$info['encrypt'] = create_randomstr(10);
			$info['password'] = password($info['password'], $info['encrypt']);
			$info['encrypt'] = $info['encrypt'];
			$info['nickname'] = $info['nickname'];
			$info['email'] = $info['email'];
			$info['mobile'] = $info['mobile'];
			if ($info['mobile'] && $this->db->count(array('mobile'=>$info['mobile']))) {
				dr_admin_msg(0, L('手机号码已经注册'), array('field' => 'mobile'));
			}
			$info['groupid'] = $info['groupid'];
			$info['point'] = $info['point'];
			$info['modelid'] = $info['modelid'];
			$info['vip'] = $info['vip'];
			$info['regdate'] = $info['lastdate'] = SYS_TIME;
			
			$this->db->insert($info);
			if($this->db->insert_id()){
				dr_admin_msg(1,L('operation_success'),'?m=member&c=member&a=add', '', 'add');
			}
		} else {
			$show_header = $show_scroll = true;
			$siteid = get_siteid();
			//会员组缓存
			$group_cache = getcache('grouplist', 'member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}
			//会员模型缓存
			$member_model_cache = getcache('member_model', 'commons');
			foreach($member_model_cache as $_key=>$_value) {
				if($siteid == $_value['siteid']) {
					$modellist[$_key] = $_value['name'];
				}
			}
			
			include $this->admin_tpl('member_add');
		}
		
	}
	
	/**
	 * edit member
	 */
	function edit() {
		if(IS_POST) {
			$memberinfo = $info = array();
			$post = $this->input->post('info');
			$basicinfo['userid'] = $post['userid'];
			$basicinfo['username'] = $post['username'];
			$basicinfo['nickname'] = $post['nickname'];
			$basicinfo['email'] = $post['email'];
			$basicinfo['point'] = $post['point'];
			$basicinfo['password'] = dr_safe_password($post['password']);
			if (isset($basicinfo['password']) && !empty($basicinfo['password'])) {
				$rt = check_password($basicinfo['password'], $basicinfo['username']);
				if (!$rt['code']) {
					dr_admin_msg(0, $rt['msg'], ['field' => 'password']);
				}
			}
			$basicinfo['groupid'] = $post['groupid'];
			$basicinfo['modelid'] = $post['modelid'];
			$basicinfo['vip'] = $post['vip'];
			$basicinfo['mobile'] = $post['mobile'];
			!$basicinfo['username'] && dr_admin_msg(0, L('input').L('username'), array('field' => 'username'));
			if ($this->db->count(array('userid<>'=>$basicinfo['userid'], 'username'=>$basicinfo['username']))) {
				dr_admin_msg(0, L('member_exist'), array('field' => 'username'));
			}
			!$basicinfo['email'] && dr_admin_msg(0, L('input').L('email'), array('field' => 'email'));
			if (!check_email($basicinfo['email'])) {
				dr_admin_msg(0, L('email_format_incorrect'), array('field' => 'email'));
			}
			if ($this->db->count(array('userid<>'=>$basicinfo['userid'], 'email'=>$basicinfo['email']))) {
				dr_admin_msg(0, L('email_already_exist'), array('field' => 'email'));
			}
			!$basicinfo['nickname'] && dr_admin_msg(0, L('input').L('nickname'), array('field' => 'nickname'));
			if ($this->db->count(array('userid<>'=>$basicinfo['userid'], 'nickname'=>$basicinfo['nickname']))) {
				dr_admin_msg(0, L('nickname').L('exists'), array('field' => 'nickname'));
			}
			if ($basicinfo['mobile'] && $this->db->count(array('userid<>'=>$basicinfo['userid'], 'mobile'=>$basicinfo['mobile']))) {
				dr_admin_msg(0, L('手机号码'.$basicinfo['mobile'].'已经注册'), array('field' => 'mobile'));
			}
			$basicinfo['overduedate'] = strtotime($post['overduedate']);

			//会员基本信息
			$info = $this->_checkuserinfo($basicinfo, 1);

			//会员模型信息
			$modelinfo = array_diff_key($post, $info);
			$modelinfo['userid'] = $info['userid'];
			//过滤vip过期时间
			unset($modelinfo['overduedate']);

			$userid = $info['userid'];
			
			//如果是超级管理员角色，显示所有用户，否则显示当前站点用户
			if(cleck_admin(param::get_session('roleid'))) {
				$where = array('userid'=>$userid);
			} else {
				$siteid = get_siteid();
				$where = array('userid'=>$userid, 'siteid'=>$siteid);
			}
			
		
			$userinfo = $this->db->get_one($where);
			if(empty($userinfo)) {
				dr_admin_msg(0,L('user_not_exist').L('or').L('no_permission'), HTTP_REFERER);
			}
			
			//删除用户头像
			if(!empty($this->input->post('delavatar'))) {
				$this->deleteavatar(intval($userinfo['userid']));
			}

			unset($info['userid']);
			unset($info['username']);
			
			//如果密码不为空，修改用户密码。
			if(isset($info['password']) && !empty($info['password'])) {
				$info['password'] = password($info['password'], $userinfo['encrypt']);
				// 钩子
				pc_base::load_sys_class('hooks')::trigger('member_edit_password_after', $basicinfo);
			} else {
				unset($info['password']);
			}

			$this->db->update($info, array('userid'=>$userid));
			
			require_once CACHE_MODEL_PATH.'member_input.class.php';
			require_once CACHE_MODEL_PATH.'member_update.class.php';
			$member_input = new member_input($basicinfo['modelid']);
			$modelinfo = $member_input->get($modelinfo);

			//更新模型表，方法更新了$this->table
			$this->db->set_model($info['modelid']);
			$userinfo = $this->db->get_one(array('userid'=>$userid));
			if($userinfo) {
				$this->db->update($modelinfo, array('userid'=>$userid));
			} else {
				$modelinfo['userid'] = $userid;
				$this->db->insert($modelinfo);
			}
			
			dr_admin_msg(1,L('operation_success'), '?m=member&c=member&a=manage', '', 'edit');
		} else {
			$show_header = $show_scroll = true;
			$siteid = get_siteid();
			$userid = $this->input->get('userid') ? $this->input->get('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			
			//会员组缓存
			$group_cache = getcache('grouplist', 'member');
			foreach($group_cache as $_key=>$_value) {
				$grouplist[$_key] = $_value['name'];
			}

			//会员模型缓存
			$member_model_cache = getcache('member_model', 'commons');
			foreach($member_model_cache as $_key=>$_value) {
				if($siteid == $_value['siteid']) {
					$modellist[$_key] = $_value['name'];
				}
			}
			
			//如果是超级管理员角色，显示所有用户，否则显示当前站点用户
			if(cleck_admin(param::get_session('roleid'))) {
				$where = array('userid'=>$userid);
			} else {
				$where = array('userid'=>$userid, 'siteid'=>$siteid);
			}

			$memberinfo = $this->db->get_one($where);
			
			if(empty($memberinfo)) {
				dr_admin_msg(0,L('user_not_exist').L('or').L('no_permission'), HTTP_REFERER);
			}
			
			$memberinfo['avatar'] = get_memberavatar($memberinfo['userid']);
			
			$modelid = $this->input->get('modelid') ? $this->input->get('modelid') : $memberinfo['modelid'];
			
			//获取会员模型表单
			require CACHE_MODEL_PATH.'member_form.class.php';
			$member_form = new member_form($modelid);
			
			$form_overdudate = form::date('info[overduedate]', $memberinfo['overduedate'] ? date('Y-m-d H:i:s',$memberinfo['overduedate']) : '', 1);
			$this->db->set_model($modelid);
			$membermodelinfo = $this->db->get_one(array('userid'=>$userid));
			$forminfos = $forminfos_arr = $member_form->get($membermodelinfo);
			
			//万能字段过滤
			foreach($forminfos as $field=>$info) {
				if($info['isomnipotent']) {
					unset($forminfos[$field]);
				} else {
					if($info['formtype']=='omnipotent') {
						foreach($forminfos_arr as $_fm=>$_fm_value) {
							if($_fm_value['isomnipotent']) {
								$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'], $info['form']);
							}
						}
						$forminfos[$field]['form'] = $info['form'];
					}
				}
			}
			$show_dialog = 1;
			include $this->admin_tpl('member_edit');		
		}
	}

	/**
	 * 后台授权登录
	 */
	public function public_alogin() {
		$uid = intval($this->input->get('id'));
		if (!$this->cleck_edit_member($uid)) {
			dr_admin_msg(0, L('only_fonder_operation'));
		}
		$admin = $this->db->get_one(array('userid'=>$uid));
		$this->cache->set_data('admin_login_member', $admin, 30);

		dr_admin_msg(1,L('正在授权登录此用户...'), WEB_PATH.'index.php?m=member&c=index&a=alogin');exit;
	}
	
	/**
	 * 删除用户头像
	 * @return {0:失败;1:成功}
	 */
	public function deleteavatar($uid) {
		//根据用户id创建文件夹
		if(!$uid) {
			exit('0');
		}
		$upload = pc_base::load_sys_class('upload');
		$memberinfo = $this->db->get_one(array('userid'=>$uid));
		if ($memberinfo && $memberinfo['avatar']) {
			$data['aid'] = $memberinfo['avatar'];
			$rt = $upload->_delete_file($data);
			$this->db->update(array('avatar'=>''), array('userid'=>$uid));
		}
	}
	
	/**
	 * delete member
	 */
	function delete() {
		$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$uidarr = array_map('intval',$uidarr);
		$where = to_sqls($uidarr, '', 'userid');
		//查询用户信息
		$userinfo_arr = $this->db->select($where, "userid, modelid");
		$userinfo = array();
		if(is_array($userinfo_arr)) {
			foreach($userinfo_arr as $v) {
				$userinfo[$v['userid']] = $v['modelid'];
			}
		}
		if ($this->db->delete($where)) {
			//删除用户模型用户资料
			foreach($uidarr as $v) {
				if(!empty($userinfo[$v])) {
					$this->db->set_model($userinfo[$v]);
					$this->db->delete(array('userid'=>$v));
					$this->member_login_db->delete(array('uid'=>$v));
				}
			}
			dr_admin_msg(1,L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}

	/**
	 * lock member
	 */
	function lock() {
		if($this->input->post('userid')) {
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$where = to_sqls($uidarr, '', 'userid');
			$this->db->update(array('islock'=>1), $where);
			dr_admin_msg(1,L('member_lock').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}
	
	/**
	 * unlock member
	 */
	function unlock() {
		if($this->input->post('userid')) {
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$where = to_sqls($uidarr, '', 'userid');
			if($this->db->update(array('islock'=>0), $where)) {
				$config = getcache('common','commons');
				if ($config) {
					if (isset($config['safe_wdl']) && $config['safe_wdl']) {
						$time = $config['safe_wdl'] * 3600 * 24;
						$login_where[] = 'logintime < '.(SYS_TIME - $time);
						$login_where[] = to_sqls($uidarr, '', 'uid');
						$this->member_login_db->update(array('logintime'=>SYS_TIME), implode(' AND ', $login_where));
					}
				}
			}
			dr_admin_msg(1,L('member_unlock').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}

	/**
	 * move member
	 */
	function move() {
		if(IS_POST) {
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('please_select').L('member'), HTTP_REFERER);
			$groupid = $this->input->post('groupid') && !empty($this->input->post('groupid')) ? $this->input->post('groupid') : dr_admin_msg(0,L('please_select').L('member_group'), HTTP_REFERER);
			
			$where = to_sqls($uidarr, '', 'userid');
			$this->db->update(array('groupid'=>$groupid), $where);
			dr_admin_msg(1,L('member_move').L('operation_success'), HTTP_REFERER, '', 'move');
		} else {
			$show_header = $show_scroll = true;
			$grouplist = getcache('grouplist');
			foreach($grouplist as $k=>$v) {
				$grouplist[$k] = $v['name'];
			}
			
			$ids = $this->input->get('ids') ? explode(',', $this->input->get('ids')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			array_pop($ids);
			if(!empty($ids)) {
				$where = to_sqls($ids, '', 'userid');
				$userarr = $this->db->listinfo($where);
			} else {
				dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER, '', 'move');
			}
			
			include $this->admin_tpl('member_move');
		}
	}

	function memberinfo() {
		$show_header = $show_pc_hash = true;
		
		$userid = !empty($this->input->get('userid')) ? intval($this->input->get('userid')) : '';
		$username = !empty($this->input->get('username')) ? trim($this->input->get('username')) : '';
		if(!empty($userid)) {
			$memberinfo = $this->db->get_one(array('userid'=>$userid));
		} elseif(!empty($username)) {
			$memberinfo = $this->db->get_one(array('username'=>$username));
		} else {
			dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		}
		
		if(empty($memberinfo)) {
			dr_admin_msg(0,L('user').L('not_exists'), HTTP_REFERER);
		}
		
		$memberinfo['avatar'] = get_memberavatar($memberinfo['userid']);

		$grouplist = getcache('grouplist');
		//会员模型缓存
		$modellist = getcache('member_model', 'commons');

		$modelid = !empty($this->input->get('modelid')) ? intval($this->input->get('modelid')) : $memberinfo['modelid'];
		//站群缓存
		$sitelist =getcache('sitelist', 'commons');

		$this->db->set_model($modelid);
		$member_modelinfo_arr = $this->db->get_one(array('userid'=>$memberinfo['userid']));
		//模型字段名称
		$model_fieldinfo = getcache('model_field_'.$modelid, 'model');
	
		foreach($model_fieldinfo as $k=>$v) {
			if($v['formtype'] == 'omnipotent') continue;
			if($v['formtype'] == 'image') {
				$member_modelinfo[$v['name']] = "<a href='".dr_get_file($member_modelinfo_arr[$k])."' target='_blank'><img src='".dr_get_file($member_modelinfo_arr[$k])."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
			} elseif($v['formtype'] == 'images') {
				$tmp = dr_get_files($member_modelinfo_arr[$k]);
				$member_modelinfo[$v['name']] = '';
				if(is_array($tmp)) {
					foreach ($tmp as $tv) {
						$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$tv['url']."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
					}
					unset($tmp);
				}
			} elseif($v['formtype'] == 'downfiles') {
				$tmp = dr_get_files($member_modelinfo_arr[$k]);
				$member_modelinfo[$v['name']] = '';
				if(is_array($tmp)) {
					foreach ($tmp as $tv) {
						$ext = trim(strtolower(strrchr((string)$tv['url'], '.')), '.');
						$file = WEB_PATH.'api.php?op=icon&fileext='.$ext;
						if (dr_is_image($ext)) {
							$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$tv['url']."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
						} elseif ($ext == 'mp4') {
							$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$file."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
						} elseif ($ext == 'mp3') {
							$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$file."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
						} elseif (strpos((string)$tv['url'], 'http://') === 0) {
							$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$file."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
						} else {
							$member_modelinfo[$v['name']] .= " <a href='".$tv['url']."' target='_blank'><img src='".$tv['url']."' height='40' widht='40' onerror=\"this.src='".IMG_PATH."member/nophoto.gif'\"></a>";
						}
					}
					unset($tmp);
				}
			} elseif($v['formtype'] == 'datetime' && $v['fieldtype'] == 'int') {
				if ($member_modelinfo_arr[$k]) {
					$member_modelinfo[$v['name']] = $v['format'] ? dr_date($member_modelinfo_arr[$k], 'Y-m-d H:i:s') : dr_date($member_modelinfo_arr[$k], 'Y-m-d');
				}
			} elseif($v['formtype'] == 'datetime' && $v['fieldtype'] == 'varchar') {
				if ($member_modelinfo_arr[$k]) {
					$member_modelinfo[$v['name']] = $v['format2'] ? dr_date($member_modelinfo_arr[$k], 'H:i:s') : dr_date($member_modelinfo_arr[$k], 'H:i');
				}
			} elseif($v['formtype'] == 'box') {
				$arr = dr_string2array($member_modelinfo_arr[$k]);
				if (!is_array($arr)) {
					$arr = explode(',',$arr);
				}
				$str = array();
				if (is_array($arr)) {
					$options = dr_format_option_array($v['options']);
					if ($options) {
						foreach ($options as $boxi => $boxv) {
							if (dr_in_array($boxi, $arr)) {
								$str[] = $boxv;
							}
						}
					}
				}
				$member_modelinfo[$v['name']] = implode('、', $str);
				unset($arr, $options, $str);
			} elseif($v['formtype'] == 'linkage') {
				$tmp = string2array($v['setting']);
				$member_modelinfo[$v['name']] = dr_linkagepos($tmp['linkage'], $member_modelinfo_arr[$k], $tmp['space']);
				unset($tmp);
			} elseif($v['formtype'] == 'linkages') {
				$tmp = string2array($v['setting']);
				$arr = dr_string2array($member_modelinfo_arr[$k]);
				if (!is_array($arr)) {
					$arr = explode(',',$arr);
				}
				$str = array();
				if ($arr) {
					foreach ($arr as $value) {
						$str[] = dr_linkagepos($tmp['linkage'], $value, $tmp['space']);
					}
				}
				$member_modelinfo[$v['name']] = implode('、', $str);
				unset($tmp, $arr, $str);
			} else {
				$member_modelinfo[$v['name']] = $member_modelinfo_arr[$k];
			}
		}

		include $this->admin_tpl('member_moreinfo');
	}
	
	private function _checkuserinfo($data, $is_edit=0) {
		if(!is_array($data)){
			dr_admin_msg(0,L('need_more_param'));return false;
		} elseif (!is_username($data['username']) && !$is_edit){
			dr_admin_msg(0,L('username_format_incorrect'));return false;
		} elseif (!isset($data['userid']) && $is_edit) {
			dr_admin_msg(0,L('username_format_incorrect'));return false;
		} elseif (empty($data['email']) || !is_email($data['email'])){
			dr_admin_msg(0,L('email_format_incorrect'));return false;
		}
		return $data;
	}
	
	/**
	 * 检查用户名
	 * @param string $username	用户名
	 * @return $status {-4：用户名禁止注册;-1:用户名已经存在 ;1:成功}
	 */
	public function public_checkname_ajax() {
		$username = $this->input->get('username') && trim($this->input->get('username')) && is_username(trim($this->input->get('username'))) ? trim($this->input->get('username')) : exit(0);
		if(CHARSET != 'utf-8') {
			$username = iconv('utf-8', CHARSET, $username);
		}
		$username = safe_replace($username);
		//首先判断会员审核表
		$this->verify_db = pc_base::load_model('member_verify_model');
		if($this->verify_db->get_one(array('username'=>$username))) {
			exit('0');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['username'] == $this->db->escape($username)){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('username'=>$username));
				$status ? exit('0') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('username'=>$username));
			$status ? exit('0') : exit('1');
		}
		
	}
	
	/**
	 * 检查邮箱
	 * @param string $email
	 * @return $status {-1:email已经存在 ;-5:邮箱禁止注册;1:成功}
	 */
	public function public_checkemail_ajax() {
		$email = $this->input->get('email') && trim($this->input->get('email')) && is_email(trim($this->input->get('email'))) ? trim($this->input->get('email')) : exit(0);
		if (!check_email($email)) {
			exit('0');
		}
		//首先判断会员审核表
		$this->verify_db = pc_base::load_model('member_verify_model');
		if($this->verify_db->get_one(array('email'=>$email))) {
			exit('0');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['email'] == $email){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('email'=>$email));
				$status ? exit('0') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('email'=>$email));
			$status ? exit('0') : exit('1');
		}
	}
	
	/**
	 * 检查用户昵称
	 * @param string $nickname	昵称
	 * @return $status {0:已存在;1:成功}
	 */
	public function public_checknickname_ajax() {
		$nickname = $this->input->get('nickname') && trim($this->input->get('nickname')) ? trim($this->input->get('nickname')) : exit('0');
		if(CHARSET != 'utf-8') {
			$nickname = iconv('utf-8', CHARSET, $nickname);
		}
		//首先判断会员审核表
		$this->verify_db = pc_base::load_model('member_verify_model');
		if($this->verify_db->get_one(array('nickname'=>$nickname))) {
			exit('0');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['nickname'] == $this->db->escape($nickname)){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('nickname'=>$nickname));
				$status ? exit('0') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('nickname'=>$nickname));
			$status ? exit('0') : exit('1');
		}
	}	
}
?>