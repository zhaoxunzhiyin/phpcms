<?php
/**
 * 获取语音验证码接口
 */
defined('IN_CMS') or exit('No permission resources.');

//生成语音验证码
$str = '';
if (is_numeric(strtolower(substr(param::get_cookie('code'),0,1)))) {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.rand(1, 4).'_'.strtolower(substr(param::get_cookie('code'),0,1)).'.mp3');
} else {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.strtolower(substr(param::get_cookie('code'),0,1)).'.mp3');
}
if (is_numeric(strtolower(substr(param::get_cookie('code'),1,1)))) {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.rand(1, 4).'_'.strtolower(substr(param::get_cookie('code'),1,1)).'.mp3');
} else {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.strtolower(substr(param::get_cookie('code'),1,1)).'.mp3');
}
if (is_numeric(strtolower(substr(param::get_cookie('code'),2,1)))) {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.rand(1, 4).'_'.strtolower(substr(param::get_cookie('code'),2,1)).'.mp3');
} else {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.strtolower(substr(param::get_cookie('code'),2,1)).'.mp3');
}
if (is_numeric(strtolower(substr(param::get_cookie('code'),3,1)))) {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.rand(1, 4).'_'.strtolower(substr(param::get_cookie('code'),3,1)).'.mp3');
} else {
	$str .= dr_catcher_data('file://'.PC_PATH.'libs/data/voice/'.strtolower(substr(param::get_cookie('code'),3,1)).'.mp3');
}
param::set_cookie('code','');
exit($str);
?>