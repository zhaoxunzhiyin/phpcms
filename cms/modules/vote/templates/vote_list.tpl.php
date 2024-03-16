<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=vote&c=vote&a=delete" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('subjectid[]');" />
                        <span></span>
                    </label></th>
			<th><?php echo L('title')?></th>
			<th width="80"><?php echo L('vote_num')?></th>
			<th width="100"><?php echo L('startdate')?></th>
			<th width="100"><?php echo L('enddate')?></th>
			<th width='160'><?php echo L('inputtime')?></th>
			<th><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="subjectid[]" value="<?php echo $info['subjectid']?>" />
                        <span></span>
                    </label></td>
		<td><a href="?m=vote&c=index&a=show&show_type=1&subjectid=<?php echo $info['subjectid']?>&siteid=<?php echo $info['siteid'];?>" title="<?php echo L('check_vote')?>" target="_blank"><?php echo $info['subject'];?></a> <font color=red><?php if($info['enabled']==0)echo L('lock'); ?></font></td>
		<td><font color=blue><?php echo $info['votenumber']?></font> </td>
		<td><?php echo dr_date(strtotime($info['fromdate']), 'Y-m-d', 'red');?></td>
		<td><?php echo dr_date(strtotime($info['todate']), 'Y-m-d', 'red');?></td>
		<td><?php echo dr_date($info['addtime'], null, 'red');?></td>
		<td><a class="btn btn-xs blue" href='javascript:void(0);'
			onclick="statistics(<?php echo $info['subjectid']?>, '<?php echo new_addslashes($info['subject'])?>')"> <?php echo L('statistics')?></a>
		<a class="btn btn-xs green" href="javascript:void(0);"
			onclick="edit(<?php echo $info['subjectid']?>, '<?php echo new_addslashes($info['subject'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> <a class="btn btn-xs yellow" href="javascript:call(<?php echo new_addslashes($info['subjectid'])?>);void(0);"><?php echo L('call_js_code')?></a> <a class="btn btn-xs red"
			href='javascript:void(0);'
			onClick="Dialog.confirm('<?php echo L('vote_confirm_del')?>',function(){redirect('?m=vote&c=vote&a=delete&subjectid=<?php echo new_addslashes($info['subjectid'])?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes">
            <span></span>
        </label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('vote_confirm_del')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
 
function edit(id, name) {
	artdialog('edit','?m=vote&c=vote&a=edit&subjectid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function statistics(id, name) {
	var w = 700;
	var h = 350;
	if (is_mobile()) {
		w = h = '100%';
	}
	var diag = new Dialog({
		id:'statistics',
		title:'<?php echo L('statistics')?> '+name+' ',
		url:'<?php echo SELF;?>?m=vote&c=vote&a=statistics&subjectid='+id+'&pc_hash='+pc_hash,
		width:w,
		height:h,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function call(id) {
	var w = 600;
	var h = 470;
	if (is_mobile()) {
		w = h = '100%';
	}
	var diag = new Dialog({
		id:'call',
		title:'<?php echo L('vote')?><?php echo L('linkage_calling_code','','admin');?>',
		url:'<?php echo SELF;?>?m=vote&c=vote&a=public_call&subjectid='+id+'&pc_hash='+pc_hash,
		width:w,
		height:h,
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
