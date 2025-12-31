<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 删除文件
 */
$userid = $this->config['userid'];
$isadmin = $this->config['isadmin'];
$roleid = $this->config['roleid'];
if ($userid) {
    if ($isadmin && $roleid && cleck_admin($roleid)) {
        /* 获取路径 */
        $aid = pc_base::load_sys_class('input')->post('id');

        if ($aid) {
            /* 删除数据 */
            $data['aid'] = $aid;
            $rt = pc_base::load_sys_class('upload')->_delete_file($data);
            if (!$rt['code']) {
                $result = dr_array2string(array(
                    'code'=> '0',
                    'state'=> $rt['msg']
                ));
            }
            $result = dr_array2string(array(
                'code'=> '1',
                'state'=> '删除成功。'
            ));
        } else {
            $result = dr_array2string(array(
                'code'=> '0',
                'state'=> '所选附件不存在。'
            ));
        }
    } else {
        $result = dr_array2string(array(
            'code'=> '0',
            'state'=> '需要超级管理员账号操作'
        ));
    }
} else {
    $result = dr_array2string(array(
        'code'=> '0',
        'state'=> '请登录在操作'
    ));
}

return $result;