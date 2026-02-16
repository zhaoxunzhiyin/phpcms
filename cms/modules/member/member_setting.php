<?php
/**
 * 管理员后台会员模块设置
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('format', '', 0);

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
			if ($setting['list_field']) {
				foreach ($setting['list_field'] as $t) {
					if ($t['func']
						&& !method_exists(pc_base::load_sys_class('function_list'), $t['func']) && !function_exists($t['func'])) {
						dr_json(0, L('列表回调函数['.$t['func'].']未定义'));
					}
				}
			}
			$member_setting['list_field'] = dr_list_field_order($setting['list_field']);
			if (!isset($member_setting['list_field']) || !$member_setting['list_field']) {
				$member_setting['list_field'] = array (
					'avatar' =>
						array (
							'use' => '1',
							'name' => '头像',
							'width' => '60',
							'func' => 'avatar',
						),
					'username' =>
						array (
							'use' => '1',
							'name' => '账号',
							'width' => '110',
							'func' => 'author',
						),
					'nickname' =>
						array (
							'use' => '1',
							'name' => '昵称',
							'width' => '120',
							'func' => '',
						),
					'amount' =>
						array (
							'use' => '1',
							'name' => '余额',
							'width' => '120',
							'func' => 'money',
						),
					'point' =>
						array (
							'use' => '1',
							'name' => '积分',
							'width' => '120',
							'func' => 'score',
						),
					'regip' =>
						array (
							'use' => '1',
							'name' => '注册IP',
							'width' => '140',
							'func' => 'ip',
						),
					'regdate' =>
						array (
							'use' => '1',
							'name' => '注册时间',
							'width' => '160',
							'func' => 'datetime',
						),
				);
			}
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
 			
			if (!isset($data['setting']['list_field']) || !$data['setting']['list_field']) {
				$data['setting']['list_field'] = array (
					'avatar' =>
						array (
							'use' => '1',
							'name' => '头像',
							'width' => '60',
							'func' => 'avatar',
						),
					'username' =>
						array (
							'use' => '1',
							'name' => '账号',
							'width' => '110',
							'func' => 'author',
						),
					'nickname' =>
						array (
							'use' => '1',
							'name' => '昵称',
							'width' => '120',
							'func' => '',
						),
					'amount' =>
						array (
							'use' => '1',
							'name' => '余额',
							'width' => '120',
							'func' => 'money',
						),
					'point' =>
						array (
							'use' => '1',
							'name' => '积分',
							'width' => '120',
							'func' => 'score',
						),
					'regip' =>
						array (
							'use' => '1',
							'name' => '注册IP',
							'width' => '140',
							'func' => 'ip',
						),
					'regdate' =>
						array (
							'use' => '1',
							'name' => '注册时间',
							'width' => '160',
							'func' => 'datetime',
						),
				);
			}
			$page = intval($this->input->get('page'));
			$field = dr_list_field_value($data['setting']['list_field'], $this->member_list_field(), '');
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
			'纯汉字' => '/^[\x{4e00}-\x{9fa5}]+$/u',
			'手机号码' => '/^1[345789]\d{9}$/ims',
			'电子邮箱' => '/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims',
		);

		include $this->admin_tpl('member_pattern');exit;
	}

	/**
	 * 会员内置字段
	 */
	public function member_list_field() {
		return array(
			'avatar' => array(
				'name' => L('头像'),
				'formtype' => 'text',
				'field' => 'avatar',
				'setting' => array()
			),
			'userid' => array(
				'name' => L('用户ID'),
				'formtype' => 'text',
				'field' => 'userid',
				'setting' => array()
			),
			'groupid' => array(
				'name' => L('用户组'),
				'formtype' => 'text',
				'field' => 'groupid',
				'setting' => array()
			),
			'username' => array(
				'name' => L('账号'),
				'formtype' => 'text',
				'field' => 'username',
				'setting' => array()
			),
			'nickname' => array(
				'name' => L('昵称'),
				'formtype' => 'text',
				'field' => 'nickname',
				'setting' => array()
			),
			'email' => array(
				'name' => L('邮箱'),
				'formtype' => 'text',
				'field' => 'email',
				'setting' => array()
			),
			'mobile' => array(
				'name' => L('手机'),
				'formtype' => 'text',
				'field' => 'mobile',
				'setting' => array()
			),
			'amount' => array(
				'name' => L('余额'),
				'formtype' => 'text',
				'field' => 'amount',
				'setting' => array()
			),
			'point' => array(
				'name' => L('积分'),
				'formtype' => 'text',
				'field' => 'point',
				'setting' => array()
			),
			'regip' => array(
				'name' => L('注册IP'),
				'formtype' => 'text',
				'field' => 'regip',
				'setting' => array()
			),
			'regdate' => array(
				'name' => L('注册时间'),
				'formtype' => 'text',
				'field' => 'regdate',
				'setting' => array()
			),
		);
	}
}
?>