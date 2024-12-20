<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=admin&c=position&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="80"><?php echo L('listorder');?></th>
            <th width="80">ID</th>
            <th><?php echo L('posid_name');?></th>
            <th width="120"><?php echo L('posid_catid');?></th>
            <th width="120"><?php echo L('posid_modelid');?></th>
            <th><?php echo L('posid_operation');?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos)){
    foreach($infos as $info){
?>   
    <tr>
    <td>
    <input name='listorders[<?php echo $info['posid']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini">
    </td>
    <td><?php echo $info['posid']?></td>
    <td align="center"><?php echo $info['name']?></td>
    <td align="center"><?php echo $info['catid'] ? dr_cat_value($info['catid'], 'catname') : L('posid_all')?></td>
    <td align="center"><?php echo $info['modelid'] ? $model[$info['modelid']]['name'] : L('posid_all')?></td>
    <td align="center">
    <a class="btn btn-xs blue" href="?m=admin&c=position&a=public_item&posid=<?php echo $info['posid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('posid_item_manage')?></a>
    <a class="btn btn-xs green" href="javascript:edit(<?php echo $info['posid']?>, '<?php echo new_addslashes($info['name'])?>')"><?php echo L('edit')?></a>
    <?php if($info['siteid']=='0' && !cleck_admin(param::get_session('roleid'))) {?>
    <?php } else {?>
    <a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=position&a=delete&posid=<?php echo $info['posid']?>', '<?php echo L('posid_del_cofirm')?>')"><?php echo L('delete')?></a>    
    <?php } ?>
    <?php if($info['thumb']){?>
    <a class="btn btn-xs yellow" href="javascript:preview('<?php echo dr_get_file($info['thumb']);?>')"><?php echo L('priview')?></a>
    <?php } ?>
    
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
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
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
<!--
function edit(id, name) {
    artdialog('edit','?m=admin&c=position&a=edit&posid='+id,'<?php echo L('edit')?>--'+name,800,450);
}
//预览视频
function preview(thumb) {
    var width = 400;
    var height = 300;
    var att = 'width: 350px;height: 260px;';
    if (is_mobile()) {
        width = height = '90%';
        var att = 'height: 90%;';
    }
    var diag = new Dialog({
        title:'<?php echo L('预览')?>',
        html:'<style type="text/css">a{text-shadow: none; color: #337ab7; text-decoration:none;}a:hover{cursor: pointer; color: #23527c; text-decoration: underline;}</style><div style="'+att+'line-height: 24px;word-break: break-all;overflow: hidden auto;"><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+thumb+'" target="_blank">'+thumb+'</a></p><p style="text-align: center;"><a href="'+thumb+'" target="_blank"><img style="max-width:100%" src="'+thumb+'"></a></p></div>',
        width:width,
        height:height,
        modal:true
    });
    diag.show();
}
//-->
</script>