<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad_10">
<div class="table-list">
<form name="myform" id="myform" action="" method="get">
<input type="hidden" name="m" value="dbsource" />
<input type="hidden" name="c" value="data" />
<input type="hidden" name="a" value="del" />
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="80" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
		<th><?php echo L('name')?></th>
		<th><?php echo L('output_mode')?></th>
		<th><?php echo L('stdcall')?></th>
		<th><?php echo L('data_call')?></th>
		<th width="150"><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
	foreach($list as $v):
?>
<tr>
<td width="80" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['id']?>" name="id[]" />
                        <span></span>
                    </label></td>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php switch($v['dis_type']){case 1:echo 'json';break;case 2:echo 'xml';break;case 3:echo 'js';break;}?></td>
<td align="center"><?php switch($v['type']){case 0:echo L('model_configuration');break;case 1:echo L('custom_sql');break;}?></td>
<td align="center"><input type="text" ondblclick="copy_text(this)" value="<?php if($v['dis_type']==3){ echo  new_html_special_chars('<script type="text/javascript" src="'.APP_PATH.'index.php?m=dbsource&c=call&a=get&id='.$v['id'].'"></script>')?><?php } else { echo APP_PATH?>index.php?m=dbsource&c=call&a=get&id=<?php echo $v['id']?><?php }?>" size="30" /></td>
<td align="center"><a href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_html_special_chars(new_addslashes($v['name']))?>')"><?php echo L('edit')?></a> | <a href="###" onclick="Dialog.confirm('<?php echo new_html_special_chars(new_addslashes(L('confirm', array('message'=>$v['name']))))?>',function(){redirect('?m=dbsource&c=data&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
</tr>
<?php 
	endforeach;
endif;
?>
</tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="button" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('sure_deleted')?>',function(){$('#myform').submit();});"/>
</div>
</form>
</div>
</div>
<div id="pages"><?php echo $pages?></div>
<script type="text/javascript">
<!--
function edit(id, name) {
	artdialog('edit','?m=dbsource&c=data&a=edit&id='+id,'<?php echo L('editing_data_sources_call')?>《'+name+'》',700,500);
}

function copy_text(matter){
	matter.select();
	js1=matter.createTextRange();
	js1.execCommand("Copy");
	Dialog.alert('<?php echo L('copy_code');?>');
}
//-->
</script>
</body>
</html>