<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad_10">
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
<td align="center" ><a href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_addslashes($v['name'])?>')"><?php echo L('edit')?></a> | <a href="###" onclick="Dialog.confirm('<?php echo new_addslashes(L('confirm', array('message'=>$v['name'])))?>',function(){redirect('?m=admin&c=release_point&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
</tr>
<?php 
	endforeach;
endif;
?>
</tbody>
</table>
</div>
</div>
<div id="pages"><?php echo $pages?></div>
<script type="text/javascript">
<!--
function edit(id, name) {
	artdialog('edit','?m=admin&c=release_point&a=edit&id='+id,'<?php echo L('release_point_edit')?>《'+name+'》',700,500);
}
//-->
</script>
</body>
</html>