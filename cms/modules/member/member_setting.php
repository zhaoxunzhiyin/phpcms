<?php
/**
 * 管理员后台会员模块设置
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_app_class('member_list_view', 'member');
pc_base::load_sys_class('list_view', '', 0);

class member_setting extends admin {
	
	private $input,$cache,$db,$menu_db,$cache_api,$sms_setting_arr,$sms_setting;
	
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->cache = pc_base::load_sys_class('cache');
		$this->db = pc_base::load_model('module_model');
		$this->menu_db = pc_base::load_model('menu_model');
		$this->cache_api = pc_base::load_app_class('cache_api', 'admin');
	}

	private function get_list_view() {
		return new list_view(member_list_view::scene(), member_list_view::candidates(), 0, '0');
	}

	/**
	 * member list
	 */
	function manage() {
		$show_header = $show_validator = true;
		if(IS_AJAX_POST) {
			$member_setting = $this->input->post('info');
			$setting = $this->input->post('setting');
			$member_setting['allowregister'] = intval($member_setting['allowregister']);
			$member_setting['choosemodel'] = intval($member_setting['choosemodel']);
			$member_setting['enablemailcheck'] = intval($member_setting['enablemailcheck']);
			$member_setting['registerverify'] = intval($member_setting['registerverify']);
			$member_setting['showapppoint'] = intval($member_setting['showapppoint']);
			$member_setting['showregprotocol'] = intval($member_setting['showregprotocol']);

			$list_view = $this->get_list_view();
			$list_field = isset($setting['list_field']) && is_array($setting['list_field']) ? $setting['list_field'] : array();
			$list_field = dr_list_field_order($list_field);
			$validate = $list_view->validate($list_field);
			if (!$validate['code']) {
				dr_json(0, $validate['msg']);
			}
			$list_view->save($list_field, 0, '0');

			// 保留旧 member_setting.list_field，兼容尚未切换到 ListView 的代码。
			$member_setting['list_field'] = $list_view->getVisible();

			if (!preg_match('/^\\d{1,8}$/i', $member_setting['rmb_point_rate'])) {
				dr_json(0, L('rmb_point_rate').L('between_1_to_8_num'), array('field' => 'rmb_point_rate'));
			}
			if (!preg_match('/^\\d{1,8}$/i', $member_setting['defualtpoint'])) {
				dr_json(0, L('defualtpoint').L('between_1_to_8_num'), array('field' => 'defualtpoint'));
			}
			if (!preg_match('/^\\d{1,8}$/i', $member_setting['defualtamount'])) {
				dr_json(0, L('defualtamount').L('between_1_to_8_num'), array('field' => 'defualtamount'));
			}
			$this->db->update(array('module'=>'member', 'setting'=>array2string($member_setting)), array('module'=>'member'));
			$this->cache_api->cache('member_setting');
			$this->cache_api->del_file();
			dr_json(1, L('operation_success'));
		} else {
			$show_scroll = true;
			$member_setting = $this->db->get_one(array('module'=>'member'), 'setting');
			$member_setting = string2array($member_setting['setting']);
			$data['setting'] = $member_setting;
			$email_config = getcache('common', 'commons');
			$this->sms_setting_arr = getcache('sms','sms');
			$siteid = get_siteid();
			
			if(empty($email_config['mail_user']) || empty($email_config['mail_password'])) {
				$mail_disabled = 1;
			}
			
			if(module_exists('sms') && !empty($this->sms_setting_arr[$siteid])) {
				$this->sms_setting = $this->sms_setting_arr[$siteid];
				if(!$this->sms_setting['sms_enable']){
					$sms_disabled = 1;
				}
			} else {
				$sms_disabled = 1;
			}

			$list_view = $this->get_list_view();
			$data['setting']['list_field'] = $list_view->getAll();
			$page = intval($this->input->get('page'));
			$field = $list_view->getAll();
			include $this->admin_tpl('member_setting');
		}

	}

	// 测试正则表达式
	public function public_test_pattern() {
		$show_header = true;

		if (IS_POST) {

			$data = $this->input->post('data');
			if (!$data['text']) {
				dr_json(0, L('测试文字不能为空'));
			} elseif (!$data['code']) {
				dr_json(0, L('正则表达式不能为空'));
			}

			if (!preg_match($data['code'], $data['text'])) {
				dr_json(0, L('正则表达式验证结果：未通过'));
			}

			dr_json(1, L('正则表达式验证结果：通过'));
		}

		$code = array(
			'纯数字' => '/^[0-9]+$/',
			'纯汉字' => '/^[\\x{4e00}-\\x{9fa5}]+$/u',
			'手机号码' => '/^1[345789]\\d{9}$/ims',
			'电子邮箱' => '/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims',
		);

		include $this->admin_tpl('member_pattern');exit;
	}

	/**
	 * 会员内置字段
	 * 保留旧接口，字段定义统一由 member_list_view 提供。
	 */
	public function member_list_field() {
		return member_list_view::candidates();
	}
}
?>