<?php
/**
 * 
 * @param 会员数据导入类
 */

defined('IN_CMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
class member_import {
	private $import_db, $member_db,$queue;
	
	public function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->import_db = pc_base::load_model('import_model');
		$this->member_db = pc_base::load_model('member_model');
		
	}
	
	/**
	 * 
	 * 会员数据导入 ...
	 * @param $val 用户数据数组
	 * @param $check_email 是否要检测EMAIL
	 */
	function add($info,$check_email) {
		if($check_email==1){
			//执行EMAIL or username 同名检测
			$username = $this->member_db->get_one(array("username"=>$info['username']));
 			if($username) return false;
		}
		//判断是否存在随机码，
		$array = array();
		$array['username'] = $info['username'];
 		$array['email'] = $info['email'];
		$array['regip'] = $info['regip'];
		$array['random'] = $info['encrypt'];
		$array['password'] = $info['password'];
  		
		//插入SSO members 表中
 		$this->member_db->insert($array);
 		$uid = $this->member_db->insert_id();
		if(!$uid) return FALSE; 
		
		//插入v9_member基本表,只需加入phpuid值
		$info['uid'] = $uid;
		$userid = $this->member_db->insert($info);
		if($userid){
			return $userid;
		}
  	}
	  
}
?>