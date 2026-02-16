<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="?m=message&c=message&a=search_message&menuid=<?php echo $this->input->get('menuid');?>" method="post" >
<input name="dosubmit" type="hidden" value="1">
<div class="col-md-12 col-sm-12">
<label><?php echo L('query_type')?></label>
                <label><i class="fa fa-caret-right"></i></label>
                <?php echo form::select($trade_status,$status,'name="search[status]"', L('all'))?>
</div>
<div class="col-md-12 col-sm-12">
                <label><?php echo L('username')?></label>
                <label><i class="fa fa-caret-right"></i></label>
                <label><input type="text" value="<?php echo $username;?>" class="input-text" name="search[username]"></label>
</div>
<div class="col-md-12 col-sm-12">
                <label><div class="formdate">
            <div class="input-group input-medium date-picker input-daterange">
                <input type="text" class="form-control" value="<?php echo $param['start_time'];?>" name="search[start_time]">
                <span class="input-group-addon"> <?php echo L('to')?> </span>
                <input type="text" class="form-control" value="<?php echo $param['end_time'];?>" name="search[end_time]">
            </div>
        </div></label>
</div>
<div class="col-md-12 col-sm-12">
        <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<form name="myform" id="myform" action="?m=message&c=message&a=delete" method="post" onsubmit="checkuid();return false;">
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
            <th width="120" align="center"><?php echo L('fromuserid')?></th>
            <th width='120' align="center"><?php echo L('touserid')?></th>
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
                        <input type="checkbox" class="checkboxes" name="messageid[]" value="<?php echo $info['messageid']?>" />
                        <span></span>
                    </label></td>
        <td><?php echo $info['subject']?></td>
        <td><?php echo $info['content'];?></td>
        <td align="center"><?php echo $info['send_from_id']?></td>
        <td align="center"><?php echo $info['send_to_id'];?></td>
        <td align="center"><a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => $info['subject']))?>',function(){redirect('?m=message&c=message&a=delete&messageid=<?php echo $info['messageid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a>
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
    artdialog('edit','?m=message&c=message&a=see_all&messageid='+id,'<?php echo L('details');//echo L('edit')?> '+name+',700,450);
}
function checkuid() {
    var ids='';
    $("input[name='messageid[]']:checked").each(function(i, n){
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
