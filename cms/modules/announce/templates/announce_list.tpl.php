<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=announce&c=admin_announce&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('aid[]');" />
                        <span></span>
                    </label></th>
			<th align="center"><?php echo L('title')?></th>
			<th width="68" align="center"><?php echo L('startdate')?></th>
			<th width='68' align="center"><?php echo L('enddate')?></th>
			<th width='68' align="center"><?php echo L('inputer')?></th>
			<th width="50" align="center"><?php echo L('hits')?></th>
			<th width="120" align="center"><?php echo L('inputtime')?></th>
			<th width="69" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
	foreach($data as $announce){
?>   
	<tr>
	<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="aid[]" value="<?php echo $announce['aid']?>" />
                        <span></span>
                    </label></td>
	<td><?php echo $announce['title']?></td>
	<td align="center"><?php echo $announce['starttime']?></td>
	<td align="center"><?php echo $announce['endtime']?></td>
	<td align="center"><?php echo $announce['username']?></td>
	<td align="center"><?php echo $announce['hits']?></td>
	<td align="center"><?php echo date('Y-m-d H:i:s', $announce['addtime'])?></td>
	<td align="center">
	<?php if ($this->input->get('s')==1) {?><a href="?m=announce&c=index&a=show&aid=<?php echo $announce['aid']?>" title="<?php echo L('preview')?>"  target="_blank"><?php }?><?php echo L('index')?><?php if ($this->input->get('s')==1) {?></a><?php }?> | 
	<a href="javascript:edit('<?php echo $announce['aid']?>', '<?php echo safe_replace($announce['title'])?>');void(0);"><?php echo L('edit')?></a>
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
        <?php if($this->input->get('s')==1) {?><input name='dosubmit' type='submit' class="button" value='<?php echo L('cancel_all_selected')?>' onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=0'"><?php } elseif($this->input->get('s')==2) {?><input name='dosubmit' type='submit' class="button" value='<?php echo L('pass_all_selected')?>' onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=1'"><?php }?>&nbsp;&nbsp;
		<input name="button" type="button" class="button" value="<?php echo L('remove_all_selected')?>" onClick="Dialog.confirm('<?php echo L('affirm_delete')?>',function(){document.myform.action='?m=announce&c=admin_announce&a=delete';$('#myform').submit();});">&nbsp;&nbsp;</div>  </div>
 <div id="pages"><?php echo $this->db->pages;?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
	artdialog('edit','?m=announce&c=admin_announce&a=edit&aid='+id,'<?php echo L('edit_announce')?>--'+title,700,500);
}
</script>