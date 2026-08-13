<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('searh_notice')?></p>
</div>
<div class="right-card-box">
<form name="myform" action="?m=search&c=search_type&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="bk10"></div>
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th width="80"><?php echo L('sort')?></td>
            <th width="80">ID</th>
            <th width="180"><?php echo L('catname')?></th>
            <th width="180"><?php echo L('modulename')?></th>
            <th width="180"><?php echo L('modlename')?></th>
            <th><?php echo L('catdescription')?></th>
            <th><?php echo L('opreration')?></th>
            </tr>
        </thead>
    <tbody>
    

<?php
foreach($datas as $r) {
?>
<tr>
<td><input type="text" name="listorders[<?php echo $r['typeid']?>]" value="<?php echo $r['listorder']?>" class='displayorder form-control input-sm input-inline input-mini'></td>
<td><?php echo $r['typeid']?></td>
<td><?php echo $r['name']?></td>
<td><?php echo $r['modelid'] && $r['typedir'] !='yp' ? L('content_module') : $r['typedir'];?></td>
<td><?php echo $this->model[$r['modelid']]['name'] ? $this->model[$r['modelid']]['name'] : $this->yp_model[$r['modelid']]['name']?></td>
<td ><?php echo $r['description']?></td>
<td><a class="btn btn-xs green" href="javascript:edit('<?php echo $r['typeid']?>','<?php echo $r['name']?>')"> <i class="fa fa-edit"></i> <?php echo L('modify')?></a><a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('sure_delete', '', 'member')?>',function(){redirect('?m=search&c=search_type&a=delete&typeid=<?php echo $r['typeid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> </td>
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
<script type="text/javascript"> 
<!--
function edit(id, name) {
    artdialog('edit','?m=search&c=search_type&a=edit&typeid='+id,'<?php echo L('edit_cat')?>《'+name+'》',580,360);
}
function data_delete(obj,id,name){
    Dialog.confirm(name,function(){
        $.get('?m=search&c=search_type&a=delete&typeid='+id+'&pc_hash='+pc_hash,function(data){
            if(data) {
                $(obj).parent().parent().fadeOut("slow");
            }
        })     
    });
};
//-->
</script>
</body>
</html>