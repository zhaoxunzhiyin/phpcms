<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		<?php echo L('all_linktype')?>:&nbsp;&nbsp;<a href="?m=link&c=link"><?php echo L('all')?></a>&nbsp;
		<?php
	if(is_array($type_arr)){
	foreach($type_arr as $typeid => $type){
		?><a href="?m=link&c=link&typeid=<?php echo $typeid;?>"><?php echo $type;?></a>&nbsp;
		<?php }}?>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" id="myform" action="?m=link&c=link&a=listorder" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('linkid[]');" />
                        <span></span>
                    </label></th>
			<th width="35" align="center"><?php echo L('listorder')?></th>
			<th><?php echo L('link_name')?></th>
			<th width="12%" align="center"><?php echo L('logo')?></th>
			<th width="10%" align="center"><?php echo L('typeid')?></th>
			<th width='10%' align="center"><?php echo L('link_type')?></th>
			<th width="8%" align="center"><?php echo L('status')?></th>
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
                        <input type="checkbox" class="checkboxes" name="linkid[]" value="<?php echo $info['linkid']?>" />
                        <span></span>
                    </label></td>
		<td align="center" width="35"><input name='listorders[<?php echo $info['linkid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
		<td><a href="<?php echo $info['url'];?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo new_html_special_chars($info['name'])?></a> </td>
		<td align="center" width="12%"><?php if($info['linktype']==1){?><?php if($info['passed']=='1'){?><?php if($info['logo']){?><img src="<?php echo $info['logo'];?>" width="83" height="31"><?php }}}?></td>
		<td align="center" width="10%"><?php echo $type_arr[$info['typeid']];?></td>
		<td align="center" width="10%"><?php if($info['linktype']==0){echo L('word_link');}else{echo L('logo_link');}?></td>
		<td width="8%" align="center"><?php if($info['passed']=='0'){?><a
			href='###'
			onClick="Dialog.confirm('<?php echo L('pass_or_not')?>',function(){redirect('?m=link&c=link&a=check&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"><font color=red><?php echo L('audit')?></font></a><?php }else{echo L('passed');}?></td>
		<td align="center" width="12%"><a href="###"
			onclick="edit(<?php echo $info['linkid']?>, '<?php echo new_addslashes(new_html_special_chars($info['name']))?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes(new_html_special_chars($info['name']))))?>',function(){redirect('?m=link&c=link&a=delete&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a> 
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
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('listorder')?>">&nbsp;&nbsp;<input type="button" class="button" name="dosubmit" onClick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=link&c=link&a=delete';$('#myform').submit();});" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
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
		Dialog.alert("<?php echo L('before_select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=link&c=link&a=listorder_up&linkid='+id,null,function (msg) { 
	if (msg==1) { 
	//$("div [id=\'option"+id+"\']").remove(); 
		Dialog.alert('<?php echo L('move_success')?>');
	} else {
	Dialog.alert(msg); 
	} 
	}); 
} 
</script>
</body>
</html>
