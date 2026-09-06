<?php
/**
 * 通用列表视图设置
 *
 * 负责 ListView 的后台 HTTP 入口及设置 UI。
 * 业务字段由 scene 对应的业务类提供，本控制器不定义任何业务字段。
 */

defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
pc_base::load_app_class('admin', 'admin', 0);
pc_base::load_sys_class('list_view', '', 0);
pc_base::load_app_class('list_view_provider', 'admin', 0);

class list_view_config extends admin {

    private $input;

    public function __construct() {
        parent::__construct();
        $this->input = pc_base::load_sys_class('input');
    }

    /**
     * 通用 ListView 入口。
     *
     * GET：打开当前用户的设置 UI
     * POST action=save：保存当前用户配置
     * POST action=reset：删除当前用户配置，恢复默认
     */
    public function public_list_view() {
        $userid = intval(param::get_session('userid'));
        if ($userid <= 0) {
            if (IS_POST) {
                dr_json(0, L('请先登录'));
            }
            showmessage(L('请先登录'));
        }

        $scene = trim((string)$this->input->get('scene'));
        if (!$scene) {
            if (IS_POST) {
                dr_json(0, L('列表场景不能为空'));
            }
            showmessage(L('列表场景不能为空'));
        }

        $candidates = list_view_provider::candidates($scene);
        if ($candidates === false) {
            if (IS_POST) {
                dr_json(0, L('列表场景不存在或未定义字段'));
            }
            showmessage(L('列表场景不存在或未定义字段'));
        }

        $username = (string)param::get_session('username');
        $list_view = new list_view($scene, $candidates, $userid, $username);
        $action = IS_POST ? trim((string)$this->input->post('action')) : 'config';

        if ($action == 'save') {
            $config = $this->input->post('config');
            if (!is_array($config)) {
                $config = array();
            }

            $validate = $list_view->validate($config);
            if (!$validate['code']) {
                dr_json(0, $validate['msg']);
            }

            if (!$list_view->save($config, $userid, $username)) {
                dr_json(0, L('operation_failure'));
            }

            dr_json(1, L('operation_success'));
        }

        if ($action == 'reset') {
            if (!$list_view->reset($userid)) {
                dr_json(0, L('operation_failure'));
            }

            dr_json(1, L('operation_success'));
        }

        if (IS_POST) {
            dr_json(0, L('illegal_parameters'));
        }

        $scene = htmlspecialchars($scene, ENT_QUOTES, CHARSET);
        $field = $list_view->getAll();
        $config = $list_view->getConfig();
        $show_header = true;
        include $this->admin_tpl('list_view');
    }
}
?>