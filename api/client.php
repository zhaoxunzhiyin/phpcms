<?php
/**
 * 电脑和手机网站切换处理接口
 */
defined('IN_CMS') or exit('No permission resources.');
header('Access-Control-Allow-Origin: *');

$siteid = pc_base::load_sys_class('input')->post('siteid');
$ismobile = pc_base::load_sys_class('input')->post('ismobile');
$url = urldecode(pc_base::load_sys_class('input')->post('url'));

$siteid = $siteid ? $siteid : get_siteid();
$config = siteinfo($siteid);

if (!$config) {
	dr_json(0, L('配置文件不存在'));
}

if (isset($config['mobileauto']) && $config['mobileauto']) {
	dr_json(0, L('系统已经开启自动识别移动端，此功能无效'));
}

$domain = $config['domain'];
$mobile_domain = $config['mobile_domain'];
if (!$domain) {
	dr_json(0, L('系统没有绑定电脑域名'));
}
if (!$mobile_domain) {
	dr_json(0, L('系统没有绑定手机域名'));
}
if ($ismobile) {
	dr_json(1, L('正在切换: '.$domain), array('url' => str_replace(array($mobile_domain, 'm=mobile'), array($domain, 'm=content'), $url)));
} else {
	dr_json(1, L('正在切换: '.$mobile_domain), array('url' => str_replace(array($domain, 'm=content'), array($mobile_domain, 'm=mobile'), $url)));
}
?>