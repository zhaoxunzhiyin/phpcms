<?php
// 已通过时，过期改变状态
$rows = pc_base::load_model('fclient_model')->select(array('status'=>2, 'endtime>'=>0, 'endtime<'=>SYS_TIME));
if ($rows) {
    foreach ($rows as $data) {
        pc_base::load_model('fclient_model')->update(array('status'=>3), array('id'=>$data['id']));
    }
}