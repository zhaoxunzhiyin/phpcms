<?php
/**
 * 会员控制器扩展
 *
 * 仅负责将通用 ListView 的当前用户配置接入 legacy member.php。
 * ListView 设置 UI、保存、恢复默认均由 admin/list_view_config 统一处理。
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('member_list_view', 'member', 0);
pc_base::load_sys_class('list_view', '', 0);

class MY_member extends member {

    public function __construct() {
        parent::__construct();

        $member_reflection = new ReflectionClass('member');
        $cache_property = $member_reflection->getProperty('member_cache');
        $cache_property->setAccessible(true);
        $member_cache = $cache_property->getValue($this);

        $userid = intval(param::get_session('userid'));
        $username = (string)param::get_session('username');
        $legacy_config = is_array($member_cache) && isset($member_cache['list_field']) ? $member_cache['list_field'] : array();

        $list_view = new list_view(
            member_list_view::scene('list'),
            member_list_view::candidates('list'),
            $userid,
            $username,
            $legacy_config
        );

        // legacy member.php 使用父类 private $list_field，这里仅做运行时兼容注入。
        $field_property = $member_reflection->getProperty('list_field');
        $field_property->setAccessible(true);
        $field_property->setValue($this, $list_view->getVisible());
    }
}
?>