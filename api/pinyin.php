<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 汉字转换拼音
 */
$name = dr_safe_replace(pc_base::load_sys_class('input')->get('name'));
$length = intval(pc_base::load_sys_class('input')->get('length')) ? intval(pc_base::load_sys_class('input')->get('length')) : 12;
if (!$name) {
	exit('');
}
$py = pc_base::load_sys_class('pinyin')->result($name);
if (strlen($py) > $length) {
	$sx = pc_base::load_sys_class('pinyin')->result($name, 0);
	if ($sx) {
		exit($sx);
	}
}
exit($py);