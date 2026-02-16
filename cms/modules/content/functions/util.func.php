<?php
/**
 * 内容分页函数
 * 
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $pageurls 链接地址
 * @return 分页
 */
function content_pages($num, $curr_page, $pageurls = array()) {
	$first_url = $pageurls[1][0];
	return $num > 0 ? pc_base::load_sys_class('input')->page($pageurls[2][0], $num, 1, $curr_page, $first_url) : '';
}
?>