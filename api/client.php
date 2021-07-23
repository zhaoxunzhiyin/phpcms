<?php
/**
 * 电脑和手机网站切换处理接口
 */
defined('IN_CMS') or exit('No permission resources.');
define('ROUTE_M', '');
$siteinfo = getcache('sitelist', 'commons');

$siteid = $input->post('siteid');
$ismobile = $input->post('ismobile');
$ishtml = $input->post('ishtml');
$url = urldecode($input->post('url'));

$siteid = $siteid ? $siteid : (get_siteid() ? get_siteid() : SITE_ID);
$config = $siteinfo[$siteid];

if (!$ishtml && isset($config['mobileauto']) && $config['mobileauto']) {
	dr_json(0, L('系统已经开启自动识别移动端，此功能无效'));
}

if (!$siteinfo) {
	dr_json(0, L('配置文件不存在'));
}

$domain = $config['domain'];
$mobile_domain = $config['mobile_domain'];
if (!$domain) {
	dr_json(0, L('系统没有绑定电脑域名'));
}
if (!$mobile_domain) {
	dr_json(0, L('系统没有绑定手机域名'));
}
if ($ishtml) {
	if ($ismobile) {
		dr_json(1, L('正在切换: '.$domain), array('url' => str_replace($mobile_domain, $domain, $url)));
	} else {
		dr_json(1, L('正在切换: '.$mobile_domain), array('url' => str_replace($domain, $mobile_domain, $url)));
	}
} else {
	if ($ismobile) {
		dr_json(1, L('正在切换: '.$domain), array('url' => str_replace(array($mobile_domain, 'mobile'), array($domain, 'content'), $url)));
	} else {
		dr_json(1, L('正在切换: '.$mobile_domain), array('url' => str_replace(array($domain, 'content'), array($mobile_domain, 'mobile'), $url)));
	}
}
?>