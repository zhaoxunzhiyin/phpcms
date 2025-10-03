<?php
/**
 * 会员前台管理中心、账号管理、收藏操作类
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('foreground');
pc_base::load_sys_class('format');
pc_base::load_sys_class('form');

class index extends foreground {

	private $input,$cache,$email,$times_db,$favorite_db,$friend_db,$rid,$att_db,$verify_db,$siteinfo,$menu,$grouplist,$member_model;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->email = pc_base::load_sys_class('email');
		$this->siteinfo = siteinfo($this->memberinfo['siteid']);
		$this->menu_db = pc_base::load_model('member_menu_model');
		$this->menu = $this->menu_db->select(array('display'=>1, 'parentid'=>0), '*', 20, 'listorder');
		$this->grouplist = getcache('grouplist');
		$this->member_model = getcache('member_model', 'commons');
		$this->memberinfo['groupname'] = $this->grouplist[$this->memberinfo['groupid']]['name'];
		$this->memberinfo['grouppoint'] = $this->grouplist[$this->memberinfo['groupid']]['point'];
		pc_base::load_sys_class('service')->assign([
			'memberinfo' => $this->memberinfo,
			'grouplist' => $this->grouplist,
			'member_model' => $this->member_model,
			'siteinfo' => $this->siteinfo,
			'menu' => $this->menu,
		]);
	}

	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		
		//获取头像数组
		$avatar = get_memberavatar($this->memberinfo['userid']);

		pc_base::load_sys_class('service')->assign([
			'avatar' => $avatar,
		]);
		pc_base::load_sys_class('service')->display('member', 'index');
	}
	
	public function register() {
		//获取用户siteid
		$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		//定义站点id常量
		if (!defined('SITEID')) {
			define('SITEID', $siteid);
		}
		
		//加载用户模块配置
		$member_setting = getcache('member_setting');
		if(!$member_setting['allowregister']) {
			showmessage(L('deny_register'), APP_PATH.'index.php?m=member&c=index&a=login');
		}
		//加载短信模块配置
 		$sms_setting_arr = getcache('sms','sms');
		$sms_setting = $sms_setting_arr[$siteid];
		
		header("Cache-control: private");
		if(IS_POST) {
			if($member_setting['register']['code']){//开启验证码
				if (empty(param::get_session('connectid')) && !check_captcha('code')) {
					showmessage(L('code_error'));
				}
			}
			$info = $this->input->post('info');
			$userinfo = array();
			$userinfo['encrypt'] = create_randomstr(10);

			$userinfo['username'] = $this->input->post('username');
			$rt = check_username($userinfo['username']);
			if (!$rt['code']) {
				showmessage($rt['msg'], HTTP_REFERER);
			}
			$userinfo['nickname'] = $this->input->post('nickname') ? $this->input->post('nickname') : '';
			
			$userinfo['email'] = $this->input->post('email') && is_email($this->input->post('email')) ? $this->input->post('email') : exit('0');
			if ($this->db->count(array('email'=>$userinfo['email']))) {
				showmessage(L('email_already_exist'), HTTP_REFERER);
			}
			$userinfo['password'] = dr_safe_password($this->input->post('password'));
			$rt = check_password($userinfo['password'], $userinfo['username']);
			if (!$rt['code']) {
				showmessage($rt['msg'], HTTP_REFERER);
			}
			
			$userinfo['modelid'] = $this->input->post('modelid') ? intval($this->input->post('modelid')) : 10;
			$userinfo['regip'] = ip_info();
			$userinfo['point'] = $member_setting['defualtpoint'] ? $member_setting['defualtpoint'] : 0;
			$userinfo['amount'] = $member_setting['defualtamount'] ? $member_setting['defualtamount'] : 0;
			$userinfo['regdate'] = $userinfo['lastdate'] = SYS_TIME;
			$userinfo['siteid'] = $siteid;
			$userinfo['connectid'] = param::get_session('connectid');
			$userinfo['from'] = param::get_session('from');
			//手机强制验证
			
			if($member_setting['mobile_checktype']=='1'){
				//取用户手机号
				$mobile_verify = $this->input->post('mobile_verify') ? intval($this->input->post('mobile_verify')) : '';
				if($mobile_verify=='') showmessage(L('请提供正确的手机验证码！'), HTTP_REFERER);
 				$sms_report_db = pc_base::load_model('sms_report_model');
				$sys_cache_sms = defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 300;
				$posttime = SYS_TIME-$sys_cache_sms;
				$where = "`id_code`='$mobile_verify' AND `posttime`>'$posttime'";
				$r = $sms_report_db->get_one($where,'*','id DESC');
 				if(!empty($r)){
					$userinfo['mobile'] = $r['mobile'];
				}else{
					showmessage(L('未检测到正确的手机号码！'), HTTP_REFERER);
				}
 			}elseif($member_setting['mobile_checktype']=='2'){
				//获取验证码，直接通过POST，取mobile值
				$userinfo['mobile'] = $this->input->post('mobile') ? $this->input->post('mobile') : '';
			}
			if($userinfo['mobile']!=""){
				if(!preg_match('/^1([0-9]{10})$/',$userinfo['mobile'])) {
					showmessage(L('请提供正确的手机号码！'), HTTP_REFERER);
				}
				if ($this->db->count(array('mobile'=>$userinfo['mobile']))) {
					showmessage(L('手机号码已经注册'), HTTP_REFERER);
				}
			}
			param::del_session('connectid');
			param::del_session('from');
			
			if($member_setting['enablemailcheck']) {	//是否需要邮件验证
				$userinfo['groupid'] = 7;
			} elseif($member_setting['registerverify']) {	//是否需要管理员审核
				if($member_setting['choosemodel']) {
					require_once CACHE_MODEL_PATH.'member_input.class.php';
					require_once CACHE_MODEL_PATH.'member_update.class.php';
					$member_input = new member_input($userinfo['modelid']);
					if ($info) {
						$info = array_map('new_html_special_chars',$info);
					}
					$modelinfo_str = $member_input->get($info);
				}
				$this->verify_db = pc_base::load_model('member_verify_model');
				unset($userinfo['lastdate'],$userinfo['connectid'],$userinfo['from']);
				$userinfo['modelinfo'] = array2string($modelinfo_str);
				$this->verify_db->insert($userinfo);
				showmessage(L('operation_success'), APP_PATH.'index.php?m=member&c=index&a=register&t=3');
			} else {
				//查看当前模型是否开启了短信验证功能
				$model_field_cache = getcache('model_field_'.$userinfo['modelid'],'model');
				if(isset($model_field_cache['mobile']) && $model_field_cache['mobile']['disabled']==0) {
					$mobile = $info['mobile'];
					if(!preg_match('/^1([0-9]{10})$/',$mobile)) showmessage(L('input_right_mobile'));
					$sms_report_db = pc_base::load_model('sms_report_model');
					$sys_cache_sms = defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 300;
					$posttime = SYS_TIME-$sys_cache_sms;
					$where = "`mobile`='$mobile' AND `posttime`>'$posttime'";
					$r = $sms_report_db->get_one($where);
					if(!$r || $r['id_code']!=$this->input->post('mobile_verify')) showmessage(L('error_sms_code'));
				}
				$userinfo['groupid'] = $this->_get_usergroup_bypoint($userinfo['point']);
			}
			//附表信息验证 通过模型获取会员信息
			if($member_setting['choosemodel']) {
				require_once CACHE_MODEL_PATH.'member_input.class.php';
				require_once CACHE_MODEL_PATH.'member_update.class.php';
				$member_input = new member_input($userinfo['modelid']);
				if ($info) {
					$info = array_map('new_html_special_chars',$info);
				}
				$user_model_info = $member_input->get($info);
			}
			$password = $userinfo['password'];
			$userinfo['password'] = password($userinfo['password'], $userinfo['encrypt']);
			$login_attr = md5(SYS_KEY.$userinfo['password'].(isset($userinfo['login_attr']) ? $userinfo['login_attr'] : ''));
			$userid = $this->db->insert($userinfo, 1);
			if($member_setting['choosemodel']) {	//如果开启选择模型
				$user_model_info['userid'] = $userid;
				//插入会员模型数据
				$this->db->set_model($userinfo['modelid']);
				$this->db->insert($user_model_info);
			}
			
			if($userid > 0) {
				// 回调钩子
				pc_base::load_sys_class('hooks')::trigger('member_login_before', $userinfo);
				//执行登录操作
				$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
				if($userinfo['groupid'] == 7) {
					param::set_cookie('_username', $userinfo['username'], $cookietime);
					param::set_cookie('email', $userinfo['email'], $cookietime);							
				} else {
					$cms_auth = sys_auth($userid."\t".$userinfo['password'], 'ENCODE', get_auth_key('login'));
					param::set_cookie('auth', $cms_auth, $cookietime);
					param::set_cookie('_userid', $userid, $cookietime);
					param::set_cookie('_login_attr', $login_attr, $cookietime);
					param::set_cookie('_username', $userinfo['username'], $cookietime);
					param::set_cookie('_nickname', $userinfo['nickname'], $cookietime);
					param::set_cookie('_groupid', $userinfo['groupid'], $cookietime);
				}
				$member['userid'] = $userid;
				// 登录后的钩子
				pc_base::load_sys_class('hooks')::trigger('member_login_after', $member);
				$this->clear_cache($member['userid']);
			}
			//如果需要邮箱认证
			if($member_setting['enablemailcheck']) {
				$code = sys_auth($userid.'|'.microtime(true), 'ENCODE', get_auth_key('email'));
				$url = APP_PATH."index.php?m=member&c=index&a=register&code=$code&verify=1";
				$message = $member_setting['registerverifymessage'];
				$message = str_replace(array('{click}','{url}','{username}','{email}','{password}'), array('<a href="'.$url.'">'.L('please_click').'</a>',$url,$userinfo['username'],$userinfo['email'],$password), $message);
				$this->email->set();
				$this->email->send($userinfo['email'], L('reg_verify_email'), $message);
				//设置当前注册账号COOKIE，为第二步重发邮件所用
				param::set_session('_regusername', $userinfo['username']);
				param::set_session('_reguserid', $userid);
				showmessage(L('operation_success'), APP_PATH.'index.php?m=member&c=index&a=register&t=2');
			}
			// 注册后的钩子
			pc_base::load_sys_class('hooks')::trigger('member_register_after', $userinfo);
			showmessage(L('register').L('success'), APP_PATH.'index.php?m=member&c=index');
		} else {
			if(!empty($this->input->get('verify'))) {
				$code = $this->input->get('code') ? trim($this->input->get('code')) : showmessage(L('operation_failure'), APP_PATH.'index.php?m=member&c=index');
				$code_res = sys_auth($code, 'DECODE', get_auth_key('email'));
				$code_arr = explode('|', $code_res);
				$userid = isset($code_arr[0]) ? $code_arr[0] : '';
				$userid = is_numeric($userid) ? $userid : showmessage(L('operation_failure'), APP_PATH.'index.php?m=member&c=index');

				$this->db->update(array('groupid'=>$this->_get_usergroup_bypoint()), array('userid'=>$userid));
				showmessage(L('operation_success'), APP_PATH.'index.php?m=member&c=index');
			} elseif(!empty($this->input->get('protocol'))) {
				pc_base::load_sys_class('service')->assign([
					'member_setting' => $member_setting,
				]);
				pc_base::load_sys_class('service')->display('member', 'protocol');
			} else {
				//过滤非当前站点会员模型
				foreach($this->member_model as $k=>$v) {
					if($v['siteid']!=$siteid || $v['disabled']) {
						unset($this->member_model[$k]);
					}
				}
				if(empty($this->member_model)) {
					showmessage(L('site_have_no_model').L('deny_register'), HTTP_REFERER);
				}
				//是否开启选择会员模型选项
				if($member_setting['choosemodel']) {
					$first_model = array_pop(array_reverse($this->member_model));
					$modelid = $this->input->get('modelid') && in_array($this->input->get('modelid'), array_keys($this->member_model)) ? intval($this->input->get('modelid')) : $first_model['modelid'];

					if(array_key_exists($modelid, $this->member_model)) {
						//获取会员模型表单
						require CACHE_MODEL_PATH.'member_form.class.php';
						$member_form = new member_form($modelid);
						$this->db->set_model($modelid);
						$forminfos = $forminfos_arr = $member_form->get();

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
						
						$formValidator = $member_form->formValidator;
					}
				}
				$description = $this->member_model[$modelid]['description'];
				
				pc_base::load_sys_class('service')->assign([
					'modelid' => $modelid,
					'formValidator' => $formValidator,
					'siteid' => $siteid,
					'description' => $description,
					'modellist' => $this->member_model,
					'forminfos' => $forminfos,
					'member_setting' => $member_setting,
					'sms_setting' => $sms_setting,
				]);
				pc_base::load_sys_class('service')->display('member', 'register');
			}
		}
	}
	
	/*
	 * 测试邮件配置
	 */
	public function send_newmail() {
		$_username = param::get_session('_regusername');
		$_userid = param::get_session('_reguserid');
		$newemail = $this->input->get('newemail');

		if($newemail=='' || !is_email($newemail)){//邮箱为空，直接返回错误
			return '2';
		}
		//验证userid和username是否匹配
		$r = $this->db->get_one(array('userid'=>intval($_userid)));
		if($r[username]!=$_username){
			return '2';
		}
		
		//验证邮箱格式
		$code = sys_auth($_userid.'|'.microtime(true), 'ENCODE', get_auth_key('email'));
		$url = APP_PATH."index.php?m=member&c=index&a=register&code=$code&verify=1";
		
		//读取配置获取验证信息
		$member_setting = getcache('member_setting');
		$message = $member_setting['registerverifymessage'];
		$message = str_replace(array('{click}','{url}','{username}','{email}','{password}'), array('<a href="'.$url.'">'.L('please_click').'</a>',$url,$_username,$newemail,$password), $message);
		$this->email->set();
 		if($this->email->send($newemail, L('reg_verify_email'), $message)){
			//更新新的邮箱，用来验证
 			$this->db->update(array('email'=>$newemail), array('userid'=>$_userid));
			$return = '1';
		}else{
			$return = '2';
		}
		echo $return;
	}
	
	public function account_manage() {
		//获取头像数组
		$avatar = get_memberavatar($this->memberinfo['userid']);

		//获取用户模型数据
		$this->db->set_model($this->memberinfo['modelid']);
		$member_modelinfo_arr = $this->db->get_one(array('userid'=>$this->memberinfo['userid']));
		$model_info = getcache('model_field_'.$this->memberinfo['modelid'], 'model');
		foreach($model_info as $k=>$v) {
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

		pc_base::load_sys_class('service')->assign([
			'avatar' => $avatar,
			'member_modelinfo' => $member_modelinfo,
		]);
		pc_base::load_sys_class('service')->display('member', 'account_manage');
	}

	public function account_manage_avatar() {
		//获取头像数组
		$avatar = get_memberavatar($this->memberinfo['userid']);
		pc_base::load_sys_class('service')->assign([
			'avatar' => $avatar,
		]);
		pc_base::load_sys_class('service')->display('member', 'account_manage_avatar');
	}
	
	/**
	 * 上传头像处理
	 * 传入头像压缩包，解压到指定文件夹后删除非图片文件
	 */
	public function uploadavatar() {
		//获取用户siteid
		$siteid = $this->memberinfo['siteid'] ? $this->memberinfo['siteid'] : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		$this->rid = md5(FC_NOW_URL.$this->input->get_user_agent().$this->input->ip_address().intval($this->memberinfo['userid']));
		//定义站点id常量
		if (!defined('SITEID')) {
			define('SITEID', $siteid);
		}
		$memberinfo = $this->memberinfo;
		pc_base::load_sys_class('upload','',0);
		$upload = new upload('member',0,$siteid);
		$upload->set_userid($this->memberinfo['userid']);
		header("content-type:text/html;charset=utf-8");
		$content = trim($this->input->post('img'));
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/i', $content, $result)){
			$type = $result[2];
			if(in_array($type, array('pjpeg', 'jpeg', 'jpg', 'gif', 'bmp', 'png', 'webp'))){
				$content = base64_decode(str_replace($result[1], '', $content));
				if (strlen($content) > 30000000) {
					dr_json(0, L('图片太大了'));
				}
				// 头像上传成功之前
				pc_base::load_sys_class('hooks')::trigger('upload_avatar_before', [
					'member' => $this->memberinfo,
					'base64_image' => $content,
				]);
				$rt = $upload->base64_image(array(
					'content' => $content,
					'file_exts' => $type,
					'attachment' => $upload->get_attach_member(SYS_ATTACHMENT_SAVE_ID, 0),
				));
				if (!$rt['code']) {
					exit(dr_array2string($rt));
				}
				
				// 附件归档
				$rt['data']['isadmin'] = 0;
				$data = $upload->save_data($rt['data'], 'avatar:'.$this->rid);
				if (!$data['code']) {
					exit(dr_array2string($data));
				}
				if($rt && $data){
					$this->att_db = pc_base::load_model('attachment_model');
					$this->db->update(array('avatar'=>$data['code']), array('userid'=>$this->memberinfo['userid']));
					$this->att_db->update(array('status'=>0), array('userid'=>$this->memberinfo['userid'],'status'=>1));
					$this->att_db->update(array('status'=>1), array('aid'=>$data['code']));
					// 头像上传成功之后
					pc_base::load_sys_class('hooks')::trigger('upload_avatar_after', [
						'member' => $this->memberinfo,
						'base64_image' => $content,
					]);
					$this->clear_cache($this->memberinfo['userid']);
					dr_json(1, L('图片上传成功'), $rt['data']);
				}
			}else{
				dr_json(0, L('图片上传类型错误'));
			}
		}else{
			dr_json(0, L('头像内容不规范'));
		}
	}
	
	public function account_manage_info() {
		$member_setting = getcache('member_setting');
		if(IS_POST) {
			$this->clear_cache($this->memberinfo['userid'], $this->memberinfo['username']);
			$info = $this->input->post('info');
			//更新用户昵称
			$nickname = $this->input->post('nickname') ? trim($this->input->post('nickname')) : '';
			$nickname = safe_replace($nickname);
			if($nickname) {
				$this->db->update(array('nickname'=>$nickname), array('userid'=>$this->memberinfo['userid']));
				$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
				param::set_cookie('_nickname', $nickname, $cookietime);
			}
			require_once CACHE_MODEL_PATH.'member_input.class.php';
			require_once CACHE_MODEL_PATH.'member_update.class.php';
			$member_input = new member_input($this->memberinfo['modelid']);
			$modelinfo = $member_input->get($info);

			$this->db->set_model($this->memberinfo['modelid']);
			$membermodelinfo = $this->db->get_one(array('userid'=>$this->memberinfo['userid']));
			if(!empty($membermodelinfo)) {
				$this->db->update($modelinfo, array('userid'=>$this->memberinfo['userid']));
			} else {
				$modelinfo['userid'] = $this->memberinfo['userid'];
				$this->db->insert($modelinfo);
			}
			$this->cache->set_data('member-info-'.$this->memberinfo['userid'], '', 1);
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			//获取会员模型表单
			require CACHE_MODEL_PATH.'member_form.class.php';
			$member_form = new member_form($this->memberinfo['modelid']);
			$this->db->set_model($this->memberinfo['modelid']);
			
			$membermodelinfo = $this->db->get_one(array('userid'=>$this->memberinfo['userid']));
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
						
			$formValidator = $member_form->formValidator;
			pc_base::load_sys_class('service')->assign([
				'formValidator' => $formValidator,
				'forminfos' => $forminfos,
			]);
			pc_base::load_sys_class('service')->display('member', 'account_manage_info');
		}
	}
	
	public function account_manage_password() {
		if(IS_POST) {
			$info = $this->input->post('info');
			$updateinfo = array();
			if($this->memberinfo['password'] != password($info['password'], $this->memberinfo['encrypt'])) {
				showmessage(L('old_password_incorrect'), HTTP_REFERER);
			}
			if ($info['password'] == $info['newpassword']) {
				showmessage(L('原密码不能与新密码相同'));
			}
			//修改会员邮箱
			if($this->memberinfo['email'] != $info['email'] && is_email($info['email'])) {
				if ($this->db->count(array('userid<>'=>$this->memberinfo['userid'], 'email'=>$info['email']))) {
					showmessage(L('email_already_exist'), HTTP_REFERER);
				}
				$email = $info['email'];
				$updateinfo['email'] = $info['email'];
			} else {
				$email = '';
			}
			$rt = check_password($info['newpassword'], $this->memberinfo['username']);
			if (!$rt['code']) {
				showmessage($rt['msg'], HTTP_REFERER);
			}
			$newpassword = password($info['newpassword'], $this->memberinfo['encrypt']);
			$updateinfo['password'] = $newpassword;
			
			if($this->db->update($updateinfo, array('userid'=>$this->memberinfo['userid']))) {
				// 钩子
				pc_base::load_sys_class('hooks')::trigger('member_edit_password_after', $this->memberinfo);
				$this->clear_cache($this->memberinfo['userid']);
			}

			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			pc_base::load_sys_class('service')->assign('show_validator', true);
			pc_base::load_sys_class('service')->display('member', 'account_manage_password');
		}
	}
	//更换手机号码
	public function account_change_mobile() {
		if(IS_POST) {
			if(!is_password($this->input->post('password'))) {
				showmessage(L('password_format_incorrect'), HTTP_REFERER);
			}
			if($this->memberinfo['password'] != password($this->input->post('password'), $this->memberinfo['encrypt'])) {
				showmessage(L('old_password_incorrect'));
			}
			$sms_report_db = pc_base::load_model('sms_report_model');
			$mobile_verify = $this->input->post('mobile_verify');
			$mobile = $this->input->post('mobile');
			if($mobile){
				if(!preg_match('/^1([0-9]{10})$/',$mobile)) exit('check phone error');
				if ($mobile && $this->db->count(array('userid<>'=>$memberinfo['userid'], 'mobile'=>$mobile))) {
					showmessage(L('手机号码已经注册'), HTTP_REFERER);
				}
				$sys_cache_sms = defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 300;
				$posttime = SYS_TIME-$sys_cache_sms;
				$where = "`mobile`='$mobile' AND `send_userid`='".$memberinfo['userid']."' AND `posttime`>'$posttime'";
				$r = $sms_report_db->get_one($where,'id,id_code','id DESC');
				if($r && $r['id_code']==$mobile_verify) {
					$sms_report_db->update(array('id_code'=>''),$where);
					$this->db->update(array('mobile'=>$mobile),array('userid'=>$memberinfo['userid']));
					showmessage(L('手机号码更新成功！'),'?m=member&c=index&a=account_change_mobile&t=1');
				} else {
					showmessage(L('短信验证码错误！请重新获取！'), HTTP_REFERER);
				}
			}else{
				showmessage(L('短信验证码已过期！请重新获取！'), HTTP_REFERER);
			}
		} else {
			pc_base::load_sys_class('service')->display('member', 'account_change_mobile');
		}
	}

	//选择密码找回方式
	public function public_get_password_type() {
		pc_base::load_sys_class('service')->assign('siteid', intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid()));
		pc_base::load_sys_class('service')->display('member', 'get_password_type');
	}

	public function account_manage_upgrade() {
		if(empty($this->grouplist[$this->memberinfo['groupid']]['allowupgrade'])) {
			showmessage(L('deny_upgrade'), HTTP_REFERER);
		}
		if($this->input->post('upgrade_type') && intval($this->input->post('upgrade_type')) < 0) {
			showmessage(L('operation_failure'), HTTP_REFERER);
		}

		if($this->input->post('upgrade_date') && intval($this->input->post('upgrade_date')) < 0) {
			showmessage(L('operation_failure'), HTTP_REFERER);
		}

		if(IS_POST) {
			$groupid = $this->input->post('groupid') ? intval($this->input->post('groupid')) : showmessage(L('operation_failure'), HTTP_REFERER);
			
			$upgrade_type = $this->input->post('upgrade_type') ? intval($this->input->post('upgrade_type')) : showmessage(L('operation_failure'), HTTP_REFERER);
			$upgrade_date = !empty($this->input->post('upgrade_date')) ? intval($this->input->post('upgrade_date')) : showmessage(L('operation_failure'), HTTP_REFERER);

			//消费类型，包年、包月、包日，价格
			$typearr = array($this->grouplist[$groupid]['price_y'], $this->grouplist[$groupid]['price_m'], $this->grouplist[$groupid]['price_d']);
			//消费类型，包年、包月、包日，时间
			$typedatearr = array('366', '31', '1');
			//消费的价格
			$cost = $typearr[$upgrade_type]*$upgrade_date;
			//购买时间
			$buydate = $typedatearr[$upgrade_type]*$upgrade_date*86400;
			$overduedate = $this->memberinfo['overduedate'] > SYS_TIME ? ($this->memberinfo['overduedate']+$buydate) : (SYS_TIME+$buydate);

			if($this->memberinfo['amount'] >= $cost) {
				$this->db->update(array('groupid'=>$groupid, 'overduedate'=>$overduedate, 'vip'=>1), array('userid'=>$this->memberinfo['userid']));
				//消费记录
				pc_base::load_app_class('spend','pay',0);
				spend::amount($cost, L('allowupgrade'), $this->memberinfo['userid'], $this->memberinfo['username']);
				showmessage(L('operation_success'), APP_PATH.'index.php?m=member&c=index&a=init');
			} else {
				showmessage(L('operation_failure'), HTTP_REFERER);
			}

		} else {
			//获取头像数组
			$avatar = get_memberavatar($this->memberinfo['userid']);
			pc_base::load_sys_class('service')->assign([
				'groupid' => $this->input->get('groupid'),
				'avatar' => $avatar,
			]);
			pc_base::load_sys_class('service')->display('member', 'account_manage_upgrade');
		}
	}
	
	public function login() {
		//获取用户siteid
		$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		//定义站点id常量
		if (!defined('SITEID')) {
			define('SITEID', $siteid);
		}
		
		//加载用户模块配置
		$member_setting = getcache('member_setting');
		$maxloginfailedtimes = isset($member_setting['maxloginfailedtimes']) ? (int)$member_setting['maxloginfailedtimes'] : '';
		$syslogintimes = isset($member_setting['syslogintimes']) ? (int)$member_setting['syslogintimes'] : 10;
		//加载短信模块配置
 		$sms_setting_arr = getcache('sms','sms');
		$sms_setting = $sms_setting_arr[$siteid];
		
		if(IS_POST) {
			if($member_setting['login']['code']){//开启验证码
				if (empty(param::get_session('connectid')) && !check_captcha('code')) {
					showmessage(L('code_error'));
				}
			}
			
			$username = $this->input->post('username');
			$member['username'] = $username;
			// 回调钩子
			pc_base::load_sys_class('hooks')::trigger('member_login_before', $member);
			$username = isset($username) ? dr_safe_username($username) : showmessage(L('username_empty'), HTTP_REFERER);
			$password = $this->input->post('password') && trim($this->input->post('password')) ? urldecode(trim($this->input->post('password'))) : showmessage(L('password_empty'), HTTP_REFERER);
			is_badword($this->input->post('password'))==false ? trim($this->input->post('password')) : showmessage(L('password_format_incorrect'), HTTP_REFERER);
			
			//密码错误剩余重试次数
			$this->times_db = pc_base::load_model('times_model');
			$rtime = $this->times_db->get_one(array('username'=>$username,'isadmin'=>0));
			if ($rtime) {
				if ($maxloginfailedtimes) {
					if ($syslogintimes && (int)$rtime['logintime'] && SYS_TIME - (int)$rtime['logintime'] > ($syslogintimes * 60)) {
						// 超过时间了
						$this->times_db->delete(array('username'=>$username,'isadmin'=>0));
					}
				}
				
				if ($maxloginfailedtimes) {
					if((int)$rtime['times'] && (int)$rtime['times'] >= $maxloginfailedtimes) {
						showmessage(L('失败次数已达到'.$rtime['times'].'次，已被禁止登录，请'.$syslogintimes.'分钟后登录'));
					}
				}
			}
			
			//查询帐号
			$r = find_member_info($username);

			if(!$r) showmessage(L('user_not_exist'),APP_PATH.'index.php?m=member&c=index&a=login');
			
			//如果用户被锁定
			if($r['islock']) {
				showmessage(L('user_is_lock'));
			}
			
			//验证用户密码
			$password = md5(md5(trim($password)).$r['encrypt']);
			if($r['password'] != $password) {
				$ip = ip();
				if ($maxloginfailedtimes) {
					if($rtime && $rtime['times'] < $maxloginfailedtimes) {
						$times = $maxloginfailedtimes-intval($rtime['times']);
						$this->times_db->update(array('ip'=>$ip,'isadmin'=>0,'times'=>'+=1'),array('username'=>$username));
					} else {
						$this->times_db->delete(array('username'=>$username,'isadmin'=>0));
						$this->times_db->insert(array('username'=>$username,'ip'=>$ip,'isadmin'=>0,'logintime'=>SYS_TIME,'times'=>1));
						$times = $maxloginfailedtimes;
					}
					showmessage(L('密码错误，您还有'.$times.'次尝试机会！'), APP_PATH.'index.php?m=member&c=index&a=login', 3000);
				} else {
					showmessage(L('password_error'), APP_PATH.'index.php?m=member&c=index&a=login', 3000);
				}
			}
			$this->times_db->delete(array('username'=>$username,'isadmin'=>0));
			
			$userid = $r['userid'];
			$groupid = $r['groupid'];
			$username = $r['username'];
			$nickname = empty($r['nickname']) ? $username : $r['nickname'];
			$login_attr = md5(SYS_KEY.$r['password'].(isset($r['login_attr']) ? $r['login_attr'] : ''));
			
			$updatearr = array('lastip'=>ip(), 'lastdate'=>SYS_TIME, 'loginnum'=>$r['loginnum']+1);
			//vip过期，更新vip和会员组
			if($r['overduedate'] < SYS_TIME) {
				$updatearr['vip'] = 0;
			}		

			//检查用户积分，更新新用户组，除去邮箱认证、禁止访问、游客组用户、vip用户，如果该用户组不允许自助升级则不进行该操作		
			if($r['point'] >= 0 && !in_array($r['groupid'], array('1', '7', '8')) && empty($r['vip'])) {
				if(!empty($this->grouplist[$r['groupid']]['allowupgrade'])) {	
					$check_groupid = $this->_get_usergroup_bypoint($r['point']);
	
					if($check_groupid != $r['groupid']) {
						$updatearr['groupid'] = $groupid = $check_groupid;
					}
				}
			}

			//如果是connect用户
			if(!empty(param::get_session('connectid'))) {
				$updatearr['connectid'] = param::get_session('connectid');
			}
			if(!empty(param::get_session('from'))) {
				$updatearr['from'] = param::get_session('from');
			}
			param::del_session('connectid');
			param::del_session('from');
						
			$this->db->update($updatearr, array('userid'=>$userid));
			
			$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
			
			$cms_auth = sys_auth($userid."\t".$password, 'ENCODE', get_auth_key('login'));
			
			param::set_cookie('auth', $cms_auth, $cookietime);
			param::set_cookie('_userid', $userid, $cookietime);
			param::set_cookie('_login_attr', $login_attr, $cookietime);
			param::set_cookie('_username', $username, $cookietime);
			param::set_cookie('_groupid', $groupid, $cookietime);
			param::set_cookie('_nickname', $nickname, $cookietime);
			$member['userid'] = $userid;
			// 登录后的钩子
			pc_base::load_sys_class('hooks')::trigger('member_login_after', $member);
			$this->clear_cache($member['userid']);
			$forward = $this->input->post('forward') && !empty($this->input->post('forward')) ? urldecode($this->input->post('forward')) : APP_PATH.'index.php?m=member&c=index';
			showmessage(L('login_success'), $forward);
		} else {
			$setting = pc_base::load_config('system');
			$forward = $this->input->get('forward') && trim($this->input->get('forward')) ? urlencode($this->input->get('forward')) : '';
			
			$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
			$siteinfo = siteinfo($siteid);

			pc_base::load_sys_class('service')->assign([
				'member_setting' => $member_setting,
				'sms_setting' => $sms_setting,
				'siteid' => $siteid,
				'siteinfo' => $siteinfo,
				'setting' => $setting,
				'forward' => $forward,
			]);
			pc_base::load_sys_class('service')->display('member', 'login');
		}
	}
	
	/**
	 * 授权登录用户中心跳转
	 */
	public function alogin() {
		$member_setting = getcache('member_setting');
		$code = $this->cache->get_data('admin_login_member');
		if (!$code) {
			showmessage(L('没有获取到会话信息'));
		}
		param::set_cookie('auth', '');
		param::set_cookie('_userid', '');
		param::set_cookie('_login_attr', '');
		param::set_cookie('_username', '');
		param::set_cookie('_groupid', '');
		param::set_cookie('_nickname', '');
		if ($code) {
			//如果用户被锁定
			if($code['islock']) {
				showmessage(L('user_is_lock'), 'close');
			}
			// 回调钩子
			pc_base::load_sys_class('hooks')::trigger('member_login_before', $code);
			$userid = $code['userid'];
			$groupid = $code['groupid'];
			$username = $code['username'];
			$password = $code['password'];
			$nickname = empty($code['nickname']) ? $username : $code['nickname'];
			$login_attr = md5(SYS_KEY.$code['password'].(isset($code['login_attr']) ? $code['login_attr'] : ''));
			$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
			$cms_auth = sys_auth($userid."\t".$password, 'ENCODE', get_auth_key('login'));
			param::set_cookie('auth', $cms_auth, $cookietime);
			param::set_cookie('_userid', $userid, $cookietime);
			param::set_cookie('_login_attr', $login_attr, $cookietime);
			param::set_cookie('_username', $username, $cookietime);
			param::set_cookie('_groupid', $groupid, $cookietime);
			param::set_cookie('_nickname', $nickname, $cookietime);
			$member['userid'] = $userid;
			// 登录后的钩子
			pc_base::load_sys_class('hooks')::trigger('member_login_after', $member);
			$this->clear_cache($member['userid']);
		}
		$this->cache->clear('admin_login_member');
		dr_redirect(APP_PATH.'index.php?m=member&c=index');
	}

	public function logout() {
		pc_base::load_sys_class('hooks')::trigger('member_logout', $this->memberinfo);
		$config = getcache('common', 'commons');
		if (isset($config['login_use']) && dr_in_array('member', $config['login_use'])) {
			$this->cache->del_auth_data('member_option_'.param::get_cookie('_userid'), 1);
		}
		param::set_cookie('auth', '');
		param::set_cookie('_userid', '');
		param::set_cookie('_login_attr', '');
		param::set_cookie('_username', '');
		param::set_cookie('_groupid', '');
		param::set_cookie('_nickname', '');
		
		$forward = $this->input->get('forward') && trim($this->input->get('forward')) ? $this->input->get('forward') : APP_PATH.'index.php?m=member&c=index&a=login';
		showmessage(L('logout_success'), $forward);
	}

	// 清理指定用户缓存
	public function clear_cache($uid, $username = '') {
		$this->cache->clear('member-info-'.$uid);
		$username && $this->cache->clear('member-info-name-'.$username);
	}

	/**
	 * 我的收藏
	 * 
	 */
	public function favorite() {
		$this->favorite_db = pc_base::load_model('favorite_model');
		if($this->input->get('id') && trim($this->input->get('id'))) {
			$this->favorite_db->delete(array('userid'=>$this->memberinfo['userid'], 'id'=>intval($this->input->get('id'))));
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			$page = $this->input->get('page') && trim($this->input->get('page')) ? intval($this->input->get('page')) : 1;
			$favoritelist = $this->favorite_db->listinfo(array('userid'=>$this->memberinfo['userid']), 'id DESC', $page, 10);
			$pages = $this->favorite_db->pages;

			pc_base::load_sys_class('service')->assign([
				'page' => $page,
				'favoritelist' => $favoritelist,
				'pages' => $pages,
			]);
			pc_base::load_sys_class('service')->display('member', 'favorite_list');
		}
	}
	
	/**
	 * 我的好友
	 */
	public function friend() {
		$this->friend_db = pc_base::load_model('friend_model');
		if($this->input->get('friendid')) {
			$this->friend_db->delete(array('userid'=>$this->memberinfo['userid'], 'friendid'=>intval($this->input->get('friendid'))));
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
	
			//我的好友列表userid
			$page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
			$friendids = $this->friend_db->listinfo(array('userid'=>$this->memberinfo['userid']), '', $page, 10);
			$pages = $this->friend_db->pages;
			foreach($friendids as $k=>$v) {
				$friendlist[$k]['friendid'] = $v['friendid'];
				$friendlist[$k]['is'] = $v['is'];
			}
			pc_base::load_sys_class('service')->assign([
				'page' => $page,
				'friendlist' => $friendlist,
				'pages' => $pages,
			]);
			pc_base::load_sys_class('service')->display('member', 'friend_list');
		}
	}
	
	/**
	 * 积分兑换
	 */
	public function change_credit() {
		$memberinfo = $this->memberinfo;
		//加载用户模块配置
		$member_setting = getcache('member_setting');
		
		if($this->input->post('dosubmit')) {
			//本系统积分兑换数
			$fromvalue = intval($this->input->post('fromvalue'));
			//本系统积分类型
			$from = $this->input->post('from');
			$toappid_to = explode('_', $this->input->post('to'));
			//目标系统appid
			$toappid = $toappid_to[0];
			//目标系统积分类型
			$to = $toappid_to[1];
			if($from == 1) {
				if($memberinfo['point'] < $fromvalue) {
					showmessage(L('need_more_point'), HTTP_REFERER);
				}
			} elseif($from == 2) {
				if($memberinfo['amount'] < $fromvalue) {
					showmessage(L('need_more_amount'), HTTP_REFERER);
				}
			} else {
				showmessage(L('credit_setting_error'), HTTP_REFERER);
			}
		} elseif($this->input->post('buy')) {
			if(!is_numeric($this->input->post('money')) || $this->input->post('money') < 0) {
				showmessage(L('money_error'), HTTP_REFERER);
			} else {
				$money = intval($this->input->post('money'));
			}
			
			if($memberinfo['amount'] < $money) {
				showmessage(L('short_of_money'), HTTP_REFERER);
			}
			//此处比率读取用户配置
			$point = $money*$member_setting['rmb_point_rate'];
			$this->db->update(array('point'=>"+=$point"), array('userid'=>$memberinfo['userid']));
			//加入消费记录，同时扣除金钱
			pc_base::load_app_class('spend','pay',0);
			spend::amount($money, L('buy_point'), $memberinfo['userid'], $memberinfo['username']);
			showmessage(L('operation_success'), HTTP_REFERER);
		} else {
			$credit_list = pc_base::load_config('credit');
			
			pc_base::load_sys_class('service')->assign([
				'member_setting' => $member_setting,
				'credit_list' => $credit_list,
			]);
			pc_base::load_sys_class('service')->display('member', 'change_credit');
		}
	}
	
	//mini登录条
	public function mini() {
		$_username = param::get_cookie('_username');
		$_userid = param::get_cookie('_userid');
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		//定义站点id常量
		if (!defined('SITEID')) {
			define('SITEID', $siteid);
		}
		
		pc_base::load_sys_class('service')->assign([
			'siteid' => $siteid,
			'_userid' => $_userid,
			'_username' => $_username,
		]);
		pc_base::load_sys_class('service')->display('member', 'mini');
		exit();
	}
	
	protected function _checkname($username) {
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
	protected function _get_usergroup_bypoint($point=0) {
		$groupid = 2;
		if(empty($point)) {
			$member_setting = getcache('member_setting');
			$point = $member_setting['defualtpoint'] ? $member_setting['defualtpoint'] : 0;
		}
		
		foreach ($this->grouplist as $k=>$v) {
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
			exit('-1');
		}
		$status = $this->db->get_one(array('username'=>$username));
		$status ? exit('-1') : exit('1');
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
			exit('-1');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['nickname'] == $this->db->escape($nickname)){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('nickname'=>$nickname));
				$status ? exit('-1') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('nickname'=>$nickname));
			$status ? exit('-1') : exit('1');
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
			exit('-1');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['email'] == $email){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('email'=>$email));
				$status ? exit('-1') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('email'=>$email));
			$status ? exit('-1') : exit('1');
		}
	}
	
	/**
	 * 检查手机
	 * @param string $mobile
	 * @return $status {-1:mobile已经存在;1:成功}
	 */
	public function public_checkmobile_ajax() {
		$mobile = $this->input->get('mobile') && trim($this->input->get('mobile')) ? trim($this->input->get('mobile')) : exit(0);
		if (!check_phone($mobile)) {
			exit('0');
		}
		//首先判断会员审核表
		$this->verify_db = pc_base::load_model('member_verify_model');
		if($this->verify_db->get_one(array('mobile'=>$mobile))) {
			exit('-1');
		}
		if($this->input->get('userid')) {
			$userid = intval($this->input->get('userid'));
			//如果是会员修改，而且NICKNAME和原来优质一致返回1，否则返回0
			$info = get_memberinfo($userid);
			if($info['mobile'] == $this->db->escape($mobile)){//未改变
				exit('1');
			}else{//已改变，判断是否已有此名
				$status = $this->db->get_one(array('mobile'=>$mobile));
				$status ? exit('-1') : exit('1');
			}
 		} else {
			$status = $this->db->get_one(array('mobile'=>$mobile));
			$status ? exit('-1') : exit('1');
		}
	}
	
	public function public_sina_login() {
		define('WB_AKEY', pc_base::load_config('system', 'sina_akey'));
		define('WB_SKEY', pc_base::load_config('system', 'sina_skey'));
		define('WEB_CALLBACK', APP_PATH.'index.php?m=member&c=index&a=public_sina_login&callback=1');
		pc_base::load_app_class('saetv2.ex', '' ,0);
		$member_setting = getcache('member_setting');
		if($this->input->get('callback') && trim($this->input->get('callback'))) {
			$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
			if ($this->input->request('code')) {
				$keys = array();
				$keys['code'] = $this->input->request('code');
				$keys['redirect_uri'] = WEB_CALLBACK;
				try {
					$token = $o->getAccessToken('code', $keys);
				} catch (OAuthException $e) {
				}
			}
			if ($token) {
				param::set_session('token', $token);
			}
			$c = new SaeTClientV2(WB_AKEY, WB_SKEY, param::get_session('token')['access_token'] );
			$ms = $c->home_timeline(); // done
			$uid_get = $c->get_uid();
			$uid = $uid_get['uid'];
			$me = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
			if(CHARSET != 'utf-8') {
				$me['name'] = iconv('utf-8', CHARSET, $me['name']);
				$me['location'] = iconv('utf-8', CHARSET, $me['location']);
				$me['description'] = iconv('utf-8', CHARSET, $me['description']);
				$me['screen_name'] = iconv('utf-8', CHARSET, $me['screen_name']);
			}
			if(!empty($me['id'])) {
 				//检查connect会员是否绑定，已绑定直接登录，未绑定提示注册/绑定页面
				$where = array('connectid'=>$me['id'], 'from'=>'sina');
				$r = $this->db->get_one($where);
				
				//connect用户已经绑定本站用户
				if(!empty($r)) {
					//读取本站用户信息，执行登录操作
					
					$password = $r['password'];
					$userid = $r['userid'];
					$groupid = $r['groupid'];
					$username = $r['username'];
					$nickname = empty($r['nickname']) ? $username : $r['nickname'];
					$login_attr = md5(SYS_KEY.$r['password'].(isset($r['login_attr']) ? $r['login_attr'] : ''));
					$this->db->update(array('lastip'=>ip(), 'lastdate'=>SYS_TIME, 'nickname'=>$me['name']), array('userid'=>$userid));
					
					$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
					
					$cms_auth = sys_auth($userid."\t".$password, 'ENCODE', get_auth_key('login'));
					
					param::set_cookie('auth', $cms_auth, $cookietime);
					param::set_cookie('_userid', $userid, $cookietime);
					param::set_cookie('_login_attr', $login_attr, $cookietime);
					param::set_cookie('_username', $username, $cookietime);
					param::set_cookie('_groupid', $groupid, $cookietime);
					param::set_cookie('_nickname', $nickname, $cookietime);
					$member['userid'] = $userid;
					// 登录后的钩子
					pc_base::load_sys_class('hooks')::trigger('member_login_after', $member);
					$this->clear_cache($member['userid']);
					$forward = $this->input->get('forward') && !empty($this->input->get('forward')) ? $this->input->get('forward') : APP_PATH.'index.php?m=member&c=index';
					showmessage(L('login_success'), $forward);
					
				} else {
 					//弹出绑定注册页面
					param::set_session('connectid', $me['id']);
					param::set_session('from', 'sina');
					$connect_username = $me['name'];
					
					//加载用户模块配置
					$member_setting = getcache('member_setting');
					if(!$member_setting['allowregister']) {
						showmessage(L('deny_register'), APP_PATH.'index.php?m=member&c=index&a=login');
					}
					
					//获取用户siteid
					$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
					//过滤非当前站点会员模型
					foreach($this->member_model as $k=>$v) {
						if($v['siteid']!=$siteid || $v['disabled']) {
							unset($this->member_model[$k]);
						}
					}
					if(empty($this->member_model)) {
						showmessage(L('site_have_no_model').L('deny_register'), HTTP_REFERER);
					}
					
					$modelid = 10; //设定默认值
					if(array_key_exists($modelid, $this->member_model)) {
						//获取会员模型表单
						require CACHE_MODEL_PATH.'member_form.class.php';
						$member_form = new member_form($modelid);
						$this->db->set_model($modelid);
						$forminfos = $forminfos_arr = $member_form->get();

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
						
						$formValidator = $member_form->formValidator;
					}
					pc_base::load_sys_class('service')->assign([
						'member_setting' => $member_setting,
						'siteid' => $siteid,
						'forminfos' => $forminfos,
						'formValidator' => $formValidator,
					]);
					pc_base::load_sys_class('service')->display('member', 'connect');
				}
			} else {
				showmessage(L('login_failure'), APP_PATH.'index.php?m=member&c=index&a=login');
			}
		} else {
			$o = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
			$aurl = $o->getAuthorizeURL(WEB_CALLBACK);
			
			pc_base::load_sys_class('service')->assign([
				'member_setting' => $member_setting,
				'o' => $o,
				'aurl' => $aurl,
			]);
			pc_base::load_sys_class('service')->display('member', 'connect_sina');
		}
	}
	
	
	/**
	 * QQ号码登录
	 * 该函数为QQ登录回调地址
	 */
	public function public_qq_login(){
		$appid = pc_base::load_config('system', 'qq_appid');
		$appkey = pc_base::load_config('system', 'qq_appkey');
		$callback = pc_base::load_config('system', 'qq_callback');
		pc_base::load_app_class('qqapi','',0);
		$info = new qqapi($appid,$appkey,$callback);
		$member_setting = getcache('member_setting');
		if(!$this->input->get('code')){
			$info->redirect_to_login();
		}else{
			$code = $this->input->get('code');
			$openid = $info->get_openid($code);
			param::set_session('openid', $openid);
			if(!empty($openid)){
				$r = $this->db->get_one(array('connectid'=>$openid,'from'=>'qq'));
				if(!empty($r)){
					//QQ已存在于数据库，则直接转向登录操作
					$password = $r['password'];
					$userid = $r['userid'];
					$groupid = $r['groupid'];
					$username = $r['username'];
					$nickname = empty($r['nickname']) ? $username : $r['nickname'];
					$login_attr = md5(SYS_KEY.$r['password'].(isset($r['login_attr']) ? $r['login_attr'] : ''));
					$this->db->update(array('lastip'=>ip(), 'lastdate'=>SYS_TIME, 'nickname'=>$me['name']), array('userid'=>$userid));
					$cookietime = $member_setting['logintime'] == '' ? 86400 : (intval($member_setting['logintime']) > 0 ? max(intval($member_setting['logintime']), 500) : 0);
					$cms_auth = sys_auth($userid."\t".$password, 'ENCODE', get_auth_key('login'));
					param::set_cookie('auth', $cms_auth, $cookietime);
					param::set_cookie('_userid', $userid, $cookietime);
					param::set_cookie('_login_attr', $login_attr, $cookietime);
					param::set_cookie('_username', $username, $cookietime);
					param::set_cookie('_groupid', $groupid, $cookietime);
					param::set_cookie('_nickname', $nickname, $cookietime);
					$member['userid'] = $userid;
					// 登录后的钩子
					pc_base::load_sys_class('hooks')::trigger('member_login_after', $member);
					$this->clear_cache($member['userid']);
					$forward = $this->input->get('forward') && !empty($this->input->get('forward')) ? $this->input->get('forward') : APP_PATH.'index.php?m=member&c=index';
					showmessage(L('login_success'), $forward);
				}else{	
					//未存在于数据库中，跳去完善资料页面。页面预置用户名（QQ返回是UTF8编码，如有需要进行转码）
					$user = $info->get_user_info();
					param::set_session('connectid', $openid);
					param::set_session('from', 'qq');
					pc_base::load_sys_class('service')->assign([
						'member_setting' => $member_setting,
						'connect_username' => $user,
					]);
					pc_base::load_sys_class('service')->display('member', 'connect');
				}
			}
		}
	}

	/**
	 * 找回密码
	 * 新增加短信找回方式 
	 */
	public function public_forget_password() {
		
		$email_config = getcache('common', 'commons');
		
		//SMTP MAIL 二种发送模式
 		if($email_config['mail_type'] == '1'){
			if(empty($email_config['mail_user']) || empty($email_config['mail_password'])) {
				showmessage(L('email_config_empty'), HTTP_REFERER);
			}
		}
		$member_setting = getcache('member_setting');
		if(IS_POST) {
			if (!check_captcha('code')) {
				showmessage(L('code_error'), HTTP_REFERER);
			}
			//邮箱验证
			if(!is_email($this->input->post('email'))){
				showmessage(L('email_error'), HTTP_REFERER);
			}
			// 验证操作间隔
			$name = 'member-email-find-password-'.$this->input->post('email');
			if ($this->cache->check_auth_data($name, defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 60)) {
				showmessage(L('已经发送稍后再试'), HTTP_REFERER);
			}
			$memberinfo = $this->db->get_one(array('email'=>$this->input->post('email')));
			if(!empty($memberinfo['email'])) {
				$email = $memberinfo['email'];
			} else {
				showmessage(L('email_error'), HTTP_REFERER);
			}
			
			$code = sys_auth($memberinfo['userid']."\t".microtime(true), 'ENCODE', get_auth_key('email'));

			$url = APP_PATH."index.php?m=member&c=index&a=public_forget_password&code=$code";
			$message = $member_setting['forgetpassword'];
			$message = str_replace(array('{click}','{url}'), array('<a href="'.$url.'">'.L('please_click').'</a>',$url), $message);
			//获取站点名称
			$sitelist = getcache('sitelist', 'commons');
			
			if(isset($sitelist[$memberinfo['siteid']]['name'])) {
				$sitename = $sitelist[$memberinfo['siteid']]['name'];
			} else {
				$sitename = 'CMS_V10_MAIL';
			}
			$this->email->set();
			if ($this->email->send($email, L('forgetpassword'), $message, $sitename)) {
				$this->cache->set_auth_data($name, $code);
				showmessage(L('operation_success'), APP_PATH.'index.php?m=member&c=index&a=login');
			} else {
				showmessage(L('邮件发送失败'));
			}
		} elseif($this->input->get('code')) {
			$code = sys_auth($this->input->get('code'), 'DECODE', get_auth_key('email'));
			$code = explode("\t", $code);

			if(is_array($code) && is_numeric($code[0]) && date('y-m-d h', SYS_TIME) == date('y-m-d h', (int)$code[1])) {
				$memberinfo = $this->db->get_one(array('userid'=>$code[0]));
				
				if(empty($memberinfo['userid'])) {
					showmessage(L('operation_failure'), APP_PATH.'index.php?m=member&c=index&a=login');
				}
				$updateinfo = array();
				$password = random(8,"23456789abcdefghkmnrstwxy");
				$updateinfo['password'] = password($password, $memberinfo['encrypt']);
				
				$this->db->update($updateinfo, array('userid'=>$code[0]));
				$email = $memberinfo['email'];
				//获取站点名称
				$sitelist = getcache('sitelist', 'commons');		
				if(isset($sitelist[$memberinfo['siteid']]['name'])) {
					$sitename = $sitelist[$memberinfo['siteid']]['name'];
				} else {
					$sitename = 'CMS_V10_MAIL';
				}
				$this->email->set();
				$this->email->send($email, L('forgetpassword'), "New password:".$password, $sitename);
				showmessage(L('operation_success').L('newpassword').':'.$password);

			} else {
				showmessage(L('operation_failure'), APP_PATH.'index.php?m=member&c=index&a=login');
			}

		} else {
			$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
			$siteinfo = siteinfo($siteid);
			
			pc_base::load_sys_class('service')->assign([
				'member_setting' => $member_setting,
				'siteid' => $siteid,
				'siteinfo' => $siteinfo,
			]);
			pc_base::load_sys_class('service')->display('member', 'forget_password');
		}
	}
	
	/**
	*通过手机修改密码
	*方式：用户发送HHPWD afei985#821008 至 1065788 ，CMS进行转发到网站运营者指定的回调地址，在回调地址程序进行密码修改等操作,处理成功时给用户发条短信确认。
	*cms 以POST方式传递相关数据到回调程序中
	*要求：网站中会员系统，mobile做为主表字段，并且唯一（如已经有手机号码，把号码字段转为主表字段中）
	*/
	
	public function public_changepwd_bymobile(){
		$phone = $this->input->request('phone');
		$msg = $this->input->request('msg');
		if(empty($phone) || empty($msg)){
			return false;
		}
		if(!preg_match('/^1([0-9]{10})$/',$phone)) {
			return false;
		}
		//判断是否CMS请求的接口
		pc_base::load_app_func('global','sms');
		$this->sms_setting_arr = getcache('sms', 'sms');
		$siteid = intval($this->input->request('siteid')) ? intval($this->input->request('siteid')) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		if(!empty($this->sms_setting_arr[$siteid])) {
			$this->sms_setting = $this->sms_setting_arr[$siteid];
		} else {
			$this->sms_setting = array();
		}
		//取用户名
		$msg_array = explode("@@",$str);
		$newpwd = $msg_array[1];
		$username = $msg_array[2];
		$array = $this->db->get_one(array('mobile'=>$phone,'username'=>$username));
		if(empty($array)){
			echo 1;
		}else{
			$result = $this->db->update(array('password'=>$newpwd),array('mobile'=>$phone,'username'=>$username));
			if($result){
				//修改成功，发送短信给用户回执
				$content = file_get_contents(PC_PATH.'modules/sms/classes/notice/member_edit_password.html');
				$content = str_replace('{$username}', $username, $content);
				$content = str_replace('{$password}', $newpwd, $content);
				$content = str_replace('{$sys_time}', dr_date(SYS_TIME), $content);
				$return = pc_base::load_app_class('smsapi', 'sms')->send_sms($phone, $content);
				echo 1;
 			}
		}
	}
	
	/**
	 * 手机短信方式找回密码
	 */
	public function public_forget_password_mobile() {
		$step = intval($this->input->post('step'));
		$step = max($step,1);
		
		if(IS_POST && $step==2) {
		//处理提交申请，以手机号为准
			if (!check_captcha('code')) {
				showmessage(L('code_error'), HTTP_REFERER);
			}
			//验证
			if(!is_username($this->input->post('username'))){
				showmessage(L('username_format_incorrect'), HTTP_REFERER);
			}
			$username = safe_replace($this->input->post('username'));

			$r = $this->db->get_one(array('username'=>$username),'userid,mobile');
			if($r['mobile']=='') {
				param::del_session('mobile');
				param::del_session('userid');
				showmessage(L('该账号没有绑定手机号码，请选择其他方式找回！'));
			}
			param::set_session('mobile', $r['mobile']);
			param::set_session('userid', $r['userid']);
			pc_base::load_sys_class('service')->assign([
				'step' => $step,
				'r' => $r,
			]);
			pc_base::load_sys_class('service')->display('member', 'forget_password_mobile');
		} elseif(IS_POST && $step==3) {
			$sms_report_db = pc_base::load_model('sms_report_model');
			$mobile_verify = $this->input->post('mobile_verify');
			$mobile = param::get_session('mobile');
			if($mobile){
				if(!preg_match('/^1([0-9]{10})$/',$mobile)) exit('check phone error');
				pc_base::load_app_func('global','sms');
				$sys_cache_sms = defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 300;
				$posttime = SYS_TIME-$sys_cache_sms;
				$where = "`mobile`='$mobile' AND `posttime`>'$posttime'";
				$r = $sms_report_db->get_one($where,'id,id_code','id DESC');
				if($r && $r['id_code']==$mobile_verify) {
					$sms_report_db->update(array('id_code'=>''),$where);
					$userid = param::get_session('userid');
					$updateinfo = array();
					$password = random(8,"23456789abcdefghkmnrstwxy");
					$encrypt = random(6,"23456789abcdefghkmnrstwxyABCDEFGHKMNRSTWXY");
					$updateinfo['encrypt'] = $encrypt;
					$updateinfo['password'] = password($password, $encrypt);
					
					$this->db->update($updateinfo, array('userid'=>$userid));
					$rs = $this->db->get_one(array('userid'=>$userid));
					$content = file_get_contents(PC_PATH.'modules/sms/classes/notice/member_reset_password.html');
					$content = str_replace('{$username}', $rs['username'], $content);
					$content = str_replace('{$password}', $password, $content);
					$content = str_replace('{$sys_time}', dr_date(SYS_TIME), $content);
					$status = sendsms($mobile, $content);
					if(!$status['code']) showmessage($status['msg']);
					param::del_session('mobile');
					param::del_session('userid');
					showmessage(L('密码已重置成功！请查收手机'),'?m=member&c=index&a=login');
				} else {
					showmessage(L('短信验证码错误！请重新获取！'));
				}
			}else{
				showmessage(L('短信验证码已过期！请重新获取！'));
			}
		} else {
			pc_base::load_sys_class('service')->assign([
				'step' => $step,
			]);
 			pc_base::load_sys_class('service')->display('member', 'forget_password_mobile');
		}
	}
	//通过用户名找回密码
	public function public_forget_password_username() {
		$step = intval($this->input->post('step'));
		$step = max($step,1);
		
		if(IS_POST && $step==2) {
		//处理提交申请，以手机号为准
			if (!check_captcha('code')) {
				showmessage(L('code_error'), HTTP_REFERER);
			}
			//验证
			if(!is_username($this->input->post('username'))){
				showmessage(L('username_format_incorrect'), HTTP_REFERER);
			}
			$username = safe_replace($this->input->post('username'));

			$r = $this->db->get_one(array('username'=>$username),'userid,email');
			if($r['email']=='') {
				param::del_session('userid');
				showmessage(L('该账号没有绑定邮箱，请选择其他方式找回！'));
			} else {
				param::set_session('userid', $r['userid']);
				param::set_session('email', $r['email']);
			}
			param::del_session('emc');
			param::set_session('emc_times', 0);
			$email_arr = explode('@',$r['email']);
			pc_base::load_sys_class('service')->assign([
				'step' => $step,
				'r' => $r,
				'email_arr' => $email_arr,
			]);
			pc_base::load_sys_class('service')->display('member', 'forget_password_username');
		} elseif(IS_POST && $step==3) {
			$sms_report_db = pc_base::load_model('sms_report_model');
			$mobile_verify = $this->input->post('mobile_verify');
			$email = param::get_session('email');
			if($email){
				if(!preg_match('/^([a-z0-9_]+)@([a-z0-9_]+).([a-z]{2,6})$/',$email)) exit('check email error');
				if(param::get_session('emc_times')=='' || param::get_session('emc_times')<=0){
					showmessage(L('验证次数超过5次,验证码失效，请重新获取邮箱验证码！'),HTTP_REFERER,3000);
				}
				param::set_session('emc_times', param::get_session('emc_times')-1);
				if(param::get_session('emc') && $this->input->post('email_verify')==param::get_session('emc')) {
					
					$userid = param::get_session('userid');
					$updateinfo = array();
					$password = random(8,"23456789abcdefghkmnrstwxy");
					$encrypt = random(6,"23456789abcdefghkmnrstwxyABCDEFGHKMNRSTWXY");
					$updateinfo['encrypt'] = $encrypt;
					$updateinfo['password'] = password($password, $encrypt);
					
					$this->db->update($updateinfo, array('userid'=>$userid));
					$rs = $this->db->get_one(array('userid'=>$userid));
					param::del_session('email');
					param::del_session('userid');
					param::del_session('emc');
					$this->email->set();
					$this->email->send($email, '密码重置通知', "您在".date('Y-m-d H:i:s')."通过密码找回功能，重置了本站密码。");
					pc_base::load_sys_class('service')->assign([
						'step' => $step,
						'password' => $password,
					]);
					pc_base::load_sys_class('service')->display('member', 'forget_password_username');
					exit;
				} else {
					showmessage(L('验证码错误！请重新获取！'),HTTP_REFERER,3000);
				}
			} else {
				showmessage(L('非法请求！'));
			}
		} else {
			pc_base::load_sys_class('service')->assign([
				'step' => $step,
			]);
 			pc_base::load_sys_class('service')->display('member', 'forget_password_username');
		}
	}

	//邮箱获取验证码
	public function public_get_email_verify() {
		// 验证操作间隔
		$name = 'member-username-find-password-'.param::get_session('email');
		if ($this->cache->check_auth_data($name, defined('SYS_CACHE_SMS') && SYS_CACHE_SMS ? SYS_CACHE_SMS : 60)) {
			dr_json(0, L('已经发送稍后再试'));
		}
		$code = random(8,"23456789abcdefghkmnrstwxy");
		param::set_session('emc', $code);
		param::set_session('emc_times', 5);
		$message = '您的验证码为：'.$code;

		$this->email->set();
		if ($this->email->send(param::get_session('email'), '邮箱找回密码验证', $message)) {
			$this->cache->set_auth_data($name, $code);
			dr_json(1, 'ok');
		} else {
			dr_json(0, '邮件发送失败');
		}
	}
}
?>