<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<form name="myform" id="myform" action="?m=admin&c=copyfrom&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="80" align="center"><?php echo L('listorder');?></th>
            <th width="200"><?php echo L('copyfrom_name');?></th>
            <th><?php echo L('copyfrom_url')?></th> 
            <th><?php echo L('copyfrom_logo')?></th> 
             <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
<?php
foreach($datas as $r) {
?>
<tr>
<td align="center"><input type="text" name="listorders[<?php echo $r['id']?>]" value="<?php echo $r['listorder']?>" class='displayorder form-control input-sm input-inline input-mini'></td>
<td align="center"><?php echo $r['sitename']?></td>
<td align="center"><?php echo $r['siteurl']?></td>
<td align="center"><?php if($r['thumb']) {?><img src="<?php echo dr_get_file($r['thumb'])?>"><?php }?></td>
<td align="center"><a class="btn btn-xs green" href="javascript:edit('<?php echo $r['id']?>','<?php echo new_addslashes($r['sitename'])?>')"><?php echo L('edit');?></a> <a class="btn btn-xs red" href="javascript:;" onclick="data_delete(this,'<?php echo $r['id']?>','<?php echo L('confirm',array('message'=>new_addslashes($r['sitename'])));?>')"><?php echo L('delete')?></a> </td>
</tr>
<?php } ?>
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
    artdialog('edit','?m=admin&c=copyfrom&a=edit&id='+id,'<?php echo L('edit');?>《'+name+'》',580,280);
}
function data_delete(obj,id,name){
    Dialog.confirm(name,function(){
        $.get('?m=admin&c=copyfrom&a=delete&id='+id+'&pc_hash='+pc_hash,function(data){
            if(data) {
                $(obj).parent().parent().fadeOut("slow");
            }
        })
    });
};
//-->
</script>