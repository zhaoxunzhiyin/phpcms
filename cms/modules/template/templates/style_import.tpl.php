<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');?>
<?php echo load_js(JS_PATH.'jquery-ui/jquery-ui.js');?>
<?php echo load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');?>
<?php echo load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<script type="text/javascript">
    $(function() { //防止回车提交表单
        document.onkeydown = function(e){
            var ev = document.all ? window.event : e;
            if (ev.keyCode==13) {
                return false;
            }
        }
    });
</script>
<form class="form-horizontal" role="form" id="myform" name="myform" action="" method="post">
    <div class="form-body">
        <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('mode');?></label>
            <div class="col-xs-7">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline"><input type="radio" name="type" value="1" checked /> <?php echo L('upload_file')?> <span></span></label>
                    <label class="mt-radio mt-radio-outline"><input type="radio" name="type" value="2" /> <?php echo L('enter_coad')?> <span></span></label>
                </div>
            </div>
        </div>
        <div class="form-group" id="upfile">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('upload_file');?></label>
            <div class="col-xs-7">
                <input type="hidden" id="filename" name="filename">
                <label class="wm-fileupload-txt"><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span><?php echo L('select_file');?></span> <input type="file" name="file_data" title=""> </span> </label>
                <span class="help-block"><?php echo L('only_allowed_to_upload_txt_files')?></span>
            </div>
        </div>
        <div class="form-group" id="code" style="display: none">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('enter_coad');?></label>
            <div class="col-xs-7">
                <textarea name="code" class="form-control" style="height:180px;"></textarea>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
$(function(){$("input[type='radio'][name='type']").click(function(){
    if ($(this).val()==1) {
        $('#upfile').show();
        $('#code').hide();
    } else{
        $('#code').show();
        $('#upfile').hide();
    }
})})
$(function(){
    // 初始化上传组件
    $('.wm-fileupload-txt').fileupload({
        disableImageResize: false,
        autoUpload: true,
        maxFileSize: 2,
        url: '?m=template&c=style&a=public_upload_index',
        dataType: 'json',
        formData : {
            '<?php echo SYS_TOKEN_NAME;?>': '<?php echo csrf_hash();?>',
        },
        progressall: function (e, data) {
            // 上传进度条 all
            var progress = parseInt(data.loaded / data.total * 100, 10);
            layer.msg(progress+'%');
        },
        add: function (e, data) {
            data.submit();
        },
        done: function (e, data) {
            //console.log($(this).html());
            dr_tips(data.result.code, data.result.msg);
            if (data.result.code) {
                $('#filename').val(data.result.data.file);
            }

        },
    });
});
</script>
</body>
</html>