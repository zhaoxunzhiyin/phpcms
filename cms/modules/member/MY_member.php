<?php
/**
 * 会员控制器扩展
 *
 * 不直接修改 member.php，通过 PHPCMS 的 MY_ 控制器机制接入 ListView。
 */

defined('IN_CMS') or exit('No permission resources.');
pc_base::load_app_class('member_list_view', 'member', 0);
pc_base::load_sys_class('list_view', '', 0);

class MY_member extends member {

    private $list_view;

    public function __construct() {
        parent::__construct();

        $member_reflection = new ReflectionClass('member');
        $cache_property = $member_reflection->getProperty('member_cache');
        $cache_property->setAccessible(true);
        $member_cache = $cache_property->getValue($this);

        $userid = intval(param::get_session('userid'));
        $username = (string)param::get_session('username');
        $legacy_config = is_array($member_cache) && isset($member_cache['list_field']) ? $member_cache['list_field'] : array();

        $this->list_view = new list_view(
            member_list_view::scene(),
            member_list_view::candidates(),
            $userid,
            $username,
            $legacy_config
        );

        // member.php 的 init() 使用父类 private $list_field，这里通过 Reflection 注入最终用户配置。
        $field_property = $member_reflection->getProperty('list_field');
        $field_property->setAccessible(true);
        $field_property->setValue($this, $this->list_view->getVisible());
    }

    /**
     * 当前用户列表字段配置
     */
    public function list_view() {
        $show_header = true;
        $field = $this->list_view->getAll();
        $config = $this->list_view->getConfig();
        include $this->admin_tpl('member_list_view');
    }

    /**
     * 保存当前用户列表字段配置
     */
    public function list_view_save() {
        if (!IS_POST) {
            dr_json(0, L('illegal_parameters'));
        }

        $config = $this->input->post('config');
        if (!is_array($config)) {
            $config = array();
        }

        $validate = $this->list_view->validate($config);
        if (!$validate['code']) {
            dr_json(0, $validate['msg']);
        }

        if (!$this->list_view->save($config)) {
            dr_json(0, L('operation_failure'));
        }

        dr_json(1, L('operation_success'));
    }

    /**
     * 恢复当前用户默认列表字段配置
     */
    public function list_view_reset() {
        if (!$this->list_view->reset()) {
            dr_json(0, L('operation_failure'));
        }
        dr_json(1, L('operation_success'));
    }
}
?>