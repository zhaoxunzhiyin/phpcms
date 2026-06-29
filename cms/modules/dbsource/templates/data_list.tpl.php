<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="" method="get">
<input name="dosubmit" type="hidden" value="1">
<input type="hidden" name="m" value="dbsource" />
<input type="hidden" name="c" value="data" />
<input type="hidden" name="a" value="del" />
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
        <th width="180"><?php echo L('name')?></th>
        <th width="150"><?php echo L('output_mode')?></th>
        <th width="150"><?php echo L('stdcall')?></th>
        <th><?php echo L('data_call')?></th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['id']?>" name="id[]" />
                        <span></span>
                    </label></td>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php switch($v['dis_type']){case 1:echo 'json';break;case 2:echo 'xml';break;case 3:echo 'js';break;}?></td>
<td align="center"><?php switch($v['type']){case 0:echo L('model_configuration');break;case 1:echo L('custom_sql');break;}?></td>
<td align="center"><input type="text" ondblclick="copy_text(this)" value="<?php if($v['dis_type']==3){ echo  new_html_special_chars('<script type="text/javascript" src="'.APP_PATH.SELF.'?m=dbsource&c=call&a=get&id='.$v['id'].'"></script>')?><?php } else { echo APP_PATH;?>index.php?m=dbsource&c=call&a=get&id=<?php echo $v['id']?><?php }?>" size="30" /></td>
<td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_html_special_chars(new_addslashes($v['name']))?>')"><?php echo L('edit')?></a><a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo new_html_special_chars(new_addslashes(L('confirm', array('message'=>$v['name']))))?>',function(){redirect('?m=dbsource&c=data&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a></td>
</tr>
<?php 
    endforeach;
endif;
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('sure_deleted')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
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