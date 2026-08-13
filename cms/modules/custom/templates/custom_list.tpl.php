<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=custom&c=custom&a=delete" method="post" onsubmit="checkuid();return false;">
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
            <th width="80">ID</th>
            <th><?php echo L('custom_title')?></th>
            <th width="100" align="center"><?php echo L('custom_content_view')?></th>
            <th width="100" align="center"><?php echo L('custom_get')?></th>
            <th width="160" align="center"><?php echo L('custom_inputtime')?></th>
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
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
        <td align="center"><?php echo $info['id']?></td>
        <td><a href="javascript:void(0);"
            onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>')"
            title="<?php echo L('edit')?>"><?php echo $info['title'];?></a></td>
        <td align="center"><a class="btn btn-xs blue" href="javascript:void(0);"
            onclick="view(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>','content')"
            ><?php echo L('custom_click_view')?></a></td>
        <td align="center"><a class="btn btn-xs yellow" href="javascript:void(0);"
            onclick="view(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>','lable')"
            ><?php echo L('custom_click_view')?></a></td>
        <td  align="center"><?php echo dr_date($info['inputtime'], null, 'red');?></td>
        <td align="center"><a class="btn btn-xs green" href="javascript:void(0);"
            onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>')"
            title="<?php echo L('edit')?>"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['title'])))?>',function(){redirect('?m=custom&c=custom&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> 
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=custom&c=custom&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=custom&c=custom&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',720,500);
}
function view(id, name,flag) {
    if(flag=='content') {
        omnipotent('view_content','?m=custom&c=custom&a=public_view_content&id='+id,name+' 的内容',1,600,220);
    } else {
        omnipotent('view_lable','?m=custom&c=custom&a=public_view_lable&id='+id,name+' 对应的标签调用',1,600,280);
    }
}
function checkuid() {
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('select_operations')?>");
        return false;
    } else {
        myform.submit();
    }
}
</script>
</body>
</html>