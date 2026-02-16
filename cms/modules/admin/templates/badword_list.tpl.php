<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=admin&c=badword&a=delete" method="post" onsubmit="checkuid();return false;">
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
            <th><?php echo L('badword_name')?></th>
            <th><?php echo L('badword_replacename')?></th>
            <th width="80"><?php echo L('badword_level')?></th>
            <th width="160"><?php echo L('inputtime')?></th>
            <th><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="badid[]" value="<?php echo $info['badid']?>" />
                        <span></span>
                    </label></td>
         <td align="center"><span  class="<?php echo $info['style']?>"><?php echo $info['badword']?></span> </td>
        <td align="center"><?php echo $info['replaceword']?></td>
        <td align="center"><?php echo $level[$info['level']]?></td>
        <td align="center"><?php echo $info['lastusetime'] ? dr_date($info['lastusetime'], null, 'red'):''?></td>
         <td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $info['badid']?>, '<?php echo new_addslashes($info['badword'])?>')"><?php echo L('edit')?></a> <a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=badword&a=delete&badid=<?php echo $info['badid']?>', '<?php echo L('badword_confirm_del')?>')"><?php echo L('delete')?></a> </td>
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('badword_confom_del')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=admin&c=badword&a=edit&badid='+id,'<?php echo L('badword_edit')?> '+name+' ',450,280);
}

function checkuid() {
    var ids='';
    $("input[name='badid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('badword_pleasechose');?>');
        return false;
    } else {
        myform.submit();
    }
}
</script>

 