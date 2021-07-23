<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" action="?m=taglist&c=taglist&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
			<th width="20">ID</th> 
			<th width="150">关键字</th> 
			<th width="300">拼音</th>
			<th width="200" align="center">关联数</th>
			<th width="100" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($datas)){
	foreach($datas as $v){
?>
	<tr>
		<td align="center" width="35" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $v['id']?>" />
                        <span></span>
                    </label></td>
		<td align="center"><?php echo $v['id']?></td> 
		<td align="center" width="150"><?php echo $v['keyword'];;?></td>
		<td align="center" width="180"><?php echo $v['pinyin']; ?></td>
		<td align="center" width="90"><?php echo $v['videonum']; ?></td>
		 <td align="center" width="190"><a href="###"
			onclick="edit(<?php echo $v['id']?>, '<?php echo new_addslashes($v['keyword'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($v['keyword'])))?>',function(){redirect('?m=taglist&c=taglist&a=delete&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
<div class="btn"> 
<input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>"/></div>
</form>
<div id="pages" class="text-c"><?php echo $pages;?></div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	artdialog('edit','?m=taglist&c=taglist&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',450,280);
}
function checkuid() {
	var ids='';
	$("input[name='id[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('至少选择一条信息')?>");
		return false;
	} else {
		myform.submit();
	}
}
</script>
