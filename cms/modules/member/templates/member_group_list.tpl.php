<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<form name="myform" id="myform" action="?m=member&c=member_group&a=delete" method="post" onsubmit="check();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" width="30px" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('groupid[]');" />
                        <span></span>
                    </label></th>
			<th align="left">ID</th>
			<th><?php echo L('sort')?></th>
			<th><?php echo L('groupname')?></th>
			<th><?php echo L('issystem')?></th>
			<th><?php echo L('membernum')?></th>
			<th><?php echo L('starnum')?></th>
			<th><?php echo L('pointrange')?></th>
			<th><?php echo L('allowattachment')?></th>
			<th><?php echo L('allowpost')?></th>
			<th><?php echo L('member_group_publish_verify')?></th>
			<th><?php echo L('allowsearch')?></th>
			<th><?php echo L('allowupgrade')?></th>
			<th><?php echo L('allowsendmessage')?></th>
			<th><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	foreach($member_group_list as $k=>$v) {
?>
    <tr>
		<td align="left" class="myselect"><?php if(!$v['issystem']) {?><label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['groupid']?>" name="groupid[]" />
                        <span></span>
                    </label><?php }?></td>
		<td align="left"><?php echo $v['groupid']?></td>
		<td align="center"><input type="text" name="sort[<?php echo $v['groupid']?>]" class="input-text-c input-text" size="3" value="<?php echo $v['sort']?>"></th>
		<td align="center" title="<?php echo $v['description']?>"><?php echo $v['name']?></td>
		<td align="center"><?php echo $v['issystem'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['membernum']?></th>
		<td align="center"><?php echo $v['starnum']?></td>
		<td align="center"><?php echo $v['point']?></td>
		<td align="center"><?php echo $v['allowattachment'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['allowpost'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['allowpostverify'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['allowsearch'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['allowupgrade'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><?php echo $v['allowsendmessage'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['groupid']?>, '<?php echo $v['name']?>')"><?php echo L('edit')?></a></td>
    </tr>
<?php
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
        <label><button type="submit" onclick="document.myform.action='?m=member&c=member_group&a=sort'" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('sort')?></button></label>
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
<script language="JavaScript">
<!--
function edit(id, name) {
	artdialog('edit','?m=member&c=member_group&a=edit&groupid='+id,'<?php echo L('edit').L('member_group')?>《'+name+'》',700,500);
}

function check() {
	if(myform.action == '?m=member&c=member_group&a=delete') {
		var ids='';
		$("input[name='groupid[]']:checked").each(function(i, n){
			ids += $(n).val() + ',';
		});
		if(ids=='') {
			Dialog.alert('<?php echo L('plsease_select').L('member_group')?>');
			return false;
		}
	}
	myform.submit();
}
//-->
</script>
</div>
</div>
</div>
</div>
</body>
</html>