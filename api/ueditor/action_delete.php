<?php
defined('IN_CMS') or exit('No permission resources.');

/**
 * 删除文件
 */
if ($userid) {
    /* 获取路径 */
    $path = $input->post('path');

    if ($path) {
        /* 删除数据 */
        $thisdb= pc_base::load_model('attachment_model');
        $data = $thisdb->delete(array('filepath'=>str_replace(WEB_PATH.'uploadfile/','',$path)));

        /* 获取完整路径 */
        $path = str_replace('../', '', $path);
        $path = str_replace('/', '\\', $path);
        $path = $_SERVER['DOCUMENT_ROOT'].$path;
        if(file_exists($path)) {
            //删除文件
            unlink($path);
            $result = json_encode(array(
                'code'=> '1',
                'state'=> '文件删除成功。'
            ), JSON_UNESCAPED_UNICODE);
        } else {
            $result = json_encode(array(
                'code'=> '0',
                'state'=> '文件删除失败，未找到'.$path
            ), JSON_UNESCAPED_UNICODE);
        }
    } else {
        $result = json_encode(array(
            'code'=> '0',
            'state'=> '文件删除失败，未找到'.$path
        ), JSON_UNESCAPED_UNICODE);
    }
} else {
    $result = json_encode(array(
        'code'=> '0',
        'state'=> '请登录在操作'
    ), JSON_UNESCAPED_UNICODE);
}

return $result;