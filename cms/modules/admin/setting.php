<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class setting extends admin {
	private $input,$db,$cache_api;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('module_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
		pc_base::load_app_func('global');
	}
	
	/**
	 * 配置信息
	 */
	public function init() {
		$show_header = $show_pc_hash = $show_validator = true;
		if(IS_AJAX_POST) {
			$setconfig = $this->input->post('setconfig');
			$setting = $this->input->post('setting');
			$setting['admin_email'] = is_email($setting['admin_email']) ? trim($setting['admin_email']) : dr_json(0, L('email_illegal'), array('field' => 'admin_email'));
			if (intval($setting['sysadmincodelen'])<2 || intval($setting['sysadmincodelen'])>8) {
				dr_json(0, L('setting_noe_code_len'), array('field' => 'sysadmincodelen'));
			}
			if (!$setconfig['js_path']) {
				dr_json(0, L('setting_js_path').L('empty'), array('field' => 'js_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['js_path'])) {
				dr_json(0, L('setting_js_path').L('setting_end_with_x'), array('field' => 'js_path'));
			}
			if (!$setconfig['css_path']) {
				dr_json(0, L('setting_css_path').L('empty'), array('field' => 'css_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['css_path'])) {
				dr_json(0, L('setting_css_path').L('setting_end_with_x'), array('field' => 'css_path'));
			}
			if (!$setconfig['img_path']) {
				dr_json(0, L('setting_img_path').L('empty'), array('field' => 'img_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['img_path'])) {
				dr_json(0, L('setting_img_path').L('setting_end_with_x'), array('field' => 'img_path'));
			}
			if (!$setconfig['mobile_js_path']) {
				dr_json(0, L('setting_mobile_js_path').L('empty'), array('field' => 'mobile_js_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['mobile_js_path'])) {
				dr_json(0, L('setting_mobile_js_path').L('setting_end_with_x'), array('field' => 'mobile_js_path'));
			}
			if (!$setconfig['mobile_css_path']) {
				dr_json(0, L('setting_mobile_css_path').L('empty'), array('field' => 'mobile_css_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['mobile_css_path'])) {
				dr_json(0, L('setting_mobile_css_path').L('setting_end_with_x'), array('field' => 'mobile_css_path'));
			}
			if (!$setconfig['mobile_img_path']) {
				dr_json(0, L('setting_mobile_img_path').L('empty'), array('field' => 'mobile_img_path'));
			}
			if (!preg_match('/^(.+)\/$/i', $setconfig['mobile_img_path'])) {
				dr_json(0, L('setting_mobile_img_path').L('setting_end_with_x'), array('field' => 'mobile_img_path'));
			}
			if (!$setting['captcha_charset'] && $setting['sysadmincodemodel']==3) {
				dr_json(0, L('setting_code_character').L('empty'), array('field' => 'captcha_charset'));
			}
			if (!preg_match('/^[A-Za-z0-9]+$/i', $setting['captcha_charset']) && $setting['sysadmincodemodel']==3) {
				dr_json(0, L('setting_code_character').L('setting_character_letters'), array('field' => 'captcha_charset'));
			}
			if ($setting['captcha_charset'] && $setting['sysadmincodemodel']!=3) {
				$setting['captcha_charset'] = '';
			}
			$setting['sysadmincode'] = intval($setting['sysadmincode']);
			$setting['maxloginfailedtimes'] = intval($setting['maxloginfailedtimes']);
			$setting['sysadminlogintimes'] = intval($setting['sysadminlogintimes']);
			$setting['mail_type'] = intval($setting['mail_type']);
			$setting['mail_server'] = trim($setting['mail_server']);
			$setting['mail_port'] = intval($setting['mail_port']);
			$setting['category_ajax'] = intval(abs($setting['category_ajax']));
			$setting['mail_user'] = trim($setting['mail_user']);
			$setting['mail_auth'] = intval($setting['mail_auth']);
			$setting['mail_from'] = trim($setting['mail_from']);
			$setting['mail_password'] = trim($setting['mail_password']);
			$setting['admin_sms_login'] = intval($setting['admin_sms_login']);
			$setting['admin_sms_check'] = intval($setting['admin_sms_check']);
			$setting['admin_login_aes'] = intval($setting['admin_login_aes']);
			if (!function_exists('openssl_decrypt')) {
				$setting['admin_login_aes'] = 0;
			}
			if($setting['mail_password']) {
				$data = $this->db->get_one(array('module'=>'admin'));
				$system_setting = string2array($data['setting']);
				$setting['mail_password'] = $setting['mail_password'] == '******' ? $system_setting['mail_password'] : $setting['mail_password'];
			}
			$setting = array2string($setting);
			$this->db->update(array('setting'=>$setting), array('module'=>'admin')); //存入admin模块setting字段
			
			$setconfig['sys_admin_pagesize'] = intval($setconfig['sys_admin_pagesize']);
			if (!$setconfig['sys_admin_pagesize']) {
				dr_json(0, L('setting_admin_pagesize').L('empty'), array('field' => 'sys_admin_pagesize'));
			}
			$setconfig['debug'] = intval($setconfig['debug']);
			if (IS_DEV) {
				$setconfig['debug'] = 1;
			}
			$setconfig['sys_go_404'] = intval($setconfig['sys_go_404']);
			$setconfig['sys_301'] = intval($setconfig['sys_301']);
			$setconfig['sys_url_only'] = intval($setconfig['sys_url_only']);
			$setconfig['sys_csrf'] = intval($setconfig['sys_csrf']);
			$setconfig['sys_csrf_time'] = intval($setconfig['sys_csrf_time']);
			$setconfig['needcheckcomeurl'] = intval($setconfig['needcheckcomeurl']);
			$setconfig['admin_log'] = intval($setconfig['admin_log']);
			$setconfig['gzip'] = intval($setconfig['gzip']);
			$setconfig['tpl_edit'] = intval($setconfig['tpl_edit']);
			if(cleck_admin(param::get_session('roleid')) && dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {
				if(!$setconfig['admin_founders']) {
					$setconfig['admin_founders'] = 1;
				}
				$setconfig_admin_founders = explode(',',$setconfig['admin_founders']);
				if(!dr_in_array(1, $setconfig_admin_founders)) {
					$setconfig['admin_founders'] = '1,'.$setconfig['admin_founders'];
				}
			}
			if($setconfig['cookie_pre']) {
				$setconfig['cookie_pre'] = dr_safe_filename($setconfig['cookie_pre'] == '************' ? COOKIE_PRE : $setconfig['cookie_pre']);
			}
			if($setconfig['auth_key']) {
				$setconfig['auth_key'] = dr_safe_filename($setconfig['auth_key'] == '************' ? SYS_KEY : $setconfig['auth_key']);
			}

			set_config($setconfig);	 //保存进config文件
			$this->setcache();
			dr_json(1, L('setting_succ'));
		}
		$setconfig = pc_base::load_config('system');
		extract($setconfig);
		if(!function_exists('ob_gzhandler')) $gzip = 0;
		$info = $this->db->get_one(array('module'=>'admin'));
		extract(string2array($info['setting']));
		$page = (int)$this->input->get('page');
		include $this->admin_tpl('setting');
	}
	
	/*
	 * 测试邮件配置
	 */
	public function public_test_mail() {
		if (!$this->input->get('mail_to')) {
			dr_json(0, L('test_email_to'));
		}
		$config = getcache('common','commons');
		$email = pc_base::load_sys_class('email');
		$dmail = $email->set(array(
			'host' => $this->input->post('mail_server'),
			'user' => $this->input->post('mail_user'),
			'pass' => $this->input->post('mail_password') == '******' ? $config['mail_password'] : $this->input->post('mail_password'),
			'port' => intval($this->input->post('mail_port')),
			'type' => intval($this->input->post('mail_type')),
			'auth' => intval($this->input->post('mail_auth')),
			'from' => $this->input->post('mail_from')
		));
		$subject = 'cms test mail';
		$message = 'this is a test mail from cms team';
		if ($dmail->send($this->input->get('mail_to'), $subject, $message)) {
			dr_json(1, str_replace('{mail_to}', $this->input->get('mail_to'), L('test_email_succ_to')));
		} else {
			dr_json(0, L('test_email_faild_to'). $dmail->error());
		}
	}

	public function public_test_index() {
		$kw = trim($this->input->get('kw'));
		if (!$kw) {
			$kw = 'iphone手机出现“白苹果”原因及解决办法，用苹果手机的可以看下';
		}
		$rt = dr_get_keyword_data($kw, 'cms');
		if (!$rt['code']) {
			exit('失败：'.$rt['msg'].'<hr><pre>'.var_export($rt['data'], true).'</pre>');
		}
		exit('原文：'.$kw.'<hr>结果：'.$rt['msg']);
	}
	
	/**
	 * 设置缓存
	 * Enter description here ...
	 */
	private function setcache() {
		$this->cache_api->cache('setting');
	}
	
	/**
	 * 生成安全码
	 */
	public function public_syskey() {
		$action = $this->input->get('action');
		if ($action=='cookie_pre') {
			echo token().'_';exit;
		} else {
			echo token(dr_site_info('name', 1));exit;
		}
	}
	
	// 当前时间值
	public function public_site_time() {
		dr_json(1, dr_date(SYS_TIME));
	}
}
?>