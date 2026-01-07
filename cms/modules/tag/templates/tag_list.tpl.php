<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form id="myform" name="myform" action="?m=tag&c=tag&a=del" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
        <th width="200"><?php echo L('name')?></th>
        <th width="120"><?php echo L('stdcall')?></th>
        <th><?php echo L('stdcode')?></th>
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
<td align="center"><?php switch($v['type']){case 0:echo L('model_configuration');break;case 1:echo L('custom_sql');break;case 2:echo L('block');}?></td>
<td align="center"><textarea ondblclick="copy_text(this)" style="width: 400px;height:30px" /><?php echo new_html_special_chars($v['tag'])?></textarea></td>
<td align="center"><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['id']?>, '<?php echo new_html_special_chars(new_addslashes($v['name']))?>')"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo new_html_special_chars(new_addslashes(L('confirm', array('message'=>$v['name']))))?>',function(){redirect('?m=tag&c=tag&a=del&id=<?php echo $v['id']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a></td>
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
        <label><button type="submit" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=tag&c=tag&a=edit&id='+id,'<?php echo L('editing_data_sources_call')?>《'+name+'》',700,500);
}
function checkuid() {
    var ids='';
    $("input[name='id[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('至少选择一条信息')?>");
        return false;
    } else {
        Dialog.confirm('<?php echo L('sure_deleted')?>',function(){myform.submit();});
    }
}
function copy_text(matter){
    matter.select();
    // 执行浏览器复制命令
    document.execCommand("Copy");
    //提示已复制
    dr_tips(1, '<?php echo L('copy_code');?>');
}
</script>
</body>
</html>