<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link href="<?php echo JS_PATH;?>codemirror/lib/codemirror.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>codemirror/theme/neat.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>codemirror/lib/codemirror.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/mode/<?php echo $file_js;?>" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>codemirror/mode/xml/xml.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    var myTextArea = document.getElementById('file_code');
    var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {
        lineNumbers: true,
        matchBrackets: true,
        styleActiveLine: true,
        theme: "neat",
        mode: '<?php echo $file_ext; ?>'
    });
    $('#my_submit').click(function () {

        url = '<?php echo dr_now_url(); ?>';

        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 1000
        });

        $("#html_result").html(' ... ');

        $.ajax({
            type: "POST",
            dataType: "json",
            url: url,
            data: {cname:$("#cname").val(), code: myCodeMirror.getValue(), pc_hash: pc_hash, <?php echo SYS_TOKEN_NAME;?>: $("#myform input[name='<?php echo SYS_TOKEN_NAME;?>']").val()},
            success: function(json) {
                layer.close(loading);
                // token 更新
                if (json.token) {
                    var token = json.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                if (json.code == 1) {
                    dr_tips(1, json.msg);
                    setTimeout("window.location.reload(true)", 2000)
                } else {
                    dr_tips(0, '<?php echo L('模板语法解析错误'); ?>');
                    $("#html_result").html('<div class="alert alert-danger">'+json.msg+'</div>');
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, this, thrownError);;
            }
        });
    });
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<div class="note note-danger">
    <p><?php echo str_replace(array(CMS_PATH,'\\','//'), array('','/','/'), $path); ?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="row myfbody">
        <div class="col-md-12">

            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <span class="caption-subject font-green"><?php echo $name; ?></span>
                    </div>

                    <div class="actions">
                        <?php if ($backups) { ?>
                        <div class="btn-group">
                            <a class="btn green-haze btn-outline btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <?php echo L('历史文件'); ?>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu pull-right" style="height:400px;overflow: scroll;">
                                <li>
                                    <a href="<?php echo $backups_url; ?>"> <?php echo L('查看当前文件'); ?></a>
                                </li>
                                <li class="divider"> </li>
                                <?php if (isset($backups) && is_array($backups) && $backups) { $key_i=-1;$count_i=dr_count($backups);foreach ($backups as $i) { $key_i++; $is_first=$key_i==0 ? 1 : 0;$is_last=$count_i==$key_i+1 ? 1 : 0;?>
                                <li>
                                    <a href="<?php echo $backups_url; ?>&bfile=<?php echo $i; ?>"> <?php echo dr_date($i, null, 'red'); ?></a>
                                </li>
                                <?php } } ?>
                                <li class="divider"> </li>
                                <li>
                                    <a href="javascript:dr_load_ajax('<?php echo L('确定要删除吗？'); ?>', '<?php echo $backups_del; ?>', 1);"> <?php echo L('清空历史文件'); ?></a>
                                </li>
                            </ul>
                        </div>
                        <?php } ?>
                        <div class="btn-group">
                            <a class="btn" href="<?php echo $reply_url; ?>"> <i class="fa fa-mail-reply"></i> <?php echo L('返回列表'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">

                        <div class="form-group">
                            <label class="control-label col-md-2"><?php echo L('文件路径'); ?></label>
                            <div class="col-md-9">
                                <p class="form-control-static"><?php echo str_replace(array(CMS_PATH,'\\','//'), array('','/','/'), $path); ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2"><?php echo L('文件别名'); ?></label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="cname" name="cname" value="<?php echo htmlspecialchars((string)$cname); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-2"><?php echo L('内容编辑'); ?></label>
                            <div class="col-md-9">
                                <textarea id="file_code" name="code"><?php echo $code; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group" style="padding-top:30px">
                            <label class="control-label col-md-2"> </label>
                            <div class="col-md-9"  id="html_result">
                            </div>
                        </div>


                    </div>
                </div>
            </div>



        </div>
    </div>

    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <button type="button" id="my_submit" class="btn blue"> <i class="fa fa-save"></i> <?php echo L('保存内容'); ?></button>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>