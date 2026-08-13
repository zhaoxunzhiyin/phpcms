<?php

/**
 * 自定义钩子
 *
 */

/**
 * 会员登录之前更新ip
 */
pc_base::load_sys_class('hooks')::app_on('member', 'member_login_before', function($member) {
    if (!$member) {
        return;
    }
    $input = pc_base::load_sys_class('input');
    $member_db = pc_base::load_model('members_model');
    $config = getcache('common','commons');
    $user = $member_db->get_one(array('username'=>$member['username']));
    if (!$user) {
        return;
    }
    if (isset($config['login_use']) && dr_in_array('member', $config['login_use'])) {
        $attr = '';
        if ((isset($config['login_city']) && $config['login_city'])) {
            $attr.= $input->ip_address();
        }
        if ((isset($config['login_llq']) && $config['login_llq'])) {
            $attr.= $input->get_user_agent();
        }
        if ($attr) {
            $log = member_get_log($user['userid']);
            $member_db->update(array('login_attr'=>md5($attr)), array('userid'=>$log['uid']));
        }
    }
});

// 会员登录后记录时间
pc_base::load_sys_class('hooks')::app_on('member', 'member_login_after', function($member) {
    if (!$member) {
        return;
    }
    $cache = pc_base::load_sys_class('cache');
    $member_db = pc_base::load_model('members_model');
    $member_login_db = pc_base::load_model('member_login_model');
    $config = getcache('common','commons');
    $user = $member_db->get_one(array('userid'=>$member['userid']));
    if (!$user) {
        return;
    }
    if (isset($config['safe_use']) && dr_in_array('member', $config['safe_use'])) {
        if (isset($config['safe_wdl']) && $config['safe_wdl']) {
            $log = member_get_log($member['userid']);
            $member_login_db->update(array('logintime' => SYS_TIME,), array('uid'=>$member['userid']));
        }
    }
    if (isset($config['login_use']) && dr_in_array('member', $config['login_use'])) {
        // 操作标记
        $cache->set_auth_data('member_option_'. $member['userid'], SYS_TIME, 1);
    }
});

// 会员修改密码之后
pc_base::load_sys_class('hooks')::app_on('member', 'member_edit_password_after', function($member) {
    $member_login_db = pc_base::load_model('member_login_model');
    $config = getcache('common','commons');
    if (isset($config['pwd_use']) && dr_in_array('member', $config['pwd_use'])) {
        $log = member_get_log($member['userid']);
        if (IS_ADMIN && ROUTE_M =='member' && ROUTE_C == 'member' && ROUTE_A == 'edit') {
            // 表示管理员重置密码
            $member_login_db->update(array(
                'is_repwd' => 0,
                'updatetime' => 0,
            ), array('uid'=>$member['userid']));
        } else {
            $member_login_db->update(array(
                'is_login' => SYS_TIME,
                'is_repwd' => SYS_TIME,
                'updatetime' => SYS_TIME,
            ), array('uid'=>$member['userid']));
        }
    }
});

// 每次运行
pc_base::load_sys_class('hooks')::app_on('member', 'cms_init', function() {

    if (IS_API) {
        return;
    }

    $cache = pc_base::load_sys_class('cache');
    $member_db = pc_base::load_model('members_model');
    $member_login_db = pc_base::load_model('member_login_model');
    $config = getcache('common','commons');
    if (isset($config['safe_use']) && dr_in_array('member', $config['safe_use'])) {
        // 长时间未登录的用户就锁定起来
        if (isset($config['safe_wdl']) && $config['safe_wdl']) {
            $time = $config['safe_wdl'] * 3600 * 24;
            $member_log_lock = $member_login_db->select('logintime < '.(SYS_TIME - $time));
            if ($member_log_lock) {
                foreach ($member_log_lock as $t) {
                    $member_db->update(array('islock'=>1), array('userid'=>$t['uid']));
                }
            }
        }
    }

    if (!IS_MEMBER) {
        return;
    }

    $cms_auth = param::get_cookie('auth');
    if ($cms_auth) {
        list($userid) = explode("\t", sys_auth($cms_auth, 'DECODE', get_auth_key('login')));
        $userid = intval($userid);
        if ($userid) {
            $log = member_get_log($userid);
            if (isset($config['pwd_use']) && dr_in_array('member', $config['pwd_use'])) {
                // 重置密码后首次登录是否强制修改密码
                if (!$log['is_repwd'] && isset($config['pwd_is_rlogin_edit']) && $config['pwd_is_rlogin_edit']) {
                    // 该改密码了
                    if (ROUTE_M =='member' && ROUTE_C == 'index' && in_array(ROUTE_A, array('account_manage_password','public_checkemail_ajax','logout'))) {
                        return; // 本身控制器不判断
                    }
                    dr_msg(0, L('首次登录需要强制修改密码'), '?m=member&c=index&a=account_manage_password&t=1');
                }
                // 首次登录是否强制修改密码
                if (!$log['is_login'] && isset($config['pwd_is_login_edit']) && $config['pwd_is_login_edit']) {
                    // 该改密码了
                    if (ROUTE_M =='member' && ROUTE_C == 'index' && in_array(ROUTE_A, array('account_manage_password','public_checkemail_ajax','logout'))) {
                        return true; // 本身控制器不判断
                    }
                    dr_msg(0, L('首次登录需要强制修改密码'), '?m=member&c=index&a=account_manage_password&t=1');
                }
                // 判断定期修改密码
                if (isset($config['pwd_is_edit']) && $config['pwd_is_edit']
                    && isset($config['pwd_day_edit']) && $config['pwd_day_edit']) {
                    if ($log['updatetime']) {
                        // 存在修改过密码才判断
                        $time = $config['pwd_day_edit'] * 3600 * 24;
                        if (SYS_TIME - $log['updatetime'] > $time) {
                            // 该改密码了
                            if (ROUTE_M =='member' && ROUTE_C == 'index' && in_array(ROUTE_A, array('account_manage_password','public_checkemail_ajax','logout'))) {
                                return true; // 本身控制器不判断
                            }
                            dr_msg(0, L('您需要定期修改密码'), '?m=member&c=index&a=account_manage_password&t=1');
                        }
                    }
                }
            }
            if (isset($config['login_use']) && dr_in_array('member', $config['login_use'])) {
                // 操作标记
                if (ROUTE_M =='member' && ROUTE_C == 'index' && in_array(ROUTE_A, array('login'))) {
                    return; // 本身控制器不判断
                }
                if (isset($config['login_is_option']) && $config['login_is_option'] && $config['login_exit_time']) {
                    $time = (int)$cache->get_auth_data('member_option_'.$userid, 1);
                    $ctime = SYS_TIME - $time;
                    if ($time && SYS_TIME - $time > $config['login_exit_time'] * 60) {
                        // 长时间不动作退出
                        $member_db->update(array('login_attr'=>rand(0, 99999)), array('userid'=>$log['uid']));
                        $cache->del_auth_data('member_option_'.$userid, 1);
                        param::set_cookie('auth', '');
                        param::set_cookie('_userid', '');
                        param::set_cookie('_login_attr', '');
                        param::set_cookie('_username', '');
                        param::set_cookie('_groupid', '');
                        param::set_cookie('_nickname', '');
                        dr_msg(0, L('长时间（'.ceil($ctime/60).'分钟）未操作，当前账号自动退出'),'?m=member&c=index&a=login');
                    }
                    $cache->set_auth_data('member_option_'.$userid, SYS_TIME, 1);
                }
            }
        }
        unset($userid, $cms_auth);
    }
});