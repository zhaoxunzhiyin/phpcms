<?php

// 判断网站是否关闭
if (!IS_DEV && !IS_ADMIN && !IS_API
    && get_cache('site', get_siteid(), 'config', 'site_close')
    && !cleck_admin(param::get_session('roleid'))) {
    // 挂钩点 网站关闭时
    pc_base::load_sys_class('hooks')::trigger('site_close');
    dr_msg(0, get_cache('site', get_siteid(), 'config', 'site_close_msg'));
}

// 站群系统接入
if (is_file(CMS_PATH.'api/fclient/sync.php')) {
    $sync = require CMS_PATH.'api/fclient/sync.php';
    if ($sync['status'] == 4) {
        if ($sync['close_url']) {
            dr_redirect($sync['close_url']);
        } else {
            dr_msg(0, L('网站被关闭'));
        }
    } elseif ($sync['status'] == 3 || ($sync['endtime'] && SYS_TIME > $sync['endtime'])) {
        if ($sync['pay_url']) {
            dr_redirect($sync['pay_url']);
        } else {
            dr_msg(0, L('网站已过期'));
        }
    }
}