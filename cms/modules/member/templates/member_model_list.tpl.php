<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('move_member_model_index_alert')?></p>
</div>
<div class="right-card-box">
<form name="myform" id="myform" action="?m=member&c=member_model&a=delete" method="post" onsubmit="check();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="70"><?php echo L('sort')?></th>
            <th width="100">ID</th>
            <th width="280"><?php echo L('model_name')?> / <?php echo L('tablename');?></th>
            <th width="50" style="text-align:center"><?php echo L('可用');?></th>
            <th><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    foreach($member_model_list as $k=>$v) {
?>
    <tr>
        <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['modelid']?>" name="modelid[]" <?php if($v['modelid']==10) echo "disabled";?> />
                        <span></span>
                    </label></td>
        <td align="center"><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=member&c=member_model&a=sort&modelid='.$v['modelid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $v['sort'];?>" class="displayorder form-control input-sm input-inline input-mini"></td>
        <td align="center"><?php echo $v['modelid']?></td>
        <td><?php echo $v['name']?> / <?php echo $v['tablename']?></td>
        <td class="table-center" style="text-align:center"><a href="javascript:;" onclick="dr_ajax_open_close(this, '<?php echo '?m=member&c=member_model&a=public_disabled&modelid='.$v['modelid'].'&menuid='.$this->input->get('menuid');?>', 1);" class="badge badge-<?php echo $v['disabled'] ? 'no' : 'yes';?>"><i class="fa fa-<?php echo $v['disabled'] ? 'times' : 'check';?>"></i></a></td>
        <td align="center"><a class="btn btn-xs blue" href="javascript:dr_iframe_show('<?php echo L('field').L('manage');?>','?m=member&c=member_modelfield&a=manage&modelid=<?php echo $v['modelid']?>&menuid=<?php echo $this->input->get('menuid');?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('field').L('manage')?></a> <a class="btn btn-xs green" href="javascript:edit(<?php echo $v['modelid']?>, '<?php echo $v['name']?>')"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs yellow" href="?m=member&c=member_model&a=export&modelid=<?php echo $v['modelid']?>"> <i class="fa fa-sign-out"></i> <?php echo L('export')?></a> <a class="btn btn-xs dark" href="javascript:move(<?php echo $v['modelid']?>, '<?php echo $v['name']?>')"> <i class="fa fa-arrows"></i> <?php echo L('move')?></a></td>
    </tr>
<?php
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
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
<script language="JavaScript">
function edit(id, name) {
    artdialog('edit','?m=member&c=member_model&a=edit&modelid='+id,'<?php echo L('edit').L('member_model')?>《'+name+'》',700,500);
}
function move(id, name) {
    artdialog('move','?m=member&c=member_model&a=move&modelid='+id,'<?php echo L('move')?>《'+name+'》',700,500);
}
function check() {
    if(myform.action == '?m=member&c=member_model&a=delete') {
        var ids='';
        $("input[name='modelid[]']:checked").each(function(i, n){
            ids += $(n).val() + ',';
        });
        if(ids=='') {
            Dialog.alert('<?php echo L('plsease_select').L('member_model')?>');
            return false;
        }
    }
    myform.submit();
}
</script>
</div>
</div>
</div>
</div>
</body>
</html>