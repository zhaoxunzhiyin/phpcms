<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 删除文件
 */
if ($userid) {
    /* 获取路径 */
    $aid = pc_base::load_sys_class('input')->post('id');

    if ($aid) {
        /* 删除数据 */
        $data['aid'] = $aid;
        $rt = pc_base::load_sys_class('upload')->_delete_file($data);
        if (!$rt['code']) {
            $result = json_encode(array(
                'code'=> '0',
                'state'=> $rt['msg']
            ), JSON_UNESCAPED_UNICODE);
        }
        $result = json_encode(array(
            'code'=> '1',
            'state'=> '删除成功。'
        ), JSON_UNESCAPED_UNICODE);
    } else {
        $result = json_encode(array(
            'code'=> '0',
            'state'=> '所选附件不存在。'
        ), JSON_UNESCAPED_UNICODE);
    }
} else {
    $result = json_encode(array(
        'code'=> '0',
        'state'=> '请登录在操作'
    ), JSON_UNESCAPED_UNICODE);
}

return $result;