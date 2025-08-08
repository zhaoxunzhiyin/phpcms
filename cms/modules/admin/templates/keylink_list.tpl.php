<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=admin&c=keylink&a=delete" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35" align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="200"><?php echo L('keyword_name')?></th>
            <th><?php echo L('link_url')?></th> 
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
                        <input type="checkbox" class="checkboxes" name="keylinkid[]" value="<?php echo $info['keylinkid']?>" />
                        <span></span>
                    </label></td>
        <td align="left"><span  class="<?php echo $info['style']?>"><?php echo $info['word']?></span> </td>
        <td align="center"><?php echo $info['url']?></td>
         <td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $info['keylinkid']?>, '<?php echo new_addslashes($info['word'])?>')"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=keylink&a=delete&keylinkid=<?php echo $info['keylinkid']?>', '<?php echo L('keylink_confirm_del')?>')"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> </td>
    </tr>
<?php
    }
}
?></tbody>
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
    artdialog('edit','?m=admin&c=keylink&a=edit&keylinkid='+id,'<?php echo L('keylink_edit')?> '+name+' ',450,200);
}

function checkuid() {
    var ids='';
    $("input[name='keylinkid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('badword_pleasechose')?>');
        return false;
    } else {
        myform.submit();
    }
}
</script>
 