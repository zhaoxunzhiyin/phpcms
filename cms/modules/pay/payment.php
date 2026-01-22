<?php
defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_app_func('global');
class payment extends admin {
	private $input, $email, $db, $account_db, $member_db, $modules_path, $method;
	function __construct() {
		parent::__construct();
		$this->input = pc_base::load_sys_class('input');
		$this->email = pc_base::load_sys_class('email');
		$this->db = pc_base::load_model('pay_payment_model');
		$this->account_db = pc_base::load_model('pay_account_model');
		$this->member_db = pc_base::load_model('member_model');
		$this->modules_path = PC_PATH.'modules'.DIRECTORY_SEPARATOR.'pay';
		pc_base::load_app_class('pay_method','','0');
		$this->method = new pay_method($this->modules_path);
	}
	/**
	 * 支付模块列表
	 */
	public function init() {
		$infos = $this->method->get_list();
		$show_dialog = true;
		include $this->admin_tpl('payment_list');
	}
	/*
	 * 增加支付模块
	 */
	public function add() {
		if(IS_POST) {
			$info = $infos = array();
			!$this->input->post('name') && dr_admin_msg(0,L('input').L('payment_mode').L('name'), array('field' => 'name'));
			$infos = $this->method->get_payment($this->input->post('pay_code'));
			$config = $infos['config'];
			foreach ($this->input->post('config_name') as $key => $value) {
				$config[$value]['value'] = trim($this->input->post('config_value')[$key]);
			}
			$info['config'] = array2string($config);
			$info['name'] = $this->input->post('name');
			$info['pay_name'] = $this->input->post('pay_name');
			$info['pay_desc'] = $this->input->post('description');
			$info['pay_id'] = $this->input->post('pay_id');
			$info['pay_code'] = $this->input->post('pay_code');
			$info['is_cod'] = $this->input->post('is_cod');
			$info['is_online'] = $this->input->post('is_online');
			$info['pay_fee'] = intval($this->input->post('pay_fee'));
			$info['pay_method'] = intval($this->input->post('pay_method'));
			$info['pay_order'] = intval($this->input->post('pay_order'));
			$info['enabled'] = '1';
			$info['author'] = $infos['author'];
			$info['website'] = $infos['website'];
			$info['version'] = $infos['version'];
			$this->db->insert($info);
			if($this->db->insert_id()){
				dr_admin_msg(1,L('operation_success'), '', '', 'add');
			}		
		} else {
			$infos = $this->method->get_payment($this->input->get('code'));
			extract($infos);
			$show_header = $show_validator = true;
			include $this->admin_tpl('payment_detail');			
		}
	}
	/*
	 * 编辑支付模块
	 */
	public function edit() {
		if(IS_POST) {
			!$this->input->post('name') && dr_admin_msg(0,L('input').L('payment_mode').L('name'), array('field' => 'name'));
			$infos = $this->method->get_payment($this->input->post('pay_code'));
			$config = $infos['config'];
			foreach ($this->input->post('config_name') as $key => $value) {
				$config[$value]['value'] = trim($this->input->post('config_value')[$key]);
			}
			$info['config'] = array2string($config);
			$info['name'] = trim($this->input->post('name'));
			$info['pay_name'] = trim($this->input->post('pay_name'));
			$info['pay_desc'] = trim($this->input->post('description'));
			$info['pay_id'] = $this->input->post('pay_id');
			$info['pay_code'] = trim($this->input->post('pay_code'));
			$info['pay_order'] = intval($this->input->post('pay_order'));
			$info['pay_method'] = intval($this->input->post('pay_method'));	
			$info['pay_fee']  = (intval($this->input->post('pay_method'))==0) ? intval($this->input->post('pay_rate')) : intval($this->input->post('pay_fix'));		
			$info['is_cod'] = trim($this->input->post('is_cod'));
			$info['is_online'] = trim($this->input->post('is_online'));
			$info['enabled'] = '1';
			$info['author'] = $infos['author'];
			$info['website'] = $infos['website'];
			$info['version'] = $infos['version'];
			$infos = $this->db->update($info,array('pay_id'=>$info['pay_id']));
			dr_admin_msg(1,L('edit').L('succ'), '', '', 'edit');						
		} else {
			$pay_id = intval($this->input->get('id'));
			$infos = $this->db->get_one(array('pay_id'=>$pay_id));
			extract($infos);
			$config = string2array($config);
			$show_header = $show_validator = true;
			include $this->admin_tpl('payment_detail');			
		}
	}
	
	/**
	 * 卸载支付模块
	 */
	public function delete() {
		$pay_id = intval($this->input->get('id'));
		if ($pay_id) {
			$this->db->delete(array('pay_id'=>$pay_id));
			dr_admin_msg(1,L('delete_succ'),'?m=pay&c=payment&menuid='.$this->input->get('menuid'));
		} else {
			dr_admin_msg(0, L('operation_failure'));
		}
	}
	
	/**
	 * 支付订单列表
	 */
	public function pay_list() {
		$where = '';
		if($this->input->get('dosubmit')){
			extract($this->input->get('info'));
			if($trade_sn) $where = "AND `trade_sn` LIKE '%$trade_sn%' ";
			if($username) $where = "AND `username` LIKE '%$username%' ";
			if($start_addtime && $end_addtime) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$where .= "AND `addtime` >= '$start' AND  `addtime` <= '$end'";				
			}
			if($status) $where .= "AND `status` LIKE '%$status%' ";			
			if($where) $where = substr($where, 3);
		}			
		$infos = array();
		foreach(L('select') as $key=>$value) {
			$trade_status[$key] = $value;
		}
		$page = $this->input->get('page') ? $this->input->get('page') : '1';
		
		$infos = $this->account_db->listinfo($where, $order = 'addtime DESC,id DESC', $page, SYS_ADMIN_PAGESIZE);
		$pages = $this->account_db->pages;
		$number = dr_count($infos);
		include $this->admin_tpl('pay_list');	
	}
	
	/**
	 * 财务统计
	 * Enter description here ...
	 */
	public function pay_stat() {
		$where = '';
		$infos = array();
		if($this->input->get('dosubmit')){
			extract($this->input->get('info'));
			if($username) $where = "AND `username` LIKE '%$username%' ";
			if($start_addtime && $end_addtime) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$where .= "AND `addtime` >= '$start' AND  `addtime` <= '$end'";				
			}
			if($status) $where .= "AND `status` LIKE '%$status%' ";			
			if($where) $where = substr($where, 3);
			$infos = $this->account_db->select($where);
			$num = dr_count($infos);
			foreach ($infos as $_v) {
				if($_v['type'] == 1) {
					$amount_num++;
					$amount += $_v['money']; 
					if($_v['status'] =='succ') {$amount_succ += $_v['money'];$amount_num_succ++;}
				}  elseif ($_v['type'] == 2) {
					$point_num++;
					$point += $_v['money']; 
					if($_v['status'] =='succ') {$point_succ += $_v['money'];$point_num_succ++;}
				}
			}			
		}		
		foreach(L('select') as $key=>$value) $trade_status[$key] = $value;		
		$total_infos = $this->account_db->select();
		$total_num= dr_count($total_infos);
		foreach ($total_infos as $_v) {
			if($_v['type'] == 1) {
				$total_amount_num++;
				$total_amount += $_v['money']; 
				if($_v['status'] =='succ') {$total_amount_succ += $_v['money'];$total_amount_num_succ++;}
			}  elseif ($_v['type'] == 2) {
				$total_point_num++;
				$total_point += $_v['money']; 
				if($_v['status'] =='succ') {$total_point_succ += $_v['money'];$total_point_num_succ++;}
			}			
		}
		include $this->admin_tpl('pay_stat');
	}
	
	/**
	 * 支付打折
	 * Enter description here ...
	 */
	public function discount() {
		if(IS_POST) {
			!$this->input->post('discount') && dr_admin_msg(0,L('input').L('discount'), array('field' => 'discount'));
			$discount = floatval($this->input->post('discount'));
			$id = intval($this->input->post('id'));
			$infos = $this->account_db->update(array('discount'=>$discount),array('id'=>$id));
			dr_admin_msg(1,L('public_discount_succ'), '', '', 'discount');			
		} else {
			$show_header = $show_validator = true;
			$id = intval($this->input->get('id'));
			$infos = $this->account_db->get_one(array('id'=>$id));
			$infos && extract($infos);
			include $this->admin_tpl('pay_discount');			
		}
	}
	
	/**
	 * 修改财务
	 * Enter description here ...
	 */
	public function modify_deposit() {
		if(IS_POST) {
			$username = $this->input->post('username') && trim($this->input->post('username')) ? trim($this->input->post('username')) : dr_admin_msg(0,L('username').L('error'));
			$usernote = $this->input->post('usernote') && trim($this->input->post('usernote')) ? trim($this->input->post('usernote')) : dr_admin_msg(0,L('usernote').L('error'));	
			$userinfo = $this->get_useid($username);
			if($userinfo) {	
				//如果增加金钱或点数，想pay_account 中记录数据
				if($this->input->post('pay_unit')) {
					$value = floatval($this->input->post('unit'));
					$payment = L('admin_recharge');
					$receipts = pc_base::load_app_class('receipts');
					$func = $this->input->post('pay_type') == '1' ? 'amount' :'point';
					$receipts->$func($value, $userinfo['userid'] , $username, create_sn(), 'offline', $payment, param::get_cookie('admin_username'), $status = 'succ',$usernote);					
					
				} else {
					$value = floatval($this->input->post('unit'));
					$msg = L('background_operation').$usernote;
					$spend = pc_base::load_app_class('spend');
					$func = $this->input->post('pay_type') == '1' ? 'amount' :'point';
					$spend->$func($value,$msg,$userinfo['userid'],$username,param::get_cookie('userid'),param::get_cookie('admin_username'));
				}
				if(intval($this->input->post('sendemail'))) {
					$op = $this->input->post('pay_unit') ? $value: '-'.$value;
					$op = $this->input->post('pay_type') ? $op.L('yuan') : $op.L('point');
					$msg = L('account_changes_notice_tips',array('username'=>$username,'time'=>date('Y-m-d H:i:s',SYS_TIME),'op'=>$op,'note'=>$usernote,'amount'=>$userinfo['amount'],'point'=>$userinfo['point']));
					$this->email->set();
					$this->email->send($userinfo['email'],L('send_account_changes_notice'),$msg);
				}
				dr_admin_msg(1,L('public_discount_succ'),HTTP_REFERER);	
			}
		} else {
			$show_validator = true;
			include $this->admin_tpl('modify_deposit');			
		}

	}
	
	/*
	 * 支付删除
	 */
	public function pay_del() {
		$id = intval($this->input->get('id'));
		if ($id) {
			$this->account_db->delete(array('id'=>$id));
			dr_admin_msg(1,L('delete_succ'),'?m=pay&c=payment&a=pay_list&menuid='.$this->input->get('menuid'));
		} else {
			dr_admin_msg(0, L('operation_failure'));
		}
	}
	
	/*
	 * 支付取消
	 */
	public function pay_cancel() {
		$id = intval($this->input->get('id'));
		$this->account_db->update(array('status'=>'cancel'),array('id'=>$id));
		dr_admin_msg(1,L('state_change_succ'),HTTP_REFERER);
	}
	/*
	 * 支付详情
	 */
	public function public_pay_detail() {
		$id = intval($this->input->get('id'));
		$infos = $this->account_db->get_one(array('id'=>$id));
		extract($infos);
		$show_header = true;
		include $this->admin_tpl('pay_detail');
	}
	
	public function public_check() {
		$id = intval($this->input->get('id'));
		$infos = $this->account_db->get_one(array('id'=>$id));
		$userinfo = $this->member_db->get_one(array('userid'=>$infos['userid']));
		$amount = $userinfo['amount'] + $infos['money'];
		$this->account_db->update(array('status'=>'succ','adminnote'=>param::get_cookie('admin_username')),array('id'=>$id));
		$this->member_db->update(array('amount'=>$amount),array('userid'=>$infos['userid']));
		dr_admin_msg(1,L('check_passed'),'?m=pay&c=payment&a=pay_list&menuid='.$this->input->get('menuid'));
	}
		
	private function get_useid($username) {
		$username = trim($username);
		if ($result = $this->member_db->get_one(array('username'=>$username))){
			return $result;
		} else {
			return false;
		}		
	}
	/**
	 * 检查用户名
	 * @param string $username	用户名
	 */
	public function public_checkname_ajax() {
		$username = $this->input->get('username') && trim($this->input->get('username')) ? trim($this->input->get('username')) : exit(0);
		if(CHARSET != 'utf-8') {
			$username = iconv('utf-8', CHARSET, $username);
		}
		$this->member_db = pc_base::load_model('member_model');
		if ($r = $this->member_db->get_one(array('username'=>$username))){
			exit(L('user_balance').$r['amount'].'  '.L('point').'  '.$r['point']);
		} else {
			exit('FALSE');
		}		
	}	
}
?>