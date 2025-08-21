<?php

/**
 * 自定义钩子
 *
 */

/**
 * 后台登录之前更新ip
 */
pc_base::load_sys_class('hooks')::app_on('admin', 'admin_login_before', function($admin) {
    if (!$admin) {
        return;
    }
    $input = pc_base::load_sys_class('input');
    $admin_db = pc_base::load_model('admin_model');
    $config = getcache('common','commons');
    $user = $admin_db->get_one(array('username'=>$admin['username']));
    if (!$user) {
        return;
    }
    if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
        $attr = '';
        if ((isset($config['login_city']) && $config['login_city'])) {
            $attr.= $input->ip_address();
        }
        if ((isset($config['login_llq']) && $config['login_llq'])) {
            $attr.= $input->get_user_agent();
        }
        if ($attr) {
            $log = admin_get_log($user['userid']);
            $admin_db->update(array('login_attr'=>md5($attr)), array('userid'=>$log['uid']));
        }
    }
});

// 后台登录后记录时间
pc_base::load_sys_class('hooks')::app_on('admin', 'admin_login_after', function($admin) {
    if (!$admin) {
        return;
    }
    $cache = pc_base::load_sys_class('cache');
    $admin_db = pc_base::load_model('admin_model');
    $admin_login_db = pc_base::load_model('admin_login_model');
    $config = getcache('common','commons');
    $user = $admin_db->get_one(array('userid'=>$admin['userid']));
    if (!$user) {
        return;
    }
    if (isset($config['safe_use']) && dr_in_array('admin', $config['safe_use'])) {
        if (isset($config['safe_wdl']) && $config['safe_wdl']) {
            $log = admin_get_log($admin['userid']);
            $admin_login_db->update(array('logintime' => SYS_TIME,), array('uid'=>$admin['userid']));
        }
    }
    if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
        // 操作标记
        $cache->set_auth_data('admin_option_'. $admin['userid'], SYS_TIME, 1);
    }
});

// 后台修改密码之后
pc_base::load_sys_class('hooks')::app_on('admin', 'admin_edit_password_after', function($admin) {
    $admin_login_db = pc_base::load_model('admin_login_model');
    $config = getcache('common','commons');
    if (isset($config['pwd_use']) && dr_in_array('admin', $config['pwd_use'])) {
        $log = admin_get_log($admin['userid']);
        if (IS_ADMIN && ROUTE_M =='admin' && ROUTE_C == 'admin_manage' && ROUTE_A == 'edit') {
            // 表示管理员重置密码
            $admin_login_db->update(array(
                'is_repwd' => 0,
                'updatetime' => 0,
            ), array('uid'=>$admin['userid']));
        } else {
            $admin_login_db->update(array(
                'is_login' => SYS_TIME,
                'is_repwd' => SYS_TIME,
                'updatetime' => SYS_TIME,
            ), array('uid'=>$admin['userid']));
        }
    }
});

// 每次运行
pc_base::load_sys_class('hooks')::app_on('admin', 'cms_init', function() {

    if (IS_API) {
        return;
    }

    $cache = pc_base::load_sys_class('cache');
    $admin_db = pc_base::load_model('admin_model');
    $admin_login_db = pc_base::load_model('admin_login_model');
    $config = getcache('common','commons');
    if (isset($config['safe_use']) && dr_in_array('admin', $config['safe_use'])) {
        // 长时间未登录的用户就锁定起来
        if (isset($config['safe_wdl']) && $config['safe_wdl']) {
            $time = $config['safe_wdl'] * 3600 * 24;
            $log_lock = $admin_login_db->select('logintime < '.(SYS_TIME - $time));
            if ($log_lock) {
                foreach ($log_lock as $t) {
                    if (ADMIN_FOUNDERS && !dr_in_array($t['uid'], ADMIN_FOUNDERS)) {
                        $admin_db->update(array('islock'=>1), array('userid'=>$t['uid']));
                    }
                }
            }
        }
    }

    if (!IS_ADMIN) {
        return;
    }

    $userid = intval(param::get_session('userid')) ? intval(param::get_session('userid')) : param::get_cookie('userid');
    if ($userid) {
        $log = admin_get_log($userid);
        if (isset($config['pwd_use']) && dr_in_array('admin', $config['pwd_use'])) {
            // 重置密码后首次登录是否强制修改密码
            if (!$log['is_repwd'] && isset($config['pwd_is_rlogin_edit']) && $config['pwd_is_rlogin_edit']) {
                // 该改密码了
                if (ROUTE_M =='admin' && in_array(ROUTE_C, array('index','admin_manage')) && in_array(ROUTE_A, array(SYS_ADMIN_PATH,'public_edit_pwd'))) {
                    return; // 本身控制器不判断
                }
                dr_admin_msg(0, L('首次登录需要强制修改密码'), '?m=admin&c=admin_manage&a=public_edit_pwd');
            }
            // 首次登录是否强制修改密码
            if (!$log['is_login'] && isset($config['pwd_is_login_edit']) && $config['pwd_is_login_edit']) {
                // 该改密码了
                if (ROUTE_M =='admin' && in_array(ROUTE_C, array('index','admin_manage')) && in_array(ROUTE_A, array(SYS_ADMIN_PATH,'public_edit_pwd'))) {
                    return true; // 本身控制器不判断
                }
                dr_admin_msg(0, L('首次登录需要强制修改密码'), '?m=admin&c=admin_manage&a=public_edit_pwd');
            }
            // 判断定期修改密码
            if (isset($config['pwd_is_edit']) && $config['pwd_is_edit']
                && isset($config['pwd_day_edit']) && $config['pwd_day_edit']) {
                if ($log['updatetime']) {
                    // 存在修改过密码才判断
                    $time = $config['pwd_day_edit'] * 3600 * 24;
                    if (SYS_TIME - $log['updatetime'] > $time) {
                        // 该改密码了
                        if (ROUTE_M =='admin' && in_array(ROUTE_C, array('index','admin_manage')) && in_array(ROUTE_A, array(SYS_ADMIN_PATH,'public_edit_pwd'))) {
                            return true; // 本身控制器不判断
                        }
                        dr_admin_msg(0, L('您需要定期修改密码'), '?m=admin&c=admin_manage&a=public_edit_pwd');
                    }
                }
            }
        }
        if (isset($config['login_use']) && dr_in_array('admin', $config['login_use'])) {
            // 操作标记
            if (ROUTE_M =='admin' && ROUTE_C == 'index' && in_array(ROUTE_A, array(SYS_ADMIN_PATH))) {
                return; // 本身控制器不判断
            }
            if (isset($config['login_is_option']) && $config['login_is_option'] && $config['login_exit_time']) {
                $time = (int)$cache->get_auth_data('admin_option_'.$userid, 1);
                $ctime = SYS_TIME - $time;
                if ($time && SYS_TIME - $time > $config['login_exit_time'] * 60) {
                    // 长时间不动作退出
                    $admin_db->update(array('login_attr'=>rand(0, 99999)), array('userid'=>$log['uid']));
                    $cache->del_auth_data('admin_option_'.$userid, 1);
                    param::del_session('userid');
                    param::del_session('login_attr');
                    param::del_session('roleid');
                    param::del_session('lock_screen');
                    param::del_session(COOKIE_PRE.ip().'pc_hash');
                    param::set_cookie('admin_username','');
                    param::set_cookie('siteid','');
                    param::set_cookie('userid',0);
                    param::set_cookie('login_attr', '');
                    param::set_cookie('admin_email', '');
                    param::set_cookie('sys_lang', '');
                    dr_admin_msg(0, L('长时间（'.ceil($ctime/60).'分钟）未操作，当前账号自动退出'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
                }
                $cache->set_auth_data('admin_option_'.$userid, SYS_TIME, 1);
            }
        }
    }
});