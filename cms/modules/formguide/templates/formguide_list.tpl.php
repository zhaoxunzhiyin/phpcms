<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = true; 
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=formguide&c=formguide&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid')?>">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('formid[]');" />
                        <span></span>
                    </label></th>
			<th><?php echo L('name_items')?></th>
			<th width='180'><?php echo L('tablename')?></th>
			<th width="180"><?php echo L('create_time')?></th>
			<th width="220"><?php echo L('call')?></th>
			<th width="80"><?php echo L('状态')?></th>
			<th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
	foreach($data as $form){
?>
	<tr>
	<td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="formid[]" value="<?php echo $form['modelid']?>" />
                        <span></span>
                    </label></td>
	<td><?php echo $form['name']?> <?php if ($form['items']) {?>(<?php echo $form['items']?>)<?php }?></td>
	<td align="center"><?php echo $form['tablename']?></td>
	<td align="center"><?php echo date('Y-m-d H:i:s', $form['addtime'])?></td>
	<td align="center"><input type="text" value="<script language='javascript' src='{APP_PATH}index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&action=js&siteid=<?php echo $form['siteid']?>'></script>"></td>
	<td align="center"><a class="btn btn-xs dark" href="?m=formguide&c=formguide&a=disabled&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&val=<?php echo $form['disabled'] ? 0 : 1;?>"><?php if ($form['disabled']==0) {echo L('field_disabled');} else {echo L('enable');}?></a></td>
	<td align="center">
	<a class="btn btn-xs yellow" href="<?php echo APP_PATH;?>index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&siteid=<?php echo $form['siteid']?>" target="_blank"><?php echo L('preview')?></a>
	<a class="btn btn-xs blue" href="?m=formguide&c=formguide_info&a=init&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('info_list')?></a>
	<a class="btn btn-xs green" href="?m=formguide&c=formguide&a=edit&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('modify')?></a>
	<a class="btn btn-xs dark" href="javascript:dr_iframe_show('<?php echo L('自定义字段');?>','?m=formguide&c=formguide_field&a=init&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&is_menu=1', '80%', '90%');"><?php echo L('field')?></a>
	<a class="btn btn-xs red" href="javascript:void(0);" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes(new_html_special_chars($form['name']))))?>',function(){redirect('?m=formguide&c=formguide&a=delete&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash='+pc_hash);});"><?php echo L('del')?></a>
	<a class="btn btn-xs yellow" href="javascript:stat('<?php echo $form['modelid']?>', '<?php echo safe_replace($form['name'])?>');void(0);"><?php echo L('stat')?></a></td>
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('affirm_delete')?>',function(){document.myform.action='?m=formguide&c=formguide&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $this->db->pages;?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function stat(id, title) {
	var w = 700;
	var h = 500;
	if (is_mobile()) {
		w = h = '100%';
	}
	var diag = new Dialog({
		id:'stat',
		title:'<?php echo L('stat_formguide')?>--'+title,
		url:'<?php echo SELF;?>?m=formguide&c=formguide&a=stat&formid='+id+'&pc_hash='+pc_hash,
		width:w,
		height:h,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}
</script>