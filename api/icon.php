<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 生成后缀图标
 */
exit(icon(pc_base::load_sys_class('input')->get('fileext')));
?>