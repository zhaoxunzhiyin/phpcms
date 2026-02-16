<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * ip地址接口
 */
$value = dr_safe_replace(pc_base::load_sys_class('input')->get('value'));
if (!$value) {
	exit(L('IP地址为空'));
}

list($value, $port) = explode('-', $value);
$address = pc_base::load_sys_class('input')->ip2address($value);
echo '<a href="https://www.baidu.com/s?wd='.$value.'&action=cms" target="_blank" style="color: #337ab7;">'.L('IP归属地：'.$address).'</a>';
if ($port) {
	echo '<br>'.L('源端口号：'.(int)$port);
}
exit;
?>