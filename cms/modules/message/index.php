<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('foreground','member');//加载foreground 应用类. 自动判断是否登录.
pc_base::load_sys_class('format');
pc_base::load_sys_class('form');

class index extends foreground {
 	private $input,$message_db,$message_group_db,$message_data_db,$_username,$_userid,$_groupid,$menu;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->message_db = pc_base::load_model('message_model');
		$this->message_group_db = pc_base::load_model('message_group_model');
		$this->message_data_db = pc_base::load_model('message_data_model');
		$this->_username = param::get_cookie('_username');
		$this->_userid = param::get_cookie('_userid');
		$this->_groupid = get_memberinfo($this->_userid,'groupid');
		//定义站点ID常量，选择模版使用
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		define("SITEID",$siteid);
		$this->menu_db = pc_base::load_model('member_menu_model');
		$this->menu = $this->menu_db->select(array('display'=>1, 'parentid'=>0), '*', 20, 'listorder');
		pc_base::load_sys_class('service')->assign([
			'menu' => $this->menu,
		]);
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$page = intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$where = array('send_to_id'=>$this->_username,'replyid'=>'0');
 		$infos = $this->message_db->listinfo($where,$order = 'messageid DESC',$page, 10);
 		$infos = new_html_special_chars($infos);
 		$pages = $this->message_db->pages;
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('message', 'inbox');
	}
	
	
	/**
	 * 发送消息 
	 */
	public function send() {
		//判断当前会员，是否可发，短消息．
		$this->message_db->messagecheck($this->_userid);
		if(IS_POST) {
			$username = $this->_username;
			$info = $this->input->post('info');
			$tousername = safe_replace($info['send_to_id']);
			$r = $this->db->get_one(array('username'=>$tousername));
			if(!$r) showmessage(L('user_not_exist','','member'));
			if($tousername==$username){
				showmessage(L('not_myself','','message'));
			}
			$subject = new_html_special_chars($info['subject']);
			$content = new_html_special_chars($info['content']);
			$this->message_db->add_message($tousername,$username,$subject,$content,true);
			showmessage(L('operation_success'),HTTP_REFERER);
		} else {
			pc_base::load_sys_class('service')->assign('show_validator', true);
			pc_base::load_sys_class('service')->assign('show_scroll', true);
			pc_base::load_sys_class('service')->assign('show_header', true);
			pc_base::load_sys_class('service')->display('message', 'send');
		}
	}
	
	/*
	 *判断收件人是否存在 
	 */
	public function public_name() {
		$username = $this->input->get('username') && trim($this->input->get('username')) ? (pc_base::load_config('system', 'charset') == 'gbk' ? iconv('utf-8', 'gbk', trim($this->input->get('username'))) : trim($this->input->get('username'))) : exit('0');
		$member_interface = pc_base::load_app_class('member_interface', 'member');
		if ($username) {
			$username = safe_replace($username);
			//判断收件人不能为自己
			if($username == $this->_username){
				exit('0');
			}
			$data = $member_interface->get_member_info($username, 2);
			if ($data!='-1') {
				exit('1');
			} else {
				exit('0');
			}
		} else {
			exit('0');
		}
		
	}
	
	/**
	 * 发件箱
	 */
	public function outbox() { 
		$where = array('send_from_id'=>$this->_username,'del_type'=>'0');
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->message_db->listinfo($where,$order = 'messageid DESC',$page, $pages = '8');
		$infos = new_html_special_chars($infos);
		$pages = $this->message_db->pages;
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('message', 'outbox');
	}
	
	/**
	 * 收件箱
	 */
	public function inbox() { 
		$where = array('send_to_id'=>$this->_username,'folder'=>'inbox');
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->message_db->listinfo($where,$order = 'messageid DESC',$page, $pages = '8'); 
		$infos = new_html_special_chars($infos);
		if (is_array($infos) && !empty($infos)) {
			foreach ($infos as $infoid=>$info){ 
				$reply_num = $this->message_db->count(array("replyid"=>$info['messageid']));
				$infos[$infoid]['reply_num'] = $reply_num;
	 		}
		}
		$pages = $this->message_db->pages;
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('message', 'inbox');
	}
	
	/**
	 * 群发邮件
	 */
	public function group() {
		//查询自己有权限看的消息
		$where = array('typeid'=>1,'groupid'=>$this->_groupid,'status'=>1);
		$page = $this->input->get('page') && intval($this->input->get('page')) ? intval($this->input->get('page')) : 1;
		$infos = $this->message_group_db->listinfo($where,$order = 'id DESC',$page, $pages = '8');
		$infos = new_html_special_chars($infos);
		$status = array();
		if (is_array($infos) && !empty($infos)) {
			foreach ($infos as $info){
				$d = $this->message_data_db->select(array('userid'=>$this->_userid,'group_message_id'=>$info['id']));
	 			if(!$d){
	 				$status[$info['id']] = 0;//未读 红色
	 			}else {
	 				$status[$info['id']] = 1;
	 			}
			}
		}
 		$pages = $this->message_group_db->pages;
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'status' => $status,
			'pages' => $pages,
		]);
		pc_base::load_sys_class('service')->display('message', 'group');
	}
	
	/**
	 * 删除收件箱-短消息 
	 * @param	intval	$sid	短消息ID，递归删除(修改状态为outbox)
	 */
	public function delete() {
		if((!$this->input->get('messageid') || empty($this->input->get('messageid'))) && (!$this->input->post('messageid') || empty($this->input->post('messageid')))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
			if(is_array($this->input->post('messageid'))){
				foreach($this->input->post('messageid') as $messageid_arr) {
					$messageid_arr = intval($messageid_arr);
					$this->message_db->update(array('folder'=>'outbox'),array('messageid'=>$messageid_arr,'send_to_id'=>$this->_username));
				}
				showmessage(L('operation_success'), HTTP_REFERER);
			}
 		}
	}
	
	/**
	 * 删除发件箱 - 短消息 
	 * @param	intval	$sid	短消息ID，递归删除( 修改状态为del_type =1 )
	 */
	public function del_type() {
		if((!$this->input->post('messageid') || empty($this->input->post('messageid')))) {
			showmessage(L('illegal_parameters'), HTTP_REFERER);
		} else {
				if(is_array($this->input->post('messageid'))){
					foreach($this->input->post('messageid') as $messageid_arr) {
						$messageid_arr = intval($messageid_arr);
 						$this->message_db->update(array('del_type'=>'1'),array('messageid'=>$messageid_arr,'send_from_id'=>$this->_username));
					}
					showmessage(L('operation_success'), HTTP_REFERER);
				} 
		}
	}
	
	/**
	 * 查看短消息 - 对当前用户是否有权限查看
	 */
	public function check_user($messageid,$where){
		$username = $this->_username;
		$messageid = intval($messageid);
		if($where=="to"){
			$result = $this->message_db->get_one(array("send_to_id"=>$username,"messageid"=>$messageid));
		}else{
			$result = $this->message_db->get_one(array("send_from_id"=>$username,"messageid"=>$messageid));
		}
 		if(!$result){//不是当前用户的消息，不能查看
			showmessage('请勿非法访问！', HTTP_REFERER);echo '0';
 		} 
	}
	
	
	/**
	 * 查看短消息
	 */
	public function read() { 
		if((!$this->input->get('messageid') || empty($this->input->get('messageid'))) && (!$this->input->post('messageid') || empty($this->input->post('messageid')))) return false;
		$messageid = $this->input->get('messageid') ? $this->input->get('messageid') : $this->input->post('messageid');
		$messageid = intval($messageid);
		//判断是否属于当前用户
		$check_user = $this->check_user($messageid,'to'); 
		
 		//查看过修改状态 为 0 
		$this->message_db->update(array('status'=>'0'),array('messageid'=>$messageid));
		//查询消息详情
		$infos = $this->message_db->get_one(array('messageid'=>$messageid));
		if($infos['send_from_id']!='SYSTEM') $infos = new_html_special_chars($infos);
		//过滤一下
		$info['send_from_id'] = safe_replace($infos['send_from_id']);
		$info['send_to_id'] = safe_replace($infos['send_to_id']);
		//查询回复消息
		$where = array('replyid'=>$infos['messageid']);
		$reply_infos = $this->message_db->listinfo($where,$order = 'messageid ASC',$page, $pages = '10');
		pc_base::load_sys_class('service')->assign('show_validator', true);
		pc_base::load_sys_class('service')->assign('show_scroll', true);
		pc_base::load_sys_class('service')->assign('show_header', true);
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'reply_infos' => $reply_infos,
		]);
		pc_base::load_sys_class('service')->display('message', 'read');
	}
	
	/**
	 * 查看自己发的短消息
	 */
	public function read_only() { 
		$messageid = $this->input->get('messageid') ? $this->input->get('messageid') : $this->input->post('messageid');
		$messageid = intval($messageid);
		if(!$messageid || empty($messageid)){
			showmessage('请勿非法访问！', HTTP_REFERER);
		}
		//判断是否属于当前用户
		$check_user = $this->check_user($messageid,'from'); 
		
		//查询消息详情
		$infos = $this->message_db->get_one(array('messageid'=>$messageid));
		$infos = new_html_special_chars($infos);
		//查询回复消息
		$where = array('replyid'=>$infos['messageid']);
		$reply_infos = $this->message_db->listinfo($where,$order = 'messageid ASC',$page, $pages = '10');
		pc_base::load_sys_class('service')->assign('show_validator', true);
		pc_base::load_sys_class('service')->assign('show_scroll', true);
		pc_base::load_sys_class('service')->assign('show_header', true);
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
			'reply_infos' => $reply_infos,
		]);
		pc_base::load_sys_class('service')->display('message', 'read_only');
	}
	
	/**
	 * 查看系统短消息
	 */
	public function read_group(){
		if((!$this->input->get('group_id') || empty($this->input->get('group_id'))) && (!$this->input->post('group_id') || empty($this->input->post('group_id')))) return false;
		//查询消息详情
		$infos = $this->message_group_db->get_one(array('id'=>$this->input->get('group_id')));
		$infos = new_html_special_chars($infos);
		if(!is_array($infos))showmessage(L('message_not_exist'),'blank');
		//检查查看表是否有记录,无则向message_data 插入浏览记录
		$check = $this->message_data_db->select(array('userid'=>$this->_userid,'group_message_id'=>$this->input->get('group_id')));
		if(!$check){
			$this->message_data_db->insert(array('userid'=>$this->_userid,'group_message_id'=>$this->input->get('group_id')));
		}
		pc_base::load_sys_class('service')->assign([
			'infos' => $infos,
		]);
		pc_base::load_sys_class('service')->display('message', 'read_group');
	}
	
	/**
	 * 回复短消息 
	 */
	public function reply() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$messageid = intval($info['replyid']);
			//判断当前会员，是否可发，短消息．
			$this->message_db->messagecheck($this->_userid);
			//检查此消息是否有权限回复 
			$this->check_user($messageid,'to');
 			$info['send_from_id'] = $this->_username;
			$info['message_time'] = SYS_TIME;
			$info['status'] = '1';
			$info['folder'] = 'inbox';
			$info['content'] = safe_replace($info['content']);
			$info['subject'] = safe_replace($info['subject']);
			$info['replyid'] = intval($info['replyid']);
			
			//回复人ID进行安全处理
			if(empty($info['send_to_id'])) {
				showmessage(L('user_noempty'),HTTP_REFERER);
			}
			$messageid = $this->message_db->insert($info,true);
			if(!$messageid) return FALSE; 
			showmessage(L('operation_success'),HTTP_REFERER);
			
		} else {
			pc_base::load_sys_class('service')->assign('show_validator', true);
			pc_base::load_sys_class('service')->assign('show_scroll', true);
			pc_base::load_sys_class('service')->assign('show_header', true);
			pc_base::load_sys_class('service')->display('message', 'send');
		}

	}
	 
	
}	
?>	