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
<div class="text-center">
    <button type="button" id="dr_check_button" onclick="dr_checking();" class="btn blue"> <i class="fa fa-refresh"></i> 开始执行</button>
</div>
<div class="note note-danger margin-top-30">
    <p>技巧提示：模板代码写法是否合理，对生成速度有着极大的影响</p>
    <p>在生成静态的时候出错，最大可能性是模板的问题</p>
    <p style="color: red">如果网站没有上线，请不要生成静态；开发中的网站使用动态地址才能方便开发调试；开发完毕后上线之前再开启和生成静态功能。</p>
</div>
<div id="dr_check_result" class="margin-top-20" style="display: none">
    <div class="progress progress-striped">
        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">

        </div>
    </div>
</div>
<div id="dr_check_div" class="well margin-top-20" style="display: none">
    <div class="scroller" style="height:300px" data-rail-visible="1"  id="dr_check_html"></div>
</div>
<input id="dr_check_status" type="hidden" value="1">
<script>
$(function () {
    dr_checking();
});
function dr_checking() {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "<?php echo $count_url;?>",
        success: function (json) {
            if (json.code == 0) {
                $('#dr_check_div').show();
                $('#dr_check_html').html('<font color="red">'+json.msg+'</font>');
            } else {
                $('#dr_check_html').html("");
                $('#dr_check_div').show();
                //$('#dr_check_result').show();
                $('#dr_check_button').html('正在初始化');
                $('#dr_check_button').attr('disabled', true);
                dr_ajax2ajax(<?php echo max(intval($page), 1);?>);
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
        url: "<?php echo $todo_url;?>&pp="+page,
        success: function (json) {
            if (json.code < 99 && $('.todo_p').length > 100) {
                $('#dr_check_html').html("");
            }
            $('#dr_check_html').append(json.msg);
            document.getElementById('dr_check_html').scrollTop = document.getElementById('dr_check_html').scrollHeight;

            //$('#dr_check_result .progress-bar-success').attr('style', 'width:'+json.code+'%');

            if (json.code == 0) {
                $('#dr_check_button').attr('disabled', false);
                $('#dr_check_button').html('<i class="fa fa-times-circle"></i> 重新开始');
            } else {
                if (json.code == -1) {
                    $('#dr_check_status').val('0');
                    $('#dr_check_button').attr('disabled', false);
                    $('#dr_check_button').html('<i class="fa fa-check-circle"></i> 生成完毕');
                    <?php if ($go_url) {?>
                        window.location.href = '<?php echo $go_url;?>';
                    <?php }?>
                } else {
                    $('#dr_check_button').html('<i class="fa fa-refresh"></i> <?php echo $modulename ? '【'.$modulename.'】' : '';?>正在生成第'+json.code+'页 / 共'+(json.data.pcount)+'页');
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