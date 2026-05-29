<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
var syncing = 0;
function sync_web(id) {
    if (syncing == 1) {
        dr_tips(0, '<?php echo L('sync_server')?>');
        return;
    }
    syncing = 1;
    $('#sync_html_'+id).html('<font color="blue"><?php echo L('sync_server_data')?></font>');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "<?php echo APP_PATH.SELF;?>?m=fclient&c=fclient&a=sync_web&id="+id+"&pc_hash="+pc_hash,
        success: function(json) {
            if (json.code) {
                $('#sync_html_'+id).html('<font color="green">'+json.msg+'</font>');
            } else {
                layer.open({
                    type: 1,
                    title: '<?php echo L('sync_fail')?>',
                    closeBtn: 0, //不显示关闭按钮
                    shadeClose : true,
                    scrollbar: false,
                    content: '<div style="padding: 30px;">'+json.msg+'</div>'
                });
                $('#sync_html_'+id).html('<font color="red"><?php echo L('sync_fail')?></font>');
            }
            syncing = 0;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            //Dialog.alert(HttpRequest.responseText);
            $('#sync_html_'+id).html('<font color="red"><?php echo L('sync_server_not')?></font>');
            syncing = 0;
        }
    });
}
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="" method="get" >
<input type="hidden" value="fclient" name="m">
<input type="hidden" value="fclient" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $this->input->get('menuid');?>" name="menuid">
<div class="col-md-12 col-sm-12">
<label><select name="field" class="form-control">
    <option value="uid"<?php if ($this->input->get('field')=='uid') echo ' selected'?>>UID</option>
    <option value="username"<?php if ($this->input->get('field')=='username') echo ' selected'?>><?php echo L('username')?></option>
    <option value="name"<?php if ($this->input->get('field')=='name') echo ' selected'?>><?php echo L('name')?></option>
    <option value="domain"<?php if ($this->input->get('field')=='domain') echo ' selected'?>><?php echo L('domain')?></option>
    <option value="sn"<?php if ($this->input->get('field')=='sn') echo ' selected'?>><?php echo L('sn')?></option>
    <option value="money"<?php if ($this->input->get('field')=='money') echo ' selected'?>><?php echo L('money')?></option>
    <option value="status"<?php if ($this->input->get('field')=='status') echo ' selected'?>><?php echo L('status')?></option>
    <option value="id"<?php if ($this->input->get('field')=='id') echo ' selected'?>> Id </option>
</select></label>
<label><i class="fa fa-caret-right"></i></label>
<label><input type="text" value="<?php echo $keyword?>" class="input-text" name="keyword"></label>
</div>
<div class="col-md-12 col-sm-12">
<label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<form name="myform" id="myform" action="?m=fclient&c=fclient" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th width="35" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="120"><?php echo L('uid')?></th>
            <th width="180"><?php echo L('name')?></th>
            <th width="120"><?php echo L('money')?></th>
            <th width="110"><?php echo L('model')?></th>
            <th width="90"><?php echo L('status')?></th>
            <th width='210'><?php echo L('notice_time')?></th>
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
        <td align="center"><?php echo $user_arr[$info['uid']];?></td>
        <td><?php echo new_html_special_chars($info['name'])?></td>
        <td align="center"><?php echo $info['money'];?></td>
        <td align="center"><?php if(dr_string2array($info['setting'])['mode']){echo L('local_site');}else{echo L('remote_web_site');}?></td>
        <td align="center"><?php if($info['status']==1){echo L('no_check');}elseif($info['status']==2){echo L('check_2');}elseif($info['status']==3){echo L('check_3');}elseif($info['status']==4){echo L('check_4');}?></td>
        <td align="center"><?php if ($info['inputtime']) echo date('Y-m-d',$info['inputtime']);?> ~ <?php if ($info['endtime']) echo date('Y-m-d',$info['endtime']);?></td>
        <td><a href="javascript:;"
            onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes(new_html_special_chars($info['name']))?>')"
            title="<?php echo L('edit')?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a>
            <a target="_blank" href="<?php echo $info['domain']?>" class="btn btn-xs red"> <i class="fa fa-search"></i> <?php echo L('web_site')?></a>
            <a href="?m=fclient&c=fclient&a=sync_admin&id=<?php echo $info['id']?>" target="_blank" class="btn btn-xs dark"> <i class="fa fa-user"></i> <?php echo L('web_site_admin')?></a>
            <?php if(dr_string2array($info['setting'])['mode']){?>
            <a href="?m=fclient&c=fclient&a=update&id=<?php echo $info['id']?>" class="btn btn-xs yellow"> <i class="fa fa-download"></i> <?php echo L('update')?></a>
            <a href="javascript:sync_web('<?php echo $info['id']?>');" class="btn btn-xs blue"> <i class="fa fa-send"></i> <?php echo L('data_detection')?></a>
            <?php }else{?>
            <a href="?m=fclient&c=fclient&a=down&id=<?php echo $info['id']?>" class="btn btn-xs yellow"> <i class="fa fa-download"></i> <?php echo L('download')?></a>
            <a href="javascript:sync_web('<?php echo $info['id']?>');" class="btn btn-xs blue"> <i class="fa fa-send"></i> <?php echo L('send_data')?></a>
            <?php }?>
            <label id="sync_html_<?php echo $info['id']?>"></label>
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
        <label><button type="button" onClick="document.myform.action='?m=fclient&c=fclient&a=delete';return confirm_delete()" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
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
    artdialog('edit','?m=fclient&c=fclient&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function confirm_delete(){
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('checked_the_info')?>');
        return false;
    }
    Dialog.confirm('<?php echo L('confirm')?>',function() {
        $('#myform').submit();
    });
}
</script>
</body>
</html>
