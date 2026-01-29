<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=slider&c=slider&a=delete_type" method="post" onsubmit="checkuid();return false;">
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
            <th width="100"><?php echo L('slider_type_listorder')?></th> 
            <th><?php echo L('slider_name')?></th>
            <th width="100" align="center"><?php echo L('type_id')?></th> 
            <th width="100" align="center"><?php echo L('slider_lable')?></th> 
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
                        <input type="checkbox" class="checkboxes" name="typeid[]" value="<?php echo $info['typeid']?>" />
                        <span></span>
                    </label></td>
        <td align="center"><input name='listorders[<?php echo $info['typeid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input_center"></td> 
        <td><?php echo $info['name']?></td>
        <td align="center"> <?php echo $info['typeid'];?></td>
        <td align="center"><a class="btn btn-xs yellow" href="javascript:void(0);"
            onclick="view(<?php echo $info['typeid']?>, '<?php echo new_addslashes($info['name'])?>','content')"
            ><?php echo L('slider_click_view')?></a></td>
         <td align="center"><a class="btn btn-xs green" href="javascript:void(0);"
            onclick="edit(<?php echo $info['typeid']?>, '<?php echo new_addslashes($info['name'])?>')"
            title="<?php echo L('edit')?>"><?php echo L('edit')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=slider&c=slider&a=delete_type&typeid=<?php echo $info['typeid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
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
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=slider&c=slider&a=edit_type&typeid='+id,'<?php echo L('edit')?> '+name+' ',450,100);
}
function checkuid() {
    var ids='';
    $("input[name='typeid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('before_select_operations')?>");
        return false;
    } else {
        myform.submit();
    }
}
function view(id, name) {
    omnipotent('tag','?m=slider&c=slider&a=view_lable&typeid='+id,name+' 对应的标签调用',1,650,450)
}
</script>
