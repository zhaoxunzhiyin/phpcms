<?php
// 权限验证
class comment_auth {

    private $data;

    public function __construct() {
        $this->data = pc_base::load_model('comment_setting_model')->get_one(array('siteid'=>get_siteid()));
    }

    // 判断底部链接的显示权限
    public function is_bottom_auth($module) {


        return 1;
    }

    // 判断右侧链接的显示权限
    public function is_link_auth($module) {

        if ($this->data['link']) {
            return 1;
        }

        return 0;
    }

    // 动态link名称
    public function get_name($module) {
        return '评论';
    }
}