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
        <th width="80">Siteid</th>
        <th width="180"><?php echo L('dbsource_name')?></th>
        <th width="300"><?php echo L('server_address')?></th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center"><?php echo $v['id']?></td>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php echo $v['host']?></td>
<td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_html_special_chars(new_addslashes($v['name']))?>')"><?php echo L('edit')?></a><a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo new_html_special_chars(new_addslashes(L('confirm', array('message'=>$v['name']))))?>',function(){redirect('?m=dbsource&c=dbsource_admin&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
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
    artdialog('edit','?m=dbsource&c=dbsource_admin&a=edit&id='+id,'<?php echo L('edit_dbsource')?>《'+name+'》',700,500);
}
//-->
</script>
</body>
</html>