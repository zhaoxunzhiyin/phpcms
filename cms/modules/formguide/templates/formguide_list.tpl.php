<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_header = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <?php if(isset($big_menu)) echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';?>
    <?php echo admin::submenu($_GET['menuid'],$big_menu); ?><span>|</span><a href="javascript:artdialog('setting','?m=formguide&c=formguide&a=setting','<?php echo L('module_setting')?>',540,350);void(0);"><em><?php echo L('module_setting')?></em></a>
    </div>
</div>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=formguide&c=formguide&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('formid[]');" />
                        <span></span>
                    </label></th>
			<th align="center"><?php echo L('name_items')?></th>
			<th width='100' align="center"><?php echo L('tablename')?></th>
			<th width='150' align="center"><?php echo L('introduction')?></th>
			<th width="140" align="center"><?php echo L('create_time')?></th>
			<th width="160" align="center"><?php echo L('call')?></th>
			<th width="220" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
	foreach($data as $form){
?>   
	<tr>
	<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="formid[]" value="<?php echo $form['modelid']?>" />
                        <span></span>
                    </label></td>
	<td><?php echo $form['name']?> [<a href="<?php echo APP_PATH?>index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&siteid=<?php echo $form['siteid']?>" target="_blank"><?php echo L('visit_front')?></a>] <?php if ($form['items']) {?>(<?php echo $form['items']?>)<?php }?></td>
	<td align="center"><?php echo $form['tablename']?></td>
	<td align="center"><?php echo $form['introduce']?></td>
	<td align="center"><?php echo date('Y-m-d H:i:s', $form['addtime'])?></td>
	<td align="center"><input type="text" value="<script language='javascript' src='{APP_PATH}index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&action=js&siteid=<?php echo $form['siteid']?>'></script>"></td>
	<td align="center"><a href="?m=formguide&c=formguide_info&a=init&formid=<?php echo $form['modelid']?>&menuid=<?php echo $_GET['menuid']?>"><?php echo L('info_list')?></a> | <a href="?m=formguide&c=formguide_field&a=add&formid=<?php echo $form['modelid']?>"><?php echo L('field_add')?></a> | <a href="?m=formguide&c=formguide_field&a=init&formid=<?php echo $form['modelid']?>"><?php echo L('field_manage')?></a> <br /><a href="?m=formguide&c=formguide&a=public_preview&formid=<?php echo $form['modelid']?>"><?php echo L('preview')?></a> | <a href="javascript:edit('<?php echo $form['modelid']?>', '<?php echo safe_replace($form['name'])?>');void(0);"><?php echo L('modify')?></a> | <a href="?m=formguide&c=formguide&a=disabled&formid=<?php echo $form['modelid']?>&val=<?php echo $form['disabled'] ? 0 : 1;?>"><?php if ($form['disabled']==0) { echo L('field_disabled'); } else { echo L('field_enabled'); }?></a> | <a href="###" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => addslashes(new_html_special_chars($form['name']))))?>',function(){redirect('?m=formguide&c=formguide&a=delete&formid=<?php echo $form['modelid']?>&pc_hash='+pc_hash);});"><?php echo L('del')?></a> | <a href="javascript:stat('<?php echo $form['modelid']?>', '<?php echo safe_replace($form['name'])?>');void(0);"><?php echo L('stat')?></a></td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
		<input name="button" type="button" class="button" value="<?php echo L('remove_all_selected')?>" onClick="Dialog.confirm('<?php echo L('affirm_delete')?>',function(){document.myform.action='?m=formguide&c=formguide&a=delete';$('#myform').submit();});">&nbsp;&nbsp;</div>  </div>
 <div id="pages"><?php echo $this->db->pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
	artdialog('edit','?m=formguide&c=formguide&a=edit&formid='+id,'<?php echo L('edit_formguide')?>--'+title,700,500);
}

function stat(id, title) {
	var diag = new Dialog({
		id:'stat',
		title:'<?php echo L('stat_formguide')?>--'+title,
		url:'<?php echo SELF;?>?m=formguide&c=formguide&a=stat&formid='+id+'&pc_hash='+pc_hash,
		width:700,
		height:500,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
</script>