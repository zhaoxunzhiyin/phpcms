<?php
defined('IN_CMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'sms', 'parentid'=>'29', 'm'=>'sms', 'c'=>'sms', 'a'=>'init', 'data'=>'', 'icon'=>'fa fa-envelope', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'sms_setting', 'parentid'=>$parentid, 'm'=>'sms', 'c'=>'sms', 'a'=>'sms_setting', 'data'=>'', 'icon'=>'fa fa-cog', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'sms_pay_history', 'parentid'=>$parentid, 'm'=>'sms', 'c'=>'sms', 'a'=>'sms_pay_history', 'data'=>'', 'icon'=>'fa fa-history', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'sms_buy_history', 'parentid'=>$parentid, 'm'=>'sms', 'c'=>'sms', 'a'=>'sms_buy_history', 'data'=>'', 'icon'=>'fa fa-rmb', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'sms_api', 'parentid'=>$parentid, 'm'=>'sms', 'c'=>'sms', 'a'=>'sms_api', 'data'=>'', 'icon'=>'fa fa-envelope', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'sms_sent', 'parentid'=>$parentid, 'm'=>'sms', 'c'=>'sms', 'a'=>'sms_sent', 'data'=>'', 'icon'=>'fa fa-send', 'listorder'=>0, 'display'=>'1'));

$language = array(
				'sms'=>'短信平台',
				'sms_setting'=>'平台设置',
				'sms_pay_history'=>'消费记录',
				'sms_buy_history'=>'充值记录',
				'sms_api'=>'短信API',
				'sms_sent'=>'短信群发',
				);
?>