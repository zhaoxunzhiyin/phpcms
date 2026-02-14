<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th width="60">ID</th>
            <th width="100"><?php echo L('respective_modules');?></th>
            <th width="80"><?php echo L('rulename');?></th>
            <th width="120"><?php echo L('urlrule_ishtml');?></th>
            <th><?php echo L('urlrule_example');?></th>
            <th><?php echo L('urlrule_url');?></th>
            <th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php foreach($infos as $r) { ?>
    <tr>
        <td align='center'><?php echo $r['urlruleid'];?></td>
        <td align="center"><?php echo $r['module'];?></td>
        <td align="center"><?php echo $r['file'];?></td>
        <td align="center"><?php echo $r['ishtml'] ? L('icon_unlock') : L('icon_locked');?></td>
        <td><?php echo $r['example'];?></td>
        <td><?php echo $r['urlrule'];?></td>
        <td align='center' ><a class="btn btn-xs green" href="javascript:edit('<?php echo $r['urlruleid']?>')"> <i class="fa fa-edit"></i> <?php echo L('edit');?></a><a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=urlrule&a=delete&urlruleid=<?php echo $r['urlruleid'];?>&menuid=<?php echo $this->input->get('menuid');?>','<?php echo L('confirm',array('message'=>$r['urlruleid']));?>')"> <i class="fa fa-trash"></i> <?php echo L('delete');?></a> </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript"> 
<!--
function edit(id) {
    artdialog('edit','?m=admin&c=urlrule&a=edit&urlruleid='+id,'<?php echo L('edit_urlrule');?>《'+id+'》',750,500);
}
//-->
</script>
</body>
</html>
