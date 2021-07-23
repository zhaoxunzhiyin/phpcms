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
		<?php echo L('all_type')?>: &nbsp;&nbsp; <a href="?m=import&c=import&a=init"><?php echo L('all_import')?></a> &nbsp;&nbsp;
		<a href="?m=import&c=import&a=init&type=content"><?php echo L('content_import')?></a>&nbsp;
		<a href="?m=import&c=import&a=init&type=member"><?php echo L('member_import')?></a>&nbsp;
		<a href="?m=import&c=import&a=init&type=other"><?php echo L('other_import')?></a>&nbsp;
				</div>
		</td>
		</tr>
    </tbody>
</table>

<form name="myform" id="myform" action="?m=import&c=import&a=delete" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('importid[]');" />
                        <span></span>
                    </label></th>
 			<th width="30%"><?php echo L('import_name')?></th>
			<th width="23%" align="center"><?php echo L('import_desc')?></th>
			<th width="15%" align="center"><?php echo L('add_time')?></th>
			<th width='15%' align="center"><?php echo L('import_time')?></th>
  			<th width="8%" align="center"><?php echo L('import_type')?></th>
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
                        <input type="checkbox" class="checkboxes" name="importid[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
 		<td><?php echo $info['import_name']?></td>
		<td align="center" width="23%"><?php echo $info['desc'];?> </td>
		<td align="center" width="10%"><?php echo date("Y-m-d H:i:s",$info['addtime']);?></td>
		<td width='15%' align="center"><?php if($info['lastinputtime']){echo date("Y-m-d H:i:s",$info['lastinputtime']);}else {echo '<font color=red>未执行</font>';}?></td>
 	 
		<td width="8%" align="center"><?php echo $info['type'];?></td>
		<td align="center" width="12%">
		<a href="?m=import&c=import&a=choice&importid=<?php echo $info['id'];?>&type=<?php echo $info['type']?>" title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a href="?m=import&c=import&a=do_import&importid=<?php echo $info['id'];?>&type=<?php echo $info['type']?>" title="<?php echo L('edit')?>"><?php echo L('do_import');?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="submit" type="submit" class="button"
	value="<?php echo L('delete_select');?>"
	onClick="return confirm(<?php echo L('delete_confirm');?>)">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
</body>
</html>
