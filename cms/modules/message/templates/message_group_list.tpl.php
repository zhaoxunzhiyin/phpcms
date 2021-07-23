<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=message&c=message&a=delete_group" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('message_group_id[]');" />
                        <span></span>
                    </label></th>
			<th><?php echo L('subject')?></th>
			<th width="35%" align="center"><?php echo L('content')?></th>
			<th width="15%" align="center"><?php echo L('sendtime')?></th>
			<th width='10%' align="center"><?php echo L('status')?></th>
			<th width="10%" align="center"><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="message_group_id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
		<td><?php echo $info['subject']?></td>
		<td align="" widht="35%"><?php echo $info['content'];?></td>
		<td align="center" width="15%"><?php echo date('Y-m-d H:i:s',$info['inputtime']);?></td>
		<td align="center" width="10%"><?php if($info['status']==1){echo L('show_m');}else {echo '<font color=red>'.L('close').'</font>';}?></td>
		<td align="center" width="10%"> <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['subject'])))?>',function(){redirect('?m=message&c=message&a=delete_group&message_group_id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
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
<input name="button" type="button" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">

function see_all(id, name) {
	artdialog('edit','?m=message&c=message&a=see_all&messageid='+id,'<?php echo L('details');//echo L('edit')?> '+name+' ',700,450);
}
function checkuid() {
	var ids='';
	$("input[name='message_group_id[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('before_select_operation')?>");
		return false;
	} else {
		myform.submit();
	}
}

</script>
</body>
</html>
