<?php
/**
 * 会员列表视图字段定义
 */

defined('IN_CMS') or exit('No permission resources.');

class member_list_view {

    public static function scene() {
        return 'member:list';
    }

    /**
     * 会员列表字段候选项
     * required=true：默认显示，并且用户不可取消。
     */
    public static function candidates() {
        return array(
            'avatar' => array(
                'name' => L('头像'),
                'formtype' => 'text',
                'field' => 'avatar',
                'setting' => array(),
                'required' => true,
            ),
            'userid' => array(
                'name' => L('用户ID'),
                'formtype' => 'text',
                'field' => 'userid',
                'setting' => array(),
                'required' => true,
            ),
            'groupid' => array(
                'name' => L('用户组'),
                'formtype' => 'text',
                'field' => 'groupid',
                'setting' => array(),
            ),
            'username' => array(
                'name' => L('账号'),
                'formtype' => 'text',
                'field' => 'username',
                'setting' => array(),
                'required' => true,
            ),
            'nickname' => array(
                'name' => L('昵称'),
                'formtype' => 'text',
                'field' => 'nickname',
                'setting' => array(),
                'required' => true,
            ),
            'email' => array(
                'name' => L('邮箱'),
                'formtype' => 'text',
                'field' => 'email',
                'setting' => array(),
            ),
            'mobile' => array(
                'name' => L('手机'),
                'formtype' => 'text',
                'field' => 'mobile',
                'setting' => array(),
            ),
            'amount' => array(
                'name' => L('余额'),
                'formtype' => 'text',
                'field' => 'amount',
                'setting' => array(),
            ),
            'point' => array(
                'name' => L('积分'),
                'formtype' => 'text',
                'field' => 'point',
                'setting' => array(),
            ),
            'regip' => array(
                'name' => L('注册IP'),
                'formtype' => 'text',
                'field' => 'regip',
                'setting' => array(),
            ),
            'regdate' => array(
                'name' => L('注册时间'),
                'formtype' => 'text',
                'field' => 'regdate',
                'setting' => array(),
            ),
        );
    }
}
?>