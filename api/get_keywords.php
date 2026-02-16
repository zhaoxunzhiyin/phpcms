<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 获取关键字接口
 */
exit(dr_get_keywords(dr_safe_replace(pc_base::load_sys_class('input')->post('data'))));
?>