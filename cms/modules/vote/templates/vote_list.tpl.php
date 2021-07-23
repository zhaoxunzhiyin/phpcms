<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=vote&c=vote&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('subjectid[]');" />
                        <span></span>
                    </label></th>
			<th><?php echo L('title')?></th>
			<th width="40" align="center"><?php echo L('vote_num')?></th>
			<th width="68" align="center"><?php echo L('startdate')?></th>
			<th width="68" align="center"><?php echo L('enddate')?></th>
			<th width='68' align="center"><?php echo L('inputtime')?></th>
			<th width="180" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="subjectid[]" value="<?php echo $info['subjectid']?>" />
                        <span></span>
                    </label></td>
		<td><a href="?m=vote&c=index&a=show&show_type=1&subjectid=<?php echo $info['subjectid']?>&siteid=<?php echo $info['siteid'];?>" title="<?php echo L('check_vote')?>" target="_blank"><?php echo $info['subject'];?></a> <font color=red><?php if($info['enabled']==0)echo L('lock'); ?></font></td>
		<td align="center"><font color=blue><?php echo $info['votenumber']?></font> </td>
		<td align="center"><?php echo $info['fromdate'];?></td>
		<td align="center"><?php echo $info['todate'];?></td>
		<td align="center"><?php echo date("Y-m-d",$info['addtime']);?></td>
		<td align="center"><a href='###'
			onclick="statistics(<?php echo $info['subjectid']?>, '<?php echo new_addslashes($info['subject'])?>')"> <?php echo L('statistics')?></a>
		| <a href="###"
			onclick="edit(<?php echo $info['subjectid']?>, '<?php echo new_addslashes($info['subject'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> | <a href="javascript:call(<?php echo new_addslashes($info['subjectid'])?>);void(0);"><?php echo L('call_js_code')?></a> | <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('vote_confirm_del')?>',function(){redirect('?m=vote&c=vote&a=delete&subjectid=<?php echo new_addslashes($info['subjectid'])?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
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
	onClick="Dialog.confirm('<?php echo L('vote_confirm_del')?>',function(){$('#myform').submit();});">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">
 
function edit(id, name) {
	artdialog('edit','?m=vote&c=vote&a=edit&subjectid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function statistics(id, name) {
	var diag = new Dialog({
		id:'statistics',
		title:'<?php echo L('statistics')?> '+name+' ',
		url:'<?php echo SELF;?>?m=vote&c=vote&a=statistics&subjectid='+id+'&pc_hash='+pc_hash,
		width:700,
		height:350,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function call(id) {
	var diag = new Dialog({
		id:'call',
		title:'<?php echo L('vote')?><?php echo L('linkage_calling_code','','admin');?>',
		url:'<?php echo SELF;?>?m=vote&c=vote&a=public_call&subjectid='+id+'&pc_hash='+pc_hash,
		width:600,
		height:470,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function checkuid() {
	var ids='';
	$("input[name='subjectid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert('<?php echo L('before_select_operation')?>');
		return false;
	} else {
		myform.submit();
	}
}

</script>
</body>
</html>
