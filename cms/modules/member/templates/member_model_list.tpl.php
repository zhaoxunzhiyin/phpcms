<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<div class="table-list">

<div class="explain-col">
<?php echo L('move_member_model_index_alert')?>
</div>

<div class="bk10"></div>
<form name="myform" id="myform" action="?m=member&c=member_model&a=delete" method="post" onsubmit="check();return false;">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" width="30px" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('modelid[]');" />
                        <span></span>
                    </label></th>
			<th align="left">ID</th>
			<th><?php echo L('sort')?></th>
			<th align="left"><?php echo L('model_name')?></th>
			<th align="left"><?php echo L('model_description')?></th>
			<th align="left"><?php echo L('table_name')?></th>
			<th align="center"><?php echo L('status')?></th>
			<th><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	foreach($member_model_list as $k=>$v) {
?>
    <tr>
		<td align="left" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['modelid']?>" name="modelid[]" <?php if($v['modelid']==10) echo "disabled";?> />
                        <span></span>
                    </label></td>
		<td align="left"><?php echo $v['modelid']?></td>
		<td align="center"><input type="text" name="sort[<?php echo $v['modelid']?>]" class="input-text-c input-text" size="3" value="<?php echo $v['sort']?>"></th>
		<td align="left"><?php echo $v['name']?></td>
		<td align="left"><?php echo $v['description']?></td>
		<td align="left"><?php echo $this->db->db_tablepre.$v['tablename']?></td>
		<td align="center"><?php echo $v['disabled'] ? L('icon_locked') : L('icon_unlock')?></td>
		<td align="center">
		<a onclick="_M(892);" href="?m=member&c=member_modelfield&a=manage&modelid=<?php echo $v['modelid']?>&menuid=892"><?php echo L('field').L('manage')?></a> | <a href="javascript:edit(<?php echo $v['modelid']?>, '<?php echo $v['name']?>')"><?php echo L('edit')?></a> | <a href="?m=member&c=member_model&a=export&modelid=<?php echo $v['modelid']?>"><?php echo L('export')?></a> | <a href="javascript:move(<?php echo $v['modelid']?>, '<?php echo $v['name']?>')"><?php echo L('move')?></a>
		</td>
    </tr>
<?php
	}
?>
</tbody>
</table>

<div class="btn"><label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="button" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=member&c=member_model&a=sort'" value="<?php echo L('sort')?>"/>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<div id="PC__contentHeight" style="display:none">160</div>

<script language="JavaScript">
<!--
function edit(id, name) {
	artdialog('edit','?m=member&c=member_model&a=edit&modelid='+id,'<?php echo L('edit').L('member_model')?>???'+name+'???',700,500);
}

function move(id, name) {
	artdialog('move','?m=member&c=member_model&a=move&modelid='+id,'<?php echo L('move')?>???'+name+'???',700,500);
}

function check() {
	if(myform.action == '?m=member&c=member_model&a=delete') {
		var ids='';
		$("input[name='modelid[]']:checked").each(function(i, n){
			ids += $(n).val() + ',';
		});
		if(ids=='') {
			Dialog.alert('<?php echo L('plsease_select').L('member_model')?>');
			return false;
		}
	}
	myform.submit();
}

//?????????????????????
function _M(menuid) {
	$.get("?m=admin&c=index&a=public_current_pos&menuid="+menuid, function(data){
		parent.$("#current_pos").html(data);
	});
}

//-->
</script>
</body>
</html>