<?php
if(!isset($setting['step']) || !$setting['step']) {
    dr_json(0, L('步长值必须填写'), array('field' => 'step'));
}
?>