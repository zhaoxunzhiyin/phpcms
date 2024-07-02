<?php
/**
 * 获取语音验证码接口
 */
defined('IN_CMS') or exit('No permission resources.');

exit(dr_get_merge(get_captcha()));
?>