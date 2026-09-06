<?php
/**
 * 会员列表视图字段定义
 *
 * scene 约定：member:{scope}
 */

defined('IN_CMS') or exit('No permission resources.');

class member_list_view {

    public static function scene($scope = 'list') {
        return 'member:' . strtolower(trim($scope));
    }

    /**
     * 会员列表字段候选项。
     *
     * @param string $scope
     * @return array
     */
    public static function candidates($scope = 'list') {
        $scope = strtolower(trim($scope));

        // 当前 member 只有 list 场景；后续增加 export/import 时，
        // 在本业务类内按 scope 返回对应字段，不需要修改通用 ListView。
        switch ($scope) {
            case 'list':
            default:
                return self::list_candidates();
        }
    }

    private static function list_candidates() {
        return array(
            'usercode' => array(
                'name' => L('用户标识'),
                'formtype' => 'text',
                'field' => 'usercode',
                'setting' => array(),
                'width' => '100',
                'func' => '',
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
                'width' => '110',
                'func' => 'author',
                'required' => true,
            ),
            'email' => array(
                'name' => L('邮箱'),
                'formtype' => 'text',
                'field' => 'email',
                'setting' => array(),
            ),
            'phone' => array(
                'name' => L('电话'),
                'formtype' => 'text',
                'field' => 'phone',
                'setting' => array(),
            ),
            'amount' => array(
                'name' => L('余额'),
                'formtype' => 'text',
                'field' => 'amount',
                'setting' => array(),
                'width' => '120',
                'func' => 'money',
                'required' => false,
            ),
            'regip' => array(
                'name' => L('注册IP'),
                'formtype' => 'text',
                'field' => 'regip',
                'setting' => array(),
                'width' => '140',
                'func' => 'ip',
                'required' => false,
            ),
            'regdate' => array(
                'name' => L('注册时间'),
                'formtype' => 'text',
                'field' => 'regdate',
                'setting' => array(),
                'width' => '160',
                'func' => 'datetime',
                'required' => false,
            ),
        );
    }
}
?>
