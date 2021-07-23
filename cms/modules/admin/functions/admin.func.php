<?php 
	/**
	 * 检查管理员名称
	 * @param array $data 管理员数据
	 */
	function checkuserinfo($data) {
		if(!is_array($data)){
			showmessage(L('parameters_error'));return false;
		} elseif (!is_username($data['username'])){
			showmessage(L('username_illegal'));return false;
		} elseif (empty($data['email']) || !is_email($data['email'])){
			showmessage(L('email_illegal'));return false;
		} elseif (empty($data['roleid'])){
			return false;
		}
		return $data;
	}
	/**
	 * 检查管理员密码合法性
	 * @param string $password 密码
	 */
	function checkpasswd($password){
		if (!is_password($password)){
			return false;
		}
		return true;
	}

	function system_information($data) {
		echo $data;
	}
?>