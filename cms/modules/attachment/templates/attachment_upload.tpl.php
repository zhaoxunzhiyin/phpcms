<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<?php echo load_js(JS_PATH.'jquery-ui/jquery-ui.js');?>
<?php echo load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');?>
<?php echo load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="margin-top:20px;margin-bottom:30px;">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="myfbody text-center">
        <label class="wm-fileupload-img">
            <span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span><?php echo L('上传');?></span> <input type="file" name="file_data" title=""> </span>
        </label>

        <span class="help-block"><?php echo $data['filepath'];?></span>
    </div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
$(function(){
    // 初始化上传组件
    $('.wm-fileupload-img').fileupload({
        disableImageResize: false,
        autoUpload: true,
        maxFileSize: 2,
        url: '?m=attachment&c=manage&a=public_upload_edit&aid=<?php echo $data['aid'];?>',
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
            if (data.result.code) {
                parent.layer.closeAll();
            }
            top.dr_tips(data.result.code, data.result.msg);
        },
    });
});
</script>
</body>
</html>