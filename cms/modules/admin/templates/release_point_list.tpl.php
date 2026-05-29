<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80">ID</th>
        <th align="left" ><?php echo L('release_point_name')?></th>
        <th align="left" ><?php echo L('server_address')?></th>
        <th align="left" ><?php echo L("username")?></th>
        <th width="150"><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
<tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td width="80" align="center"><?php echo $v['id']?></td>
<td><?php echo $v['name']?></td>
<td><?php echo $v['host']?></td>
<td><?php echo $v['username']?></td>
<td align="center" ><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_addslashes($v['name'])?>')"><?php echo L('edit')?></a> <a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo new_addslashes(L('confirm', array('message'=>$v['name'])))?>',function(){redirect('?m=admin&c=release_point&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
</tr>
<?php 
    endforeach;
endif;
?>
</tbody>
</table>
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
function edit(id, name) {
    artdialog('edit','?m=admin&c=release_point&a=edit&id='+id,'<?php echo L('release_point_edit')?>《'+name+'》',700,500);
}
//-->
</script>
</body>
</html>