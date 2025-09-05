<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取语音验证码接口
 */
exit(dr_get_merge(get_captcha()));
?>