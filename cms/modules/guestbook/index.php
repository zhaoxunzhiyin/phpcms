<?php
defined('IN_CMS') or exit('No permission resources.');
if (!module_exists(ROUTE_M)) showmessage(L('module_not_exists'));
class index {
	function __construct() {
		pc_base::load_app_func('global');
		$this->input = pc_base::load_sys_class('input');
		$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : get_siteid();
  		define("SITEID",$siteid);
	}
	
	public function init() {
		$siteid = SITEID; 
 		$setting = getcache('guestbook', 'commons');
		$SEO = seo(SITEID, '', L('guestbook'), '', '');
		include template('guestbook', 'index');
	}
	
	 /**
	 *	留言板列表页
	 */
	public function list_type() {
		$siteid = SITEID;
  		$type_id = trim(urldecode($_GET['type_id']));
		$type_id = intval($type_id);
  		if($type_id==""){
 			$type_id ='0';
 		}
   		$setting = getcache('guestbook', 'commons');
		$SEO = seo(SITEID, '', L('guestbook'), '', '');
  		include template('guestbook', 'list_type');
	} 
 	
	 /**
	 *	留言板留言 
	 */
	public function register() { 
 		$siteid = SITEID;
 		if(isset($_POST['dosubmit'])){
 			if($_POST['name']==""){
 				showmessage(L('usename_noempty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
 			}
 			if($_POST['lxqq']==""){
 				showmessage(L('email_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
 			}
 			if($_POST['email']==""){
 				showmessage(L('email_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
 			}
			if($_POST['shouji']==""){
 				showmessage(L('shouji_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
 			}
 			$guestbook_db = pc_base::load_model('guestbook_model');
 			 
			 /*添加用户数据*/
 			$sql = array('siteid'=>$siteid,'typeid'=>$_POST['typeid'],'name'=>$_POST['name'],'sex'=>$_POST['sex'],'lxqq'=>$_POST['lxqq'],'email'=>$_POST['email'],'shouji'=>$_POST['shouji'],'introduce'=>$_POST['introduce'],'addtime'=>time());
 			 
 			$guestbook_db->insert($sql);
 			showmessage(L('add_success'), "?m=guestbook&c=index&siteid=$siteid");
 		}else {
  			$setting = getcache('guestbook', 'commons');
 			if($setting[$siteid]['is_post']=='0'){
 				showmessage(L('suspend_application'), HTTP_REFERER);
 			}
 			$this->type = pc_base::load_model('type_model');
 			$types = $this->type->get_types($siteid);//获取站点下所有留言板分类
 			pc_base::load_sys_class('form', '', 0);
 			$setting = getcache('guestbook', 'commons');
 			$SEO = seo(SITEID, '', L('application_guestbook'), '', '');
   			include template('guestbook', 'register');
 		}
	} 
	
}
?>