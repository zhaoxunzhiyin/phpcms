<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=message&c=message&a=delete_group" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th><?php echo L('subject')?></th>
            <th width="300" align="center"><?php echo L('content')?></th>
            <th width="160" align="center"><?php echo L('sendtime')?></th>
            <th width='120' align="center"><?php echo L('status')?></th>
            <th align="center"><?php echo L('operations_manage')?></th>
        </tr>
    </thead>
<tbody>
<?php
if(is_array($infos)){
    foreach($infos as $info){
        ?>
    <tr>
        <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="message_group_id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
        <td><?php echo $info['subject']?></td>
        <td><?php echo $info['content'];?></td>
        <td align="center"><?php echo dr_date($info['inputtime'], null, 'red');?></td>
        <td align="center"><?php if($info['status']==1){echo L('show_m');}else {echo '<font color=red>'.L('close').'</font>';}?></td>
        <td align="center"> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['subject'])))?>',function(){redirect('?m=message&c=message&a=delete_group&message_group_id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a>
        </td>
    </tr>
    <?php
    }
}
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">

function see_all(id, name) {
    artdialog('edit','?m=message&c=message&a=see_all&messageid='+id,'<?php echo L('details');//echo L('edit')?> '+name+' ',700,450);
}
function checkuid() {
    var ids='';
    $("input[name='message_group_id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('before_select_operation')?>");
        return false;
    } else {
        myform.submit();
    }
}

</script>
</body>
</html>
