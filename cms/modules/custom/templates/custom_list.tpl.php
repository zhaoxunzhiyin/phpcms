<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=custom&c=custom&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
			<th>ID</th>
			<th align="LEFT"><?php echo L('custom_title')?></th>
			<th width="25%" align="center"><?php echo L('custom_content_view')?></th>
			<th width="25%" align="center"><?php echo L('custom_get')?></th>
			<th width="13%" align="center"><?php echo L('custom_inputtime')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
		<td align="center" width="35"><?php echo $info['id']?></td>
		<td><a href="###"
			onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>')"
			title="<?php echo L('edit')?>"><?php echo $info['title'];?></a></td>
		<td align="center" width="10%"><a href="###"
			onclick="view(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>','content')"
			><?php echo L('custom_click_view')?></a></td>
		<td align="center" width="10%"><a href="###"
			onclick="view(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>','lable')"
			><?php echo L('custom_click_view')?></a></td>
		<td  align="center"><?php echo date("Y-m-d H:m:s",$info['inputtime']);?></td>
		<td align="center" width="12%"><a href="###"
			onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['title'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['title'])))?>',function(){redirect('?m=custom&c=custom&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a> 
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input type="button" class="button" name="dosubmit" onClick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=custom&c=custom&a=delete';$('#myform').submit();});" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, name) {
	artdialog('edit','?m=custom&c=custom&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',720,380);
}
function view(id, name,flag) {
	if(flag=='content') {
		omnipotent('view_content','?m=custom&c=custom&a=view_content&id='+id,name+' 的内容',1,600,220);
	} else {
		omnipotent('view_lable','?m=custom&c=custom&a=view_lable&id='+id,name+' 对应的标签调用',1,600,280);
	}
}
function checkuid() {
	var ids='';
	$("input[name='id[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
window.top.$('#display_center_id').css('display','none');
</script>
</body>
</html>