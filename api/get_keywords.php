<?php
/**
 * 获取关键字接口
 */
defined('IN_CMS') or exit('No permission resources.');

echo dr_get_keywords($input->post('data'));
?>