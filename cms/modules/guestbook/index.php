<?php
defined('IN_CMS') or exit('No permission resources.');
class index {
	private $input,$type;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		define("SITEID",$siteid);
	}
	
	public function init() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID; 
 		$setting = getcache('guestbook', 'commons');
		$SEO = seo(SITEID, '', L('guestbook'), '', '');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'setting' => $setting,
			'page' => max(1, intval($this->input->get('page'))),
		]);
		pc_base::load_sys_class('service')->display('guestbook', 'index');
	}
	
	 /**
	 *	留言板列表页
	 */
	public function list_type() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = SITEID;
		$type_id = trim(urldecode($this->input->get('type_id')));
		$type_id = intval($type_id);
		if($type_id==""){
			$type_id ='0';
		}
		$setting = getcache('guestbook', 'commons');
		$SEO = seo(SITEID, '', L('guestbook'), '', '');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'setting' => $setting,
			'type_id' => $type_id,
		]);
		pc_base::load_sys_class('service')->display('guestbook', 'list_type');
	} 

	 /**
	 *	留言板留言 
	 */
	public function register() { 
		$siteid = SITEID;
		$setting = getcache('guestbook', 'commons');
		$setting = $setting[$siteid];
		if(!$setting['is_post']){
			showmessage(L('suspend_application'), HTTP_REFERER);
		}
		if(IS_POST){
			if($setting['enablecheckcode']){//开启验证码
				if (!check_captcha('code')) {
					showmessage(L('code_error'));
				}
			}
			if(!$this->input->post('name')){
				showmessage(L('usename_noempty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
			}
			if(!$this->input->post('lxqq')){
				showmessage(L('email_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
			}
			if(!$this->input->post('email')){
				showmessage(L('email_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
			}
			if(!$this->input->post('shouji')){
				showmessage(L('shouji_not_empty'),"?m=guestbook&c=index&a=register&siteid=$siteid");
			}
			$guestbook_db = pc_base::load_model('guestbook_model');
			 
			 /*添加用户数据*/
			$sql = array('siteid'=>$siteid,'typeid'=>$this->input->post('typeid'),'name'=>$this->input->post('name'),'sex'=>$this->input->post('sex'),'lxqq'=>$this->input->post('lxqq'),'email'=>$this->input->post('email'),'shouji'=>$this->input->post('shouji'),'introduce'=>$this->input->post('introduce'),'addtime'=>SYS_TIME);
			 
			$dataid = $guestbook_db->insert($sql, true);
			if ($dataid) {
				if ($setting['sendmail'] && $setting['mails']) {
					$email = pc_base::load_sys_class('email');
					$mails = explode(',', $setting['mails']);
					if (is_array($mails)) {
						foreach ($mails as $m) {
							$email->set();
							$mailmessage = $setting['mailmessage'];
							$mailmessage = str_replace('$', '', $mailmessage);
							if (preg_match_all("/\{(.+)\}/U", $mailmessage, $value)) {
								foreach ($value[1] as $t) {
									$mailmessage = str_replace($t, $this->input->post($t), $mailmessage);
								}
							}
							$mailmessage = str_replace(array('{', '}'), '', $mailmessage);
							$email->send($m, L('tips'), $mailmessage);
						}
					}
				}
				if ($setting['sendsms'] && $setting['mobiles'] && module_exists('sms')) {
					$mobiles = explode(',', $setting['mobiles']);
					if (is_array($mobiles)) {
						foreach ($mobiles as $m) {
							$smsmessage = $setting['smsmessage'];
							$smsmessage = str_replace('$', '', $smsmessage);
							if (preg_match_all("/\{(.+)\}/U", $smsmessage, $value)) {
								foreach ($value[1] as $t) {
									$smsmessage = str_replace($t, $this->input->post($t), $smsmessage);
								}
							}
							$smsmessage = str_replace(array('{', '}'), '', $smsmessage);
							$rt = pc_base::load_app_class('smsapi', 'sms')->send_sms($m, $smsmessage);
						}
					}
				}
			}
			showmessage(L('add_success').($setting['sendsms'] && $setting['mobiles'] && module_exists('sms') ? $rt['msg'] : ''), "?m=guestbook&c=index&siteid=$siteid");
		}else {
			$this->type = pc_base::load_model('type_model');
			$types = $this->type->get_types($siteid);//获取站点下所有留言板分类
			pc_base::load_sys_class('form');
			$SEO = seo(SITEID, '', L('application_guestbook'), '', '');
			pc_base::load_sys_class('service')->assign([
				'SEO' => $SEO,
				'siteid' => $siteid,
				'setting' => $setting,
				'types' => $types,
			]);
			pc_base::load_sys_class('service')->display('guestbook', 'register');
		}
	} 
	
}
?>