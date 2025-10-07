<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                <?php foreach($steps as $i=>$t) {?>
                    <li class="ma<?php echo $i;?><?php if($i<=$step) echo ' on';?>"><?php echo $t;?></li>
                <?php }?>
                </ul>
            </div>
            <div class="bz a<?php echo $step;?>"><div class="jj_bg"></div></div>
        </div>
        <div class="ct">
            <div class="clr">
                <div class="l">
                    <dl>
                        <dt>PHPCMS 新版下载：</dt>
                        <dd><a href="https://gitee.com/zhaoxunzhiyin/phpcms" target="_blank">https://gitee.com/zhaoxunzhiyin</a></dd>
                        <dt>QQ在线支持：</dt>
                        <dd><a href="http://wpa.qq.com/msgrd?v=3&uin=297885395&site=PHPCMS&menu=yes" target="_blank">297885395</a></dd>
                        <dt>QQ讨论群：</dt>
                        <dd><a href="https://jq.qq.com/?_wv=1027&k=iRONFLwT" target="_blank">551419699</a></dd>
                        <?php if(PC_VERSION || PC_RELEASE){ ?>
                        <dt>程序版本：</dt>
                        <dd>PHPCMS <?php echo PC_VERSION?> [<?php echo PC_RELEASE?>]</dd>
                        <?php }?>
                        <?php if(CMS_VERSION || CMS_RELEASE){ ?>
                        <dt>当前版本：</dt>
                        <dd>CMS <?php echo CMS_VERSION?> [<?php echo CMS_RELEASE?>]</dd>
                        <?php }?>
                    </dl>
                </div>
                <div class="r ct_box">
                    <div class="scroller" style="height:300px" data-rail-visible="1"  id="dr_check_html">
                        <p>正在执行更新缓存...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn_box">
            <a href="javascript:history.back();" class="btn btn-success"> 返回上一步 </a>
            <a href="javascript:void(0);" class="btn default" id="finish">正在执行更新缓存</a>
        </div>            
    </div>
</div>
<form id="myform" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="cache">
</form>
<form id="install" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="7">
</form>
</body>
<script language="javascript">
$().ready(function() {
    initSlimScroll('.scroller');
    reloads(1);
})
function reloads(page) {
    $.ajax({
        type: "POST",
        dataType: "json",
        url: '<?php echo SELF;?>',
        data: $("#myform").serialize()+"&page="+page,
        success: function (json) {
            $('#dr_check_html').append("<p>"+json.msg+"</p>");
            document.getElementById('dr_check_html').scrollTop = document.getElementById('dr_check_html').scrollHeight;

            if (json.code == 0) {
                $('.btn_box').removeClass("d_n");
                $('#dr_check_html').append("<p style='color:red'>出现故障："+json.msg+"</p>");
                return;
            } else {
                if (json.data.page == 99) {
                    // 完成
                    $('#btn_box').removeClass("d_n");
                    $('#finish').html('安装完成');
                    setTimeout("$('#install').submit();",1000);
                } else {
                    reloads(json.data.page);
                }
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            $('#dr_check_html').append("<p style='color:red'>出现故障："+HttpRequest.responseText+"</p>");
        }
    });
}
function initSlimScroll(a) {
    $().slimScroll &&
    $(a).each(function () {
        if (!$(this).attr("data-initialized")) {
        var a;
        a = $(this).attr("data-height")
            ? $(this).attr("data-height")
            : $(this).css("height");
        $(this).slimScroll({
            allowPageScroll: !0,
            size: "7px",
            color: $(this).attr("data-handle-color")
                ? $(this).attr("data-handle-color")
                : "#bbb",
            wrapperClass: $(this).attr("data-wrapper-class")
                ? $(this).attr("data-wrapper-class")
                : "slimScrollDiv",
            railColor: $(this).attr("data-rail-color")
                ? $(this).attr("data-rail-color")
                : "#eaeaea",
            position: "right",
            height: a,
            alwaysVisible:
                "1" == $(this).attr("data-always-visible") ? !0 : !1,
            railVisible: "1" == $(this).attr("data-rail-visible") ? !0 : !1,
            disableFadeOut: !0
        });
        $(this).attr("data-initialized", "1");
        }
    });
}
</script>
</html>