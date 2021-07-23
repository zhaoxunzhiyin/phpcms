<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=slider&c=slider&a=delete_type" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('typeid[]');" />
                        <span></span>
                    </label></th>
			<th width="80"><?php echo L('slider_type_listorder')?></th> 
			<th><?php echo L('slider_name')?></th>
			<th width="12%" align="center"><?php echo L('type_id')?></th> 
			<th width="30%" align="center"><?php echo L('slider_lable')?></th> 
			<th width="20%" align="center"><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="typeid[]" value="<?php echo $info['typeid']?>" />
                        <span></span>
                    </label></td>
		<td align="center"><input name='listorders[<?php echo $info['typeid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input_center"></td> 
		<td><?php echo $info['name']?></td>
		<td align="center" width="12%"> <?php echo $info['typeid'];?></td>
		<td align="center" width="30%"><a href="###"
			onclick="view(<?php echo $info['typeid']?>, '<?php echo new_addslashes($info['name'])?>','content')"
			><?php echo L('slider_click_view')?></a></td>
		 <td align="center" width="20%"><a href="###"
			onclick="edit(<?php echo $info['typeid']?>, '<?php echo new_addslashes($info['name'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=slider&c=slider&a=delete_type&typeid=<?php echo $info['typeid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="button" type="button" class="button" value="<?php echo L('remove_all_selected')?>" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});">
</div>
</form>
<div id="pages" class="text-c"><?php echo $pages;?></div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	artdialog('edit','?m=slider&c=slider&a=edit_type&typeid='+id,'<?php echo L('edit')?> '+name+' ',450,70);
}
function checkuid() {
	var ids='';
	$("input[name='typeid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('before_select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
function view(id, name) {
	omnipotent('tag','?m=slider&c=slider&a=view_lable&typeid='+id,name+' 对应的标签调用',1,600,320)
}
</script>
