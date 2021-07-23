<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=link&c=link&a=check_register" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('linkid[]');" />
                        <span></span>
                    </label></th>
 			<th><?php echo L('link_name')?></th>
 			<th width="20%" align="center"><?php echo L('url')?></th> 
			<th width="12%" align="center"><?php echo L('logo')?></th> 
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
                        <input type="checkbox" class="checkboxes" name="linkid[]" value="<?php echo $info['linkid']?>" />
                        <span></span>
                    </label></td>
 		<td><a href="<?php echo $info['url'];?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo $info['name']?></a></td>
		<th width="20%" align="center"><a href="<?php echo $info['url']?>" target="_blank"><?php echo $info['url']?></a></th>
		<td align="center" width="12%"><?php if($info['linktype']==1){?><?php if($info['passed']=='1'){?><img src="<?php echo $info['logo'];?>" width=83 height=31><?php } else echo $info['logo'];}?></td>
		 <td align="center" width="20%"><a href="###"
			onclick="edit(<?php echo $info['linkid']?>, '<?php echo new_addslashes($info['name'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=link&c=link&a=delete&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a> 
		
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
<input name="dosubmit" type="button" class="button"
	value="<?php echo L('pass_check')?>"
	onClick="Dialog.confirm('<?php echo L('pass_or_not')?>',function(){$('#myform').submit();});">&nbsp;&nbsp;<input type="button" class="button" name="dosubmit" onclick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=link&c=link&a=delete';$('#myform').submit();});" value="<?php echo L('delete')?>"/> </div>
<div id="pages"><?php echo $this->pages?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	artdialog('edit','?m=link&c=link&a=edit&linkid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function checkuid() {
	var ids='';
	$("input[name='linkid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
</script>
