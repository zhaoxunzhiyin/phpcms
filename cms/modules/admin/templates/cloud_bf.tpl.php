<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$menu_data = $this->menu_db->get_one(array('name' => 'version_update', 'm' => 'admin', 'c' => 'cloud', 'a' => 'upgrade'));?>
<style type="text/css">
.progress {border: 0;background-image: none;filter: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}
.progress {height: 20px;background-color: #fff;border-radius: 4px;}
.progress-bar-success {background-color: #3ea9e2;}
.badge-success {background-color: #36c6d3;}
</style>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery.slimscroll.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p>本功能用于检测本地核心文件与服务器最新版文件的差异</p>
    <p>本次检测中的文件强烈建议开发者不要去修改，否则会引起系统不稳定或者系统崩溃</p>
</div>

<div class="text-center">
    <button type="button" id="dr_check_button" onclick="dr_checking();" class="btn blue"> <i class="fa fa-refresh"></i> 立即与服务器文件对比差异</button>
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
            <span class="caption-subject  sbold ">对比结果</span>
        </div>

    </div>
    <div class="portlet-body">
        <div id="dr_check_bf">

        </div>
    </div>
</div>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
function dr_checking() {
    $('#dr_check_button').attr('disabled', true);
    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 准备中');
    $('#dr_check_bf').html("");
    $('#dr_check_html').html("正在准备中");
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "?m=admin&c=cloud&a=bf_count&pc_hash="+pc_hash,
        success: function (json) {
            if (json.code == 0) {
                dr_tips(0, json.msg);
                $('#dr_check_div').show();
                $('#dr_check_result').show();
                $('#dr_check_button').attr('disabled', false);
                $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新对比');
                $('#dr_check_html').append('<p style="color: red">'+json.msg+'</p>');
            } else {
                $('#dr_check_bf').html("");
                $('#dr_check_html').html("");
                $('#dr_check_result').html($('#dr_check_ing').html());
                $('#dr_check_div').show();
                $('#dr_check_result').show();
                $('#dr_check_button').attr('disabled', true);
                $('#dr_check_bf').append('<p style="color: green">本网站程序下载时间：<?php echo CMS_DOWNTIME;?></p>');
                $('#dr_check_bf').append('<p style="color: green">服务端最近更新时间：'+json.msg+'</p>');
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
        url: "?m=admin&c=cloud&a=bf_check&page="+page+"&pc_hash="+pc_hash,
        success: function (json) {

            $('#dr_check_html').append(json.msg);
            document.getElementById('dr_check_html').scrollTop = document.getElementById('dr_check_html').scrollHeight;

            if (json.code == 0) {
                $('#dr_check_button').attr('disabled', false);
                $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新对比');
                dr_tips(0, '发现异常');
            } else {
                $('#dr_check_result .progress-bar-success').attr('style', 'width:'+json.code+'%');
                if (json.code == 100) {
                    $('#dr_check_button').attr('disabled', false);
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 重新对比');
                    // 对比结果
                    var isxs = 0;
                    $("#dr_check_html .rbf").each(function(){
                        $('#dr_check_bf').append('<p>'+$(this).html()+'</p>');
                        isxs = 1;
                    });
                    if (isxs == 1) {
                        $('#dr_check_bf').append('<p style="text-align: center"><a class="btn green" href="javascript:;" layuimini-content-href="?m=admin&c=cloud&a=upgrade&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="版本升级" data-icon="fa fa-refresh"> <i class="fa fa-refresh"></i> 前往下载升级包</a> <a class="btn red" href="https://gitee.com/zhaoxunzhiyin/phpcms/" target="_blank"> <i class="fa fa-download"></i> 前往下载完整包，然后手动替换以上红色的文件</a></p>');
                    }
                } else {
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> 文件对比中 '+json.code+'%');
                    dr_ajax2ajax(json.code);
                }
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
layui.use(['layer', 'miniTab'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniTab = layui.miniTab;
    miniTab.listen();
});
</script>
</div>
</div>
</div>
</div>
</body>
</html>