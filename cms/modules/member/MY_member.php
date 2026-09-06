<?php
/**
 * 会员控制器扩展
 *
 * 会员列表字段已经完全切换到通用 ListView。
 * 本扩展仅处理 legacy member.php 的 private $list_field 注入，
 * 不再读取或兼容 member_setting.list_field。
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('member_list_view', 'member', 0);
pc_base::load_sys_class('list_view', '', 0);

class MY_member extends member {

    public function __construct() {
        parent::__construct();

        $userid = intval(param::get_session('userid'));
        $username = (string)param::get_session('username');

        $list_view = new list_view(
            member_list_view::scene('list'),
            member_list_view::candidates('list'),
            $userid,
            $username
        );

        // legacy member.php 的 $list_field 是 private，MY_ 扩展无法直接赋值。
        // 这里仅作为旧控制器运行时桥接，不读取旧 member_setting.list_field。
        $reflection = new ReflectionClass('member');
        $property = $reflection->getProperty('list_field');
        $property->setAccessible(true);
        $property->setValue($this, $list_view->getVisible());
    }
}
?>