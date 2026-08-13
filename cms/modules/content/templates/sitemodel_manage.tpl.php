<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<div class="table-list">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
    <table width="100%" cellspacing="0">
        <thead>
            <tr class="heading">
            <th width="50"><?php echo L('可用');?></th>
            <th width="70" style="text-align:center"><?php echo L('sort');?></th>
            <th width="200"><?php echo L('model_name');?> / <?php echo L('tablename');?></th>
            <th width="150"><?php echo L('items');?></th>
            <th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php foreach($datas as $r) {?>
    <tr>
        <td><a href="javascript:;" onclick="dr_ajax_open_close(this, '<?php echo '?m=content&c=sitemodel&a=disabled&modelid='.$r['modelid'].'&menuid='.$this->input->get('menuid');?>', 1);" class="badge badge-<?php echo $r['disabled'] ? 'no' : 'yes';?>"><i class="fa fa-<?php echo $r['disabled'] ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align:center"><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=content&c=sitemodel&a=public_order_edit&modelid='.$r['modelid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $r['sort'];?>" class="displayorder form-control input-sm input-inline input-mini"></td>
        <td><?php echo $r['name'];?> / <?php echo $r['tablename']?></td>
        <td><?php echo $r['items']?></td>
        <td>
            <a class="btn btn-xs blue" href="javascript:dr_iframe_show('<?php echo L('模型内容字段');?>','?m=content&c=sitemodel_field&a=init&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $this->input->get('menuid');?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('模型内容字段');?></a>
            <a class="btn btn-xs green" href="?m=content&c=sitemodel&a=edit&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $this->input->get('menuid');?>"> <i class="fa fa-edit"></i> <?php echo L('edit');?></a>
            <a class="btn btn-xs red" href="javascript:;" onclick="model_delete(this,'<?php echo $r['modelid']?>','<?php echo L('confirm_delete_model',array('message'=>new_addslashes($r['name'])));?>','<?php echo $r['items']?>')"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a>
            <a class="btn btn-xs yellow" href="?m=content&c=sitemodel&a=export&modelid=<?php echo $r['modelid']?>&menuid=<?php echo $this->input->get('menuid');?>"> <i class="fa fa-sign-out"></i> <?php echo L('export');?></a>
        </td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</form>
  </div>
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript"> 
<!--
function model_delete(obj,id,name,items){
    if(items!=0) {
        Dialog.alert('<?php echo L('model_does_not_allow_delete');?>');
        return false;
    }
    Dialog.confirm(name, function(){
        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 100000000
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '?m=content&c=sitemodel&a=delete&pc_hash='+pc_hash,
            data: $("#myform").serialize()+"&modelid="+id,
            success: function(json) {
                layer.close(loading);
                // token 更新
                if (json.token) {
                    var token = json.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                if (json.code) {
                    $(obj).parent().parent().fadeOut("slow");
                }
                dr_tips(json.code, json.msg);
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
            }
        });
    });
};
//-->
</script>
</body>
</html>
