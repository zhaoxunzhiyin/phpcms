<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=guestbook&c=guestbook&a=check_register" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
          <tr>
            <th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('guestid[]');" />
                        <span></span>
                    </label></th>
            <th width="35" align="center"><?php echo L('listorder')?></th>
            <th align="center"><?php echo L('guestbook_name')?></th>
            <th align="center"><?php echo L('sex')?></th>
            <th align="center"><?php echo L('lxqq')?></th>
            <th align="center"><?php echo L('email')?></th>
            <th align="center"><?php echo L('shouji')?></th>
            <th align="center"><?php echo L('web_description')?></th>
            <th align="center"><?php echo L('typeid')?></th>
            <th align="center"><?php echo L('lytime')?></th>
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
                        <input type="checkbox" class="checkboxes" name="guestid[]" value="<?php echo $info['guestid']?>" />
                        <span></span>
                    </label></td>
            <td align="center" width="35"><input name='listorders[<?php echo $info['guestid']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
            <td align="center"><?php echo $info['name']?></td>
            <td align="center"><?php echo $info['sex']?></td>
            <td align="center"><?php echo $info['lxqq'];?></td>
            <td align="center" style="color:#004499"><?php echo $info['email'];?></td>
            <td align="center"><?php echo $info['shouji'];?></td>
            <td align="center" style="color:#004499"><?php echo str_cut($info['introduce'] ,'50');?></td>
            <td align="center"><?php if($info['typeid']==0){echo "默认分类";}else{echo $type_arr[$info['typeid']];}?></td>
            <td align="center"><?php echo date('Y-m-d H:i',$info['addtime']);?></td>
            <td width="8%" align="center"><?php if($info['passed']=='0'){?>
              <a
			href='?m=guestbook&c=guestbook&a=check&guestid=<?php echo $info['guestid']?>'
			onClick="return confirm('<?php echo L('pass_or_not')?>')"><font color=red><?php echo L('audit')?></font></a>
              <?php }else{echo L('passed');}?></td>
            <td align="center" width="12%"><a href="###"
			onclick="show(<?php echo $info['guestid']?>, '<?php echo new_addslashes($info['name'])?>')"
			title="<?php echo L('show')?>"><?php echo L('show')?></a> | <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=guestbook&c=guestbook&a=delete&guestid=<?php echo $info['guestid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
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
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('pass_check')?>"
	onClick="return confirm('<?php echo L('pass_or_not')?>')">&nbsp;&nbsp;<input type="button" class="button" name="dosubmit" onclick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=guestbook&c=guestbook&a=delete';$('#myform').submit();});" value="<?php echo L('delete')?>"/> </div>
<div id="pages"><?php echo $this->pages?></div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	artdialog('edit','?m=link&c=guestbook&a=edit&guestbookid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function show(id, name) {
	artdialog('edit','?m=guestbook&c=guestbook&a=show&guestid='+id,'<?php echo L('show')?> '+name+' ',700,450);
}
function checkuid() {
	var ids='';
	$("input[name='guestid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('before_select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
</script>
