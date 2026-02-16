<?php
/**
 * 管理员后台会员审核操作类
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);

class member_verify extends admin {
	
	private $input, $email, $db, $member_db, $member_field_db;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->email = pc_base::load_sys_class('email');
		$this->db = pc_base::load_model('member_verify_model');
	}

	/**
	 * member list
	 */
	function init() {
		$status = !empty($this->input->get('s')) ? $this->input->get('s') : 0;
		$where = array('status'=>$status);
		$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
		$memberlist = $this->db->listinfo($where, 'regdate DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->db->pages;
		$member_model = getcache('member_model', 'commons');
		include $this->admin_tpl('member_verify');
	}
	
	function modelinfo() {
		$show_header = true;
		$userid = !empty($this->input->get('userid')) ? intval($this->input->get('userid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		$modelid = !empty($this->input->get('modelid')) ? intval($this->input->get('modelid')) : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
		
		$memberinfo = $this->db->get_one(array('userid'=>$userid));
		//模型字段名称
		$this->member_field_db = pc_base::load_model('sitemodel_field_model');
		$model_fieldinfo = $this->member_field_db->select(array('modelid'=>$modelid), "*", 100);
		//用户模型字段信息
		$member_fieldinfo = string2array($memberinfo['modelinfo']);
		
		//交换数组key值
		foreach($model_fieldinfo as $v) {
			if(array_key_exists($v['field'], $member_fieldinfo)) {
				$tmp = $member_fieldinfo[$v['field']];
				unset($member_fieldinfo[$v['field']]);
				$member_fieldinfo[$v['name']] = $tmp;
				unset($tmp);
			}
		}

		include $this->admin_tpl('member_verify_modelinfo');
	}
		
	/**
	 * pass member
	 */
	function pass() {
		if ($this->input->post('userid')) {
			$this->member_db = pc_base::load_model('member_model');
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$where = to_sqls($uidarr, '', 'userid');
			$userarr = $this->db->listinfo($where);
			$success_uids = $info = array();
			
			foreach($userarr as $v) {
				$info['password'] = password($v['password'], $v['encrypt']);
				$info['regdate'] = $info['lastdate'] = $v['regdate'];
				$info['username'] = $v['username'];
				$info['nickname'] = $v['nickname'];
				$info['email'] = $v['email'];
				$info['regip'] = $v['regip'];
				$info['point'] = $v['point'];
				$info['groupid'] = $this->_get_usergroup_bypoint($v['point']);
				$info['amount'] = $v['amount'];
				$info['encrypt'] = $v['encrypt'];
				$info['modelid'] = $v['modelid'] ? $v['modelid'] : 10;
				if($v['mobile']) $info['mobile'] = $v['mobile'];
				$userid = $this->member_db->insert($info, 1);

				if($v['modelinfo']) {	//如果数据模型不为空
					//插入会员模型数据
					$user_model_info = string2array($v['modelinfo']);
					$user_model_info['userid'] = $userid;
					$this->member_db->set_model($info['modelid']);
					$this->member_db->insert($user_model_info);
				}
				
				if($userid) {
					$success_uids[] = $v['userid'];
				}
			}
			$where = to_sqls($success_uids, '', 'userid');			
			$this->db->update(array('status'=>1, 'message'=>$this->input->post('message')), $where);
			
			//发送 email通知
			if($this->input->post('sendemail')) {
				$memberinfo = $this->db->select($where);
				foreach ($memberinfo as $v) {
					$this->email->set();
					$this->email->send($v['email'], L('reg_pass'), $this->input->post('message'));
				}
			}
			
			dr_admin_msg(1,L('pass').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}
	
	/**
	 * delete member
	 */
	function delete() {
		if($this->input->post('userid')) {
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$message = stripslashes($this->input->post('message'));
			$where = to_sqls($uidarr, '', 'userid');
			$this->db->delete($where);
						
			dr_admin_msg(1,L('delete').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}

	/**
	 * reject member
	 */
	function reject() {
		if($this->input->post('userid')) {
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$where = to_sqls($uidarr, '', 'userid');
			$res = $this->db->update(array('status'=>4, 'message'=>$this->input->post('message')), $where);
			//发送 email通知
			if($res) {
				if($this->input->post('sendemail')) {
					$memberinfo = $this->db->select($where);
					foreach ($memberinfo as $v) {
						$this->email->set();
						$this->email->send($v['email'], L('reg_reject'), $this->input->post('message'));
					}
				}
			}
			
			dr_admin_msg(1,L('reject').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}

	/**
	 * ignore member
	 */
	function ignore() {
		if($this->input->post('userid')) {		
			$uidarr = $this->input->post('userid') ? $this->input->post('userid') : dr_admin_msg(0,L('illegal_parameters'), HTTP_REFERER);
			$where = to_sqls($uidarr, '', 'userid');
			$res = $this->db->update(array('status'=>2, 'message'=>$this->input->post('message')), $where);
			//发送 email通知
			if($res) {
				if($this->input->post('sendemail')) {
					$memberinfo = $this->db->select($where);
					foreach ($memberinfo as $v) {
						$this->email->set();
						$this->email->send($v['email'], L('reg_ignore'), $this->input->post('message'));
					}
				}
			}
			dr_admin_msg(1,L('ignore').L('operation_success'), HTTP_REFERER);
		} else {
			dr_admin_msg(0,L('operation_failure'), HTTP_REFERER);
		}
	}
		
	/*
	 * change password
	 */
	function _edit_password($userid, $password){
		$userid = intval($userid);
		if($userid < 1) return false;
		if(!is_password($password)) {
			dr_admin_msg(0,L('password_format_incorrect'));
			return false;
		}
		$passwordinfo = password($password);
		return $this->db->update($passwordinfo,array('userid'=>$userid));
	}
	
	private function _checkuserinfo($data, $is_edit=0) {
		if(!is_array($data)){
			dr_admin_msg(0,L('need_more_param'));return false;
		} elseif (!is_username($data['username']) && !$is_edit){
			dr_admin_msg(0,L('username_format_incorrect'));return false;
		} elseif (!isset($data['userid']) && $is_edit) {
			dr_admin_msg(0,L('username_format_incorrect'));return false;
		}  elseif (empty($data['email']) || !is_email($data['email'])){
			dr_admin_msg(0,L('email_format_incorrect'));return false;
		}
		return $data;
	}
		
	private function _checkpasswd($password){
		if (!is_password($password)){
			return false;
		}
		return true;
	}
	
	private function _checkname($username) {
		$username = trim($username);
		if ($this->db->get_one(array('username'=>$username))){
			return false;
		}
		return true;
	}
	
	/**
	 *根据积分算出用户组
	 * @param $point int 积分数
	 */
	private function _get_usergroup_bypoint($point=0) {
		$groupid = 2;
		if(empty($point)) {
			$member_setting = getcache('member_setting');
			$point = $member_setting['defualtpoint'] ? $member_setting['defualtpoint'] : 0;
		}
		$grouplist = getcache('grouplist');
		
		foreach ($grouplist as $k=>$v) {
			$grouppointlist[$k] = $v['point'];
		}
		arsort($grouppointlist);

		//如果超出用户组积分设置则为积分最高的用户组
		if($point > max($grouppointlist)) {
			$groupid = key($grouppointlist);
		} else {
			foreach ($grouppointlist as $k=>$v) {
				if($point >= $v) {
					$groupid = $tmp_k;
					break;
				}
				$tmp_k = $k;
			}
		}
		return $groupid;
	}
	
	/**
	 * check uername status
	 */
	public function checkname_ajax() {
		$username = $this->input->get('username') && trim($this->input->get('username')) ? trim($this->input->get('username')) : exit(0);
		$username = iconv('utf-8', CHARSET, $username);
		
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['username'] == $username){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$where = array('username'=>$username);
				$res = $this->db->get_one($where);
				if($res) {
					exit('0');
				} else {
					exit('1');
				}
			}
 		} else {
			$where = array('username'=>$username);
			$res = $this->db->get_one($where);
			if($res) {
				exit('0');
			} else {
				exit('1');
			}
		}
	}
	
	/**
	 * check email status
	 */
	public function checkemail_ajax() {
		$email = $this->input->get('email') && trim($this->input->get('email')) ? trim($this->input->get('email')) : exit(0);
		
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['email'] == $email){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$where = array('email'=>$email);
				$res = $this->db->get_one($where);
				if($res) {
					exit('0');
				} else {
					exit('1');
				}
			}
 		} else {
			$where = array('email'=>$email);
			$res = $this->db->get_one($where);
			if($res) {
				exit('0');
			} else {
				exit('1');
			}
		}
	}
}
?>