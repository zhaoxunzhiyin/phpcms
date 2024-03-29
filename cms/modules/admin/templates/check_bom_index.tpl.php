<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
.progress {border: 0;background-image: none;filter: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}
.progress {height: 20px;background-color: #fff;border-radius: 4px;}
.progress-bar-success {background-color: #3ea9e2;}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p>如果使用编辑器修改配置或者查看文件时没有注意编辑器设置将可能在被编辑的文件上附加BOM头, 从而造成系统功能异常</p>
</div>

<div class="text-center">
    <button type="button" id="dr_check_button" onclick="dr_checking();" class="btn blue"> <i class="fa fa-refresh"></i> 单击检测全站PHP文件</button>
</div>

<div id="dr_check_result" class="margin-top-30" style="display: none">

</div>

<div id="dr_check_div"  class="well margin-top-30" style="display: none">
    <div class="scroller" style="height:300px" data-rail-visible="1"  id="dr_check_html">

    </div>
</div>

<div id="dr_check_ing" style="display: none">
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">

        </div>
    </div>
</div>

<div class="portlet light bordered" style="margin-top: 30px;">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject  sbold ">检测结果</span>
        </div>

    </div>
    <div class="portlet-body" style="padding-bottom: 40px;">
        <div id="dr_check_bf">

        </div>
    </div>
</div>
<script>
function dr_checking() {
    $('#dr_check_button').attr('disabled', true);
    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 准备中');
    $('#dr_check_bf').html("");
    $('#dr_check_html').html("正在准备中");
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "?m=admin&c=check_bom&a=public_php_count",
        success: function (json) {
            if (json.code == 0) {
                dr_tips(0, '没有查询到php文件，请检查目录权限');
            } else {
                $('#dr_check_html').html("");
                $('#dr_check_result').html($('#dr_check_ing').html());
                $('#dr_check_div').show();
                $('#dr_check_result').show();
                $('#dr_check_button').attr('disabled', true);
                dr_ajax2ajax(1);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
function dr_ajax2ajax(page) {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "?m=admin&c=check_bom&a=public_php_check&page="+page,
        success: function (json) {

            $('#dr_check_html').append(json.msg);
            document.getElementById('dr_check_html').scrollTop = document.getElementById('dr_check_html').scrollHeight;

            if (json.code == 0) {
                $('#dr_check_button').attr('disabled', false);
                $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新检测全站PHP文件');
                dr_tips(0, '发现异常');
            } else {
                $('#dr_check_result .progress-bar-success').attr('style', 'width:'+json.code+'%');
                if (json.code == 100) {
                    $('#dr_check_button').attr('disabled', false);
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新检测全站PHP文件');
                    var isxs = 0;
                    $("#dr_check_html .rbf").each(function(){
                        $('#dr_check_bf').append('<p>'+$(this).html()+'</p>');
                        isxs = 1;
                    });
                } else {
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 文件检测中 '+json.code+'%');
                    dr_ajax2ajax(json.code);
                }
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
</script>
</div>
</div>
</div>
</div>
</body>
</html>