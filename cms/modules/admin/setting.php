<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class setting extends admin {
	private $db;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('module_model');
		pc_base::load_app_func('global');
	}
	
	/**
	 * 配置信息
	 */
	public function init() {
		$show_validator = true;
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
		if(!function_exists('ob_gzhandler')) $gzip = 0;
		$info = $this->db->get_one(array('module'=>'admin'));
		extract(string2array($info['setting']));
		$show_header = true;
		$show_validator = 1;
		include $this->admin_tpl('setting');
	}
	
	/**
	 * 保存配置信息
	 */
	public function save() {
		$setting = $this->input->post('setting');
		$setting['admin_email'] = is_email($setting['admin_email']) ? trim($setting['admin_email']) : showmessage(L('email_illegal'),HTTP_REFERER);
		$setting['sysadmincode'] = intval($setting['sysadmincode']);
		$setting['maxloginfailedtimes'] = intval($setting['maxloginfailedtimes']);
		$setting['sysadminlogintimes'] = intval($setting['sysadminlogintimes']);
		$setting['minrefreshtime'] = intval($setting['minrefreshtime']);
		$setting['mail_type'] = intval($setting['mail_type']);		
		$setting['mail_server'] = trim($setting['mail_server']);	
		$setting['mail_port'] = intval($setting['mail_port']);	
		$setting['category_ajax'] = intval(abs($setting['category_ajax']));	
		$setting['mail_user'] = trim($setting['mail_user']);
		$setting['mail_auth'] = intval($setting['mail_auth']);		
		$setting['mail_from'] = trim($setting['mail_from']);		
		$setting['mail_password'] = trim($setting['mail_password']);
		$setting['errorlog_size'] = trim($setting['errorlog_size']);
		$setting = array2string($setting);
		$this->db->update(array('setting'=>$setting), array('module'=>'admin')); //存入admin模块setting字段
		
		//如果开始盛大通行证接入，判断服务器是否支持curl
		$setconfig = $this->input->post('setconfig');
		$snda_error = '';
		if(!$setconfig['baidu_skey'] || !$setconfig['baidu_arcretkey']) {
			delcache('baidu_api_access_token','commons');
		}
		if($setconfig['snda_akey'] || $setconfig['snda_skey']) {
			if(function_exists('curl_init') == FALSE) {
				$snda_error = L('snda_need_curl_init');
				$setconfig['snda_enable'] = 0;
			}
		}
		if($setconfig['auth_key']) {
			$system_setconfig = pc_base::load_config('system');
			$setconfig['auth_key'] = dr_safe_filename($setconfig['auth_key'] == '************' ? $system_setconfig['auth_key'] : $setconfig['auth_key']);
		}

		set_config($setconfig);	 //保存进config文件
		$this->setcache();
		showmessage(L('setting_succ').$snda_error, HTTP_REFERER);
	}
	
	/*
	 * 测试邮件配置
	 */
	public function public_test_mail() {
		pc_base::load_sys_func('mail');
		$subject = 'cms test mail';
		$message = 'this is a test mail from cms team';
		$mail= Array (
			'mailsend' => 2,
			'maildelimiter' => 1,
			'mailusername' => 1,
			'server' => $this->input->post('mail_server'),
			'port' => intval($this->input->post('mail_port')),
			'mail_type' => intval($this->input->post('mail_type')),
			'auth' => intval($this->input->post('mail_auth')),
			'from' => $this->input->post('mail_from'),
			'auth_username' => $this->input->post('mail_user'),
			'auth_password' => $this->input->post('mail_password')
		);	
		
		if(sendmail($this->input->get('mail_to'),$subject,$message,$this->input->post('mail_from'),$mail)) {
			echo L('test_email_succ').$this->input->get('mail_to');
		} else {
			echo L('test_email_faild');
		}	
	}
	
	/**
	 * 设置缓存
	 * Enter description here ...
	 */
	private function setcache() {
		$result = $this->db->get_one(array('module'=>'admin'));
		$setting = string2array($result['setting']);
		setcache('common', $setting,'commons');
	}
	
	/**
	 * 生成安全码
	 */
	public function public_syskey() {
		$site = siteinfo(1);
		echo token($site['name']);exit;
	}
	
	// 当前时间值
	public function public_site_time() {
		dr_json(1, dr_date(SYS_TIME));
	}
}
?>