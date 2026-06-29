<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input type="hidden" name="urlruleid" id="urlruleid" value="<?php echo $urlruleid;?>">
    <div class="portlet bordered light">
        <div class="portlet-body">
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_module');?></label>
                    <div class="col-md-9">
                       <?php echo form::select($modules,$module,"name='info[module]' id='module' onchange='change(this.value)'");?>
                    </div>
                </div>
                <div class="form-group" id="dr_row_file">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_file');?></label>
                    <div class="col-md-10">
                        <label><input type="text" name="info[file]" id="file" value="<?php echo $file;?>" class="form-control"></label>
                        <span class="type"><?php echo form::select(array('category'=>'栏目', 'show'=>'内容', 'search'=>'搜索'),$file,"name='type' id='type' onchange='change_type(this.value)'", L('请选择'));?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_ishtml');?></label>
                    <div class="col-md-10">
                        <div class="mt-radio-inline">
                            <label class="mt-radio">
                                <input name="info[ishtml]" type="radio" value="1"<?php if($ishtml) echo ' checked';?>> <?php echo L('yes');?>
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input name="info[ishtml]" type="radio" value="0"<?php if(!$ishtml) echo ' checked';?>> <?php echo L('no');?>
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="dr_row_example">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_example');?></label>
                    <div class="col-md-10">
                        <input type="text" name="info[example]" id="example" value="<?php echo $example;?>" class="form-control">
                    </div>
                </div>
                <div class="form-group" id="dr_row_urlrule">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_url');?></label>
                    <div class="col-md-10">
                        <input type="text" name="info[urlrule]" id="urlrule" value="<?php echo $urlrule;?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('urlrule_func');?></label>
                    <div class="col-md-10">
                        <?php echo L('representing_search_parameters');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$param}');"> <i class="fa fa-plus"></i> {$param}</a></label>，<?php echo L('complete_parent_path');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$parentdir}');"> <i class="fa fa-plus"></i> {$parentdir}</a></label>，<?php echo L('complete_part_path');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$categorydir}');"> <i class="fa fa-plus"></i> {$categorydir}</a></label>
                        <div class="bk6"></div>
                        <?php echo L('category_path');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$catdir}');"> <i class="fa fa-plus"></i> {$catdir}</a></label>，<?php echo L('catid');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$catid}');"> <i class="fa fa-plus"></i> {$catid}</a></label>，<?php echo L('ID');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$id}');"> <i class="fa fa-plus"></i> {$id}</a></label>，<?php echo L('prefix');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$prefix}');"> <i class="fa fa-plus"></i> {$prefix}</a></label>，<?php echo L('paging');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$page}');"> <i class="fa fa-plus"></i> {$page}</a></label>
                        <div class="bk6"></div>
                        <?php echo L('year');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$year}');"> <i class="fa fa-plus"></i> {$year}</a></label> <?php echo L('month');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$month}');"> <i class="fa fa-plus"></i> {$month}</a></label>，<?php echo L('day');?>：<label><a class="btn btn-xs blue" href="javascript:insertHtml('{$day}');"> <i class="fa fa-plus"></i> {$day}</a></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
$(function() {
    change('<?php echo $module;?>');
    change_type('<?php echo $file;?>');
});
function insertHtml(text) {
    var txtarea = document.getElementById('urlrule');
    if (!txtarea) { return; }
    // 保存光标位置
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
        "ff" : (document.selection ? "ie" : false));
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br == "ff") {
        strPos = txtarea.selectionStart;
    }
    // 插入内容
    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if (br == "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}
function change(model) {
    if(model == 'content') {
        $('.type').show();
        $('#file').prop('readonly', true);
    } else {
        $('.type').hide();
        $('#file').prop('readonly', false);
    }
}
function change_type(model) {
    $('#file').val(model);
}
</script>
</body>
</html>