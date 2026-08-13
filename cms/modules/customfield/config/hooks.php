<?php
/**
 * 全局变量调用
 *
 * @param   string  $catname   分类名
 * @param   string  $name      变量名
 * @param   string  $siteid    站点ID
 * @return
 */
function dr_var_value($catname, $name, $siteid = 0) {
	if (!$siteid) $siteid = get_siteid();
	return get_cache('fieldlist', 'data', $siteid, $catname, $name);
}