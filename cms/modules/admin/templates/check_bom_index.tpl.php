<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
body {background-color: #f5f6f8;font-size: 14px;}
a:hover {text-decoration: underline;}
.note.note-danger {border-radius: 4px;border-left: 4px solid #f0868e;background-color: #ffffff;color: #888;}
.note.note-danger {background-color: #fef7f8;border-color: #f0868e;color: #210406;}
.note {margin: 0 0 20px;padding: 15px 30px 15px 15px;border-left: 5px solid #eee;border-radius: 0 4px 4px 0;}
.note, .tabs-right.nav-tabs>li>a:focus, .tabs-right.nav-tabs>li>a:hover {-webkit-border-radius: 0 4px 4px 0;-moz-border-radius: 0 4px 4px 0;-ms-border-radius: 0 4px 4px 0;-o-border-radius: 0 4px 4px 0;}
.page-container {margin: 0;padding: 0;position: relative;}
.finecms-file-ts, .progress {margin-bottom: 8px!important;}
.progress {border: 0;background-image: none;filter: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}
.progress {height: 20px;background-color: #fff;border-radius: 4px;}
.embed-responsive, .modal, .modal-open, .progress {overflow: hidden;}
.progress-bar-striped, .progress-striped .progress-bar {background-size: 40px 40px;}
.progress-bar-striped, .progress-striped .progress-bar, .progress-striped .progress-bar-success {background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);}
.progress-bar-striped, .progress-striped .progress-bar, .progress-striped .progress-bar-info, .progress-striped .progress-bar-success, .progress-striped .progress-bar-warning {background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);background-image: -o-linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent);}
.progress-bar-success {background-color: #36c6d3;}
.progress-bar {float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;background-color: #337ab7;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;}
.badge, .input-group-addon, .label, .pager, .progress-bar {text-align: center;}
.progress-bar {float: left;width: 0;height: 100%;font-size: 12px;line-height: 20px;color: #fff;background-color: #36c6d3;-webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);-webkit-transition: width .6s ease;-o-transition: width .6s ease;transition: width .6s ease;}
.page-content-wrapper {float: left;width: 100%;}
.page-content-wrapper .page-content {margin-top: 0;padding: 25px 20px 10px;}
.text-center {text-align: center;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none}
.btn.active.focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn:active:focus,.btn:focus {outline: dotted thin;outline: -webkit-focus-ring-color auto 5px;outline-offset: -2px}
.btn.focus,.btn:focus,.btn:hover {color: #333;text-decoration: none}
.btn.active,.btn:active {outline: 0;-webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);box-shadow: inset 0 3px 5px rgba(0,0,0,.125)}
.btn.disabled,.btn[disabled],fieldset[disabled] .btn {cursor: not-allowed;opacity: .65;filter: alpha(opacity=65);-webkit-box-shadow: none;box-shadow: none}
.btn.btn-outline.green.active,.btn.btn-outline.green:active,.btn.btn-outline.green:active:focus,.btn.btn-outline.green:active:hover,.btn.btn-outline.green:focus,.btn.btn-outline.green:hover,.btn.green-meadow:not(.btn-outline) {background-color: #3fa8e1;border-color: #3fa8e1;color: #FFF;}
.btn.green-meadow:not(.btn-outline).active,.btn.green-meadow:not(.btn-outline):active,.btn.green-meadow:not(.btn-outline):hover,.open>.btn.green-meadow:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #6eb5db;border-color: #6eb5db;}
.btn:not(.btn-sm):not(.btn-lg) {line-height: 1.44;}
.btn {outline: 0!important;}
.btn, .form-control {box-shadow: none!important;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;
font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
.margin-top-20 {margin-top: 20px!important;}
.margin-top-30 {margin-top: 30px!important;}
.btn,.btn-group,.btn-group-vertical,.caret,.checkbox-inline,.radio-inline,img {vertical-align: middle}
.well {background-color: #ffffff!important;}
.well {border: 0;padding: 20px;-webkit-box-shadow: none!important;-moz-box-shadow: none!important;box-shadow: none!important;}
.well {min-height: 20px;margin-bottom: 20px;background-color: #f1f4f7;border-radius: 4px;}
#dr_check_bf p, #dr_check_html p {margin: 10px 0;clear: both;}
#dr_check_html .p_error {color: red;}
#dr_check_html .rleft {float: left;}
#dr_check_bf .rright, #dr_check_html .rright {float: right;}
#dr_check_html .rright .ok {color: green;}
#dr_check_html .rright .error {color: red;}
label {font-weight: 400;}
#dr_check_bf p, #dr_check_html .rright .error {color: red;}
#dr_check_bf p, #dr_check_html p {margin: 10px 0;clear: both;}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content  ">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p>如果使用编辑器修改配置或者查看文件时没有注意编辑器设置将可能在被编辑的文件上附加BOM头, 从而造成系统功能异常</p>
</div>

<div class="text-center">
    <button type="button" id="dr_check_button" onclick="dr_checking();" class="btn green-meadow"> <i class="fa fa-refresh"></i> 单击检测全站PHP文件</button>
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
    <div class="portlet-body">
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
$(function() {
    $(".scroller").slimScroll({
        allowPageScroll: !0,
        size: "7px",
        color: "#bbb",
        wrapperClass: "slimScrollDiv",
        railColor: "#eaeaea",
        position: "right",
        height: '300px',
        alwaysVisible: false,
        railVisible: false,
        disableFadeOut: !0
    });
});
</script>
</div>
</div>
</div>
</div>
</body>
</html>