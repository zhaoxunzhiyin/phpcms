<?php
defined('IN_CMS') or exit('No permission resources.');

class admin_op {
	private $input,$db;
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
		if(!$password) return false;
		$passwordinfo = password($password);
		return $this->db->update($passwordinfo,array('userid'=>$userid));
	}
	/*
	 * 检查用户名重名
	 */	
	public function checkname($username) {
		$username = trim($username);
		if ($this->db->get_one(array('username'=>$username),'userid')){
			return false;
		}
		return true;
	}	
}
?>