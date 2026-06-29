<?php
defined('IN_CMS') or exit('No permission resources.'); 
class respond {
	
	private $input, $pay_db, $account_db, $member_db;
	
	function __construct() {
		$this->input = pc_base::load_sys_class('input');
		pc_base::load_app_func('global');
	}
	
	/**
	 * return_url get形式响应
	 */
	public function respond_get() {
		if ($this->input->get('code')){
			$payment = $this->get_by_code($this->input->get('code'));
			if(!$payment) showmessage(L('payment_failed'));
			$cfg = unserialize_config($payment['config']);
			$pay_name = ucwords($payment['pay_code']);
			pc_base::load_app_class('pay_factory','',0);
			$payment_handler = new pay_factory($pay_name, $cfg);
			$return_data = $payment_handler->receive();
			if($return_data) {
				if($return_data['order_status'] == 0) {				
					$this->update_member_amount_by_sn($return_data['order_id']);
				}
				$this->update_recode_status_by_sn($return_data['order_id'],$return_data['order_status']);
				showmessage(L('pay_success'),APP_PATH.'index.php?m=pay&c=deposit');
			} else {
				showmessage(L('pay_failed'),APP_PATH.'index.php?m=pay&c=deposit');
			}
		} else {
			showmessage(L('pay_success'));
		}
	}

	/**
	 * 服务器端 POST形式响应
	 */
	public function respond_post() {
		$code = $this->input->post('code') ? $this->input->post('code') : $this->input->get('code');
		if ($code){
			$payment = $this->get_by_code($code);
			if(!$payment) log_message('error', '付款无效');
			$cfg = unserialize_config($payment['config']);
			$pay_name = ucwords($payment['pay_code']);
			pc_base::load_app_class('pay_factory','',0);
			$payment_handler = new pay_factory($pay_name, $cfg);
			$return_data = $payment_handler->notify();
			if($return_data) {
				if($return_data['order_status'] == 0) {
					$this->update_member_amount_by_sn($return_data['order_id']);
				}
				$this->update_recode_status_by_sn($return_data['order_id'],$return_data['order_status']);				
                $result = TRUE;
			} else {
				$result = FALSE;
			}
			$payment_handler->response($result);
		}
	}

	/**
	 * 更新订单状态
	 * @param unknown_type $trade_sn 订单ID
	 * @param unknown_type $status 订单状态
	 */
	private function update_recode_status_by_sn($trade_sn,$status) {
		$trade_sn = trim($trade_sn);
		$status = trim(intval($status));
		$data = array();
		$this->account_db = pc_base::load_model('pay_account_model');
		$status = return_status($status);
		$data = array('status'=>$status);
		return $this->account_db->update($data,array('trade_sn'=>$trade_sn));
	}

	/**
	 * 更新用户账户余额
	 * @param unknown_type $trade_sn
	 */
	private function update_member_amount_by_sn($trade_sn) {
		$data = $userinfo = array();
		$this->member_db = pc_base::load_model('member_model');
		$orderinfo = $this->get_userinfo_by_sn($trade_sn);
	
		$userinfo = $this->member_db->get_one(array('userid'=>$orderinfo['userid']));
		if($orderinfo){
			$money = floatval($orderinfo['money']);
			$amount = $userinfo['amount'] + $money;
			$data = array('amount'=>$amount);
			return $this->member_db->update($data,array('userid'=>$orderinfo['userid']));
		} else {
			log_message('error', '重新更改失败！trade_sn:'.$trade_sn);
			return false;
		}
	}
	
	/**
	 * 通过订单ID抓取用户信息
	 * @param unknown_type $trade_sn
	 */
	private function get_userinfo_by_sn($trade_sn) {
		$trade_sn = trim($trade_sn);
		$this->account_db = pc_base::load_model('pay_account_model');
		$result = $this->account_db->get_one(array('trade_sn'=>$trade_sn));
		$status_arr = array('succ','failed','error','timeout','cancel');
		return ($result && !in_array($result['status'],$status_arr)) ? $result : false;
	}
	
	/**
	 * 通过支付代码获取支付信息
	 * @param unknown_type $code
	 */
	private function get_by_code($code) {
		$result = array();
		$code = trim($code);
		$this->pay_db = pc_base::load_model('pay_payment_model');
		$result = $this->pay_db->get_one(array('pay_code'=>$code));
		return $result;
	}
}
?>