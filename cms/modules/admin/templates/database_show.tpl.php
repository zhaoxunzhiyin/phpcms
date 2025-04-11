<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style type="text/css">
.table-checkable tr > td:first-child, .table-checkable tr > th:first-child {max-width: 140px!important;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="margin-top:20px;margin-bottom:30px;">
                <div style="margin-top: -20px"><span style="padding-left: 10px"><?php echo $tables;?></span></div>
    <div class="table-list">
        <table class="table table-striped table-bordered table-hover table-checkable dataTable">
            <thead>
            <tr class="heading">
                <th width="140" style="text-align: left;padding-left:9px"><?php echo L('字段');?></th>
                <th width="150"><?php echo L('描述');?></th>
                <th><?php echo L('类型');?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($structure as $i=>$t) {?>
            <tr class="odd gradeX">
                <td style="text-align: left;padding-left:9px"><?php echo $t['Field'];?></td>
                <td><?php echo ($t['Comment'] ? $t['Comment'] : $t['Field']);?></td>
                <td><?php echo $t['Type'];?></td>
            </tr>
            <?php }?>
            </tbody>
        </table>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>