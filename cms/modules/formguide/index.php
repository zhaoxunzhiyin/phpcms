<?php
defined('IN_CMS') or exit('No permission resources.');
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
class index {
	private $input, $db, $m_db, $siteid, $setting;
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		$this->db = pc_base::load_model('sitemodel_model');
		$this->m_db = pc_base::load_model('sitemodel_field_model');
		$setting = new_html_special_chars(getcache('formguide', 'commons'));
		$this->siteid = intval($this->input->get('siteid')) ? intval(trim($this->input->get('siteid'))) : (defined('SITE_ID') && SITE_ID!=1 ? SITE_ID : get_siteid());
		$this->setting = $setting[$this->siteid];
		if (!$this->setting) {
			$this->setting = array();
		}
	}
	
	/**
	 * 表单向导首页
	 */
	public function index() {
		if (IS_POST) {
			dr_json(0, L('禁止提交，请检查提交地址是否有误'));
		}
		$siteid = $this->siteid;
		$SEO = seo($this->siteid, '', L('formguide_list'));
		$page = max(intval($this->input->get('page')), 1);
		$total = $this->db->count(array('siteid'=>$this->siteid, 'type'=>3, 'disabled'=>0));
		$pages = pages($total, $page, 20);
		$offset = ($page-1)*20;
		$datas = $this->db->select(array('siteid'=>$this->siteid, 'type'=>3, 'disabled'=>0), 'modelid, name, addtime', $offset.',20', 'sort,modelid');
		pc_base::load_sys_class('service')->assign([
			'SEO' => $SEO,
			'siteid' => $siteid,
			'pages' => $pages,
			'datas' => $datas,
		]);
		pc_base::load_sys_class('service')->display('formguide', 'index');
	}
	
	/**
	 * 表单展示
	 */
	public function show() {
		if (!$this->input->get('formid') || empty($this->input->get('formid'))) {
			$this->input->get('action') ? exit : showmessage(L('form_no_exist'), HTTP_REFERER);
		}
		$siteid = $this->input->get('siteid') ? intval($this->input->get('siteid')) : 1;
		$formid = intval($this->input->get('formid'));
		$r = $this->db->get_one(array('modelid'=>$formid, 'siteid'=>$siteid, 'disabled'=>0), 'tablename, setting');
		if (!$r) {
			$this->input->get('action') ? exit : showmessage(L('form_no_exist'), HTTP_REFERER);
		}
		$setting = string2array($r['setting']);
		if ($setting['enabletime']) {
			if (($setting['starttime'] && $setting['starttime']>SYS_TIME) || ($setting['endtime'] && ($setting['endtime']+3600*24)<SYS_TIME)) {
				$this->input->get('action') ? exit : showmessage(L('form_expired'), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
			}
		}
		$userid = intval(param::get_cookie('_userid'));
		if ($setting['allowunreg']==0 && !$userid && $this->input->get('action')!='js') showmessage(L('please_login_in'), APP_PATH.'index.php?m=member&c=index&a=login&forward='.urlencode(HTTP_REFERER));
		if (IS_POST) {
			if($setting['code']){//开启验证码
				if (!check_captcha('code')) {
					showmessage(L('code_error'));
				}
			}
			$tablename = 'form_'.$r['tablename'];
			$this->m_db->table($tablename);
			
			$where = array();
			$where[] = 'userid="'.$userid.'"';
			$where[] = 'ip="'.ip().'"';
			$re = $this->m_db->get_one(implode(' AND ', $where), 'datetime', '`dataid` DESC');
			if (!$setting['allowmultisubmit'] && $re) {
				$this->input->get('action') ? exit : showmessage(L('had_participate'), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
			} else if (($setting['allowmultisubmit'] && $re['datetime']) && ((SYS_TIME-$re['datetime'])<intval($this->setting['interval'])*60)) {
				$this->input->get('action') ? exit : showmessage(L('had_participate'), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
			}
			$data = array();
			require CACHE_MODEL_PATH.'formguide_input.class.php';
			$formguide_input = new formguide_input($formid);
			$data = $this->input->post('info');
			// 挂钩点
			pc_base::load_sys_class('hooks')::trigger('form_post_before', $data);
			$data = $formguide_input->get($data);
			$data['userid'] = $userid;
			$data['username'] = param::get_cookie('_username');
			$data['datetime'] = SYS_TIME;
			$data['ip'] = ip();
			$dataid = $this->m_db->insert($data, true);
			if ($dataid) {
				if ($setting['sendmail'] && $setting['mails']) {
					$email = pc_base::load_sys_class('email');
					$mails = explode(',', $setting['mails']);
					if (is_array($mails)) {
						foreach ($mails as $m) {
							$email->set();
							$mailmessage = $setting['mailmessage'] ? $setting['mailmessage'] : $this->setting['mailmessage'];
							$mailmessage = str_replace('$', '', $mailmessage);
							if (preg_match_all("/\{(.+)\}/U", $mailmessage, $value)) {
								foreach ($value[1] as $t) {
									$mailmessage = str_replace($t, $data[$t], $mailmessage);
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
							$smsmessage = $setting['smsmessage'] ? $setting['smsmessage'] : $this->setting['smsmessage'];
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
				// 挂钩点
				pc_base::load_sys_class('hooks')::trigger('form_post_after', $data);
				$this->db->update(array('items'=>'+=1'), array('modelid'=>$formid, 'siteid'=>$this->siteid));
			}
			showmessage((isset($setting['rt_text']) && $setting['rt_text'] ? $setting['rt_text'] : L('thanks')).($setting['sendsms'] && $setting['mobiles'] && module_exists('sms') ? $rt['msg'] : ''), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
		} else {
			if ($setting['allowunreg']==0 && !$userid && $this->input->get('action')=='js') {
				$no_allowed = 1;
			}
			pc_base::load_sys_class('form');
			$f_info = $this->db->get_one(array('modelid'=>$formid, 'siteid'=>$this->siteid));
			extract($f_info);
			$tablename = 'form_'.$r['tablename'];
			$this->m_db->table($tablename);
			$ip = ip();
			$where = array();
			$where[] = 'userid="'.$userid.'"';
			$where[] = 'ip="'.ip().'"';
			$re = $this->m_db->get_one(implode(' AND ', $where), 'datetime', '`dataid` DESC');
			$setting = string2array($setting);
			if (!$setting['allowmultisubmit'] && $re) {
				$this->input->get('action') ? exit : showmessage(L('had_participate'), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
			} else if (($setting['allowmultisubmit'] && $re['datetime']) && ((SYS_TIME-$re['datetime'])<intval($this->setting['interval'])*60)) {
				$this->input->get('action') ? exit : showmessage(L('had_participate'), isset($setting['rt_url']) && $setting['rt_url'] ? str_replace(array('{APP_PATH}', '{formid}', '{siteid}'), array(APP_PATH, $formid, $this->siteid), $setting['rt_url']) : APP_PATH);
			}
			
			require CACHE_MODEL_PATH.'formguide_form.class.php';
			$formguide_form = new formguide_form($formid, $no_allowed);
			$forminfos_data = $formguide_form->get();
			$SEO = seo($this->siteid, '', $name);
			if ($this->input->get('action') && $this->input->get('action')=='js') {
				if(!function_exists('ob_gzhandler')) ob_clean();
				ob_start();
			}
			$template = ($this->input->get('action')=='js') ? $js_template : $show_template;
			$setting2 = [];
			$setting2['codelen'] = $this->setting['codelen'];
			pc_base::load_sys_class('service')->assign([
				'SEO' => $SEO,
				'siteid' => $this->siteid,
				'formid' => $formid,
				'name' => $name,
				'setting' => dr_array22array($setting, $setting2),
				'no_allowed' => $no_allowed,
				'forminfos_data' => $forminfos_data,
			]);
			pc_base::load_sys_class('service')->display('formguide', $template, $default_style);
			if ($this->input->get('action') && $this->input->get('action')=='js') {
				$data=ob_get_contents();
				ob_clean();
				exit(format_js($data));
			}
		}
	}
}
?>