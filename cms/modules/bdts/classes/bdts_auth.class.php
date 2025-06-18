<?php
// 权限验证
class bdts_auth {

    private $data;

    public function __construct() {
        $this->data = pc_base::load_app_class('admin_bdts', 'bdts')->getConfig();
    }

    // 判断底部链接的显示权限
    public function is_bottom_auth($module) {

        if ($this->data['bottom']) {
            return 1;
        }

        return 0;
    }

    // 判断右侧链接的显示权限
    public function is_link_auth($module) {


        return 1;
    }

    // 动态link名称
    public function get_name($module) {
        return '批量百度主动推送';
    }
}