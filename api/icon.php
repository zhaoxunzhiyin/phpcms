<?php
/**
 * 生成后缀图标
 */
defined('IN_CMS') or exit('No permission resources.');

exit(icon(pc_base::load_sys_class('input')->get('fileext')));
?>