<?php
if(dr_is_empty($setting['upload_maxsize'])) {
    dr_json(0, L('文件大小必须填写'), array('field' => 'upload_maxsize'));
}
?>