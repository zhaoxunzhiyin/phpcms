<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link href="<?php echo JS_PATH;?>bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<?php echo load_js(JS_PATH.'jquery-ui/jquery-ui.js');?>
<?php echo load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');?>
<?php echo load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<div class="note note-danger">
    <p><?php echo $path; ?></p>
</div>
<form action="" class="form-horizontal" enctype="multipart/form-data" method="post" name="myform" id="myform">
    <div class="row">
        <div class="col-md-12">

            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green"><?php echo L('文件修改'); ?></span>
                    </div>

                    <div class="actions">
                        <div class="btn-group">
                            <a class="btn" href="<?php echo $reply_url; ?>"> <i class="fa fa-mail-reply"></i> <?php echo L('返回列表'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-9">
                                <p class="form-control-static"> <?php echo $preview; ?> </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('文件名'); ?></label>
                            <div class="col-md-9">
                                <p class="form-control-static"> <?php echo basename($path); ?> </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2"><?php echo L('上传文件'); ?></label>
                            <div class="col-md-9">
                                <label class="fileupload-file"><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span><?php echo L('上传文件');?></span> <input type="file" name="file" title=""> </span> </label>
                            </div>
                        </div>



                    </div>
                </div>
            </div>



        </div>
    </div>
</form>
<script type="text/javascript">
// 初始化上传组件
$('.fileupload-file').fileupload({
    disableImageResize: false,
    autoUpload: true,
    maxFileSize: 2,
    url: '<?php echo dr_now_url(); ?>',
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
            if (data.result.data) {
                setTimeout("window.location.href = '"+data.result.data+"&pc_hash="+pc_hash+"'", 2000);
            } else {
                setTimeout("window.location.reload(true)", 2000);
            }
        }

    },
});
</script>
</div>
</div>
</div>
</div>
</body>
</html>