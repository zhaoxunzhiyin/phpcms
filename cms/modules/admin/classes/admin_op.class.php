<?php
defined('IN_CMS') or exit('No permission resources.');

//定义在后台
!defined('IN_ADMIN') && define('IN_ADMIN', TRUE);
class admin_op {
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('admin_model');
	}
	/*
	 * 修改密码
	 */
	public function edit_password($userid, $password){
		$userid = intval($userid);
		if($userid < 1) return false;
		if(!is_password($password))
		{
			showmessage(L('pwd_incorrect'));
			return false;
		}
		$passwordinfo = password($password);
		return $this->db->update($passwordinfo,array('userid'=>$userid));
	}
	/*
	 * 检查用户名重名
	 */	
	public function checkname($username) {
		$username =  trim($username);
		if ($this->db->get_one(array('username'=>$username),'userid')){
			return false;
		}
		return true;
	}	
}
?>