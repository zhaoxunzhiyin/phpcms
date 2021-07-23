<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=admin&c=keylink&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('keylinkid[]');" />
                        <span></span>
                    </label></th>
             <th width="30%"><?php echo L('keyword_name')?></th>
            <th ><?php echo L('link_url')?></th> 
             <th width="120"><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="keylinkid[]" value="<?php echo $info['keylinkid']?>" />
                        <span></span>
                    </label></td>
        <td width="30%" align="left"><span  class="<?php echo $info['style']?>"><?php echo $info['word']?></span> </td>
        <td align="center"><?php echo $info['url']?></td>
         <td align="center"><a href="javascript:edit(<?php echo $info['keylinkid']?>, '<?php echo new_addslashes($info['word'])?>')"><?php echo L('edit')?></a> | <a href="javascript:confirmurl('?m=admin&c=keylink&a=delete&keylinkid=<?php echo $info['keylinkid']?>', '<?php echo L('keylink_confirm_del')?>')"><?php echo L('delete')?></a> </td>
    </tr>
<?php
	}
}
?></tbody>
 </table>
 <div class="btn">
 <a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a href="#" onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input type="button" name="button" class="button" value="<?php echo L('remove_all_selected')?>" onClick="Dialog.confirm('<?php echo L('badword_confom_del')?>',function(){$('#myform').submit();});" /> 
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	artdialog('edit','?m=admin&c=keylink&a=edit&keylinkid='+id,'<?php echo L('keylink_edit')?> '+name+' ',450,150);
}

function checkuid() {
	var ids='';
	$("input[name='keylinkid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert('<?php echo L('badword_pleasechose')?>');
		return false;
	} else {
		myform.submit();
	}
}
</script>
 