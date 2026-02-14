<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th><?php echo L('name')?></th>
        <th width="80"><?php echo L('type')?></th>
        <th><?php echo L('display_position')?></th>
        <th width="150"><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php if($v['type']==1) {echo L('code');} else {echo L('table_style');}?></td>
<td align="center"><?php echo $v['pos']?></td>
<td align="center"><a href="javascript:block_update(<?php echo $v['id']?>, '<?php echo $v['name']?>')"><?php echo L('updates')?></a> | <a href="javascript:edit(<?php echo $v['id']?>, '<?php echo $v['name']?>')"><?php echo L('edit')?></a> | <a href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('confirm', array('message'=>$v['name']))?>',function(){redirect('?m=block&c=block_admin&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
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
<div id="closeParentTime" style="display:none"></div>
<script type="text/javascript">
<!--
function block_update(id, name) {
    artdialog('edit','?m=block&c=block_admin&a=block_update&id='+id,'<?php echo L('edit')?>《'+name+'》',700,500);
}

function edit(id, name) {
    artdialog('edit','?m=block&c=block_admin&a=edit&id='+id,'<?php echo L('edit')?>《'+name+'》',700,500);
}
//-->
</script>
</body>
</html>