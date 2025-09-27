<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box myfbody">
<form name="myform" action="?m=collection&c=node&a=import_program_add&nodeid=<?php if(isset($nodeid)) echo $nodeid?>&type=<?php echo $type?>&ids=<?php echo $ids?>&catid=<?php echo $catid?>" method="post" id="myform">
<input type="hidden" name="menuid" value="<?php echo $this->input->get('menuid')?>">
<input name="dosubmit" type="hidden" value="1">
<fieldset>
    <legend><?php echo L('the_new_publication_solutions')?></legend>
    
        <table width="100%" class="table_form">
            <tr>
            <td width="130"><?php echo L('category')?>：</td> 
            <td>
            <?php echo $cat['catname'];?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('the_withdrawal_of_the_summary')?>：</td> 
            <td>
            <label class="mt-checkbox mt-checkbox-outline"><input name="is_auto_description_content" type="checkbox" value="1"><?php echo L('if_the_contents_of_intercepted')?><span></span></label> <label><input type="text" name="auto_description_content" value="200" size="3"></label> <label><?php echo L('characters_to_a_summary_of_contents')?></label>
            </td>
        </tr>
        <tr>
            <td><?php echo L('the_withdrawal_of_thumbnails')?>：</td> 
            <td>
            <label class="mt-checkbox mt-checkbox-outline"><input type='checkbox' name='is_auto_thumb_content' value="1"><?php echo L('whether_access_to_the_content_of')?><span></span></label> <label><input type="text" name="auto_thumb_content" value="1" size="2" class=""></label> <label><?php echo L('picture_a_caption_pictures')?></label>
            </td>
        </tr>
        <tr>
            <td><?php echo L('去除站外链接')?>：</td> 
            <td>
            <label class="mt-checkbox mt-checkbox-outline"><input type='checkbox' name='is_remove_a_content' value="1"><?php echo L('是否去除站外链接')?><span></span></label>
            </td>
        </tr>
        <tr>
            <td><?php echo L('import_article_state')?>：</td> 
            <td>
            <?php if(!empty($cat['setting']['workflowid'])) {echo form::radio(array('1'=>L('pendingtrial'), '99'=>L('fantaoboys')), '1', 'name="content_status"');} else {echo form::radio(array('99'=>L('fantaoboys')), '99', 'name="content_status"');}?>
            </td>
        </tr>
    </table>
</fieldset>
<div class="bk10"></div>
<fieldset>
    <legend><?php echo L('corresponding_labels_and_a_database_ties') ?></legend>
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="left"><?php echo L('the_original_database_field')?></th>
            <th align="left"><?php echo L('explain')?></th>
            <th align="left"><?php echo L('label_field__collected_with_the_result_')?></th>
            <th align="left"><?php echo L('handler_functions')?></th>
        </tr>
    </thead>
<tbody>
<?php
    foreach($model as $k=>$v) {
        if (in_array($v['formtype'], array('catid', 'typeid', 'posids', 'groupid', 'readpoint','template'))) continue;
?>
    <tr>
        <td align="left"><?php echo $v['field']?></td>
        <td align="left"><?php echo $v['name']?></td>
        <td align="left"><input type="hidden" name="model_field[]" value="<?php echo $v['field']?>"><?php echo form::select($node_field, (in_array($v['field'], array('inputtime', 'updatetime')) ? 'time' : $v['field']), 'name="node_field[]"')?></td>
        <td align="left"><label><input type="text" id="funcs" name="funcs[]" class="funcs"></label><?php echo form::select($spider_funs, '', 'name="" onchange="$(\'#funcs\').val(this.value);"', '请选择')?></td>
    </tr>
<?php
    }

?>
</tbody>
</table>
</div>
</fieldset>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=collection&c=node&a=import_program_add&nodeid=<?php if(isset($nodeid)) echo $nodeid?>&type=<?php echo $type?>&ids=<?php echo $ids?>&catid=<?php echo $catid?>', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>