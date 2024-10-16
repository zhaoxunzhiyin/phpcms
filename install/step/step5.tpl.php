<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                <?php foreach($steps as $i=>$t) {?>
                    <li class="ma<?php echo $i;?> <?php if($i<=$step) echo ' on';?>"><?php echo $t;?></li>
                <?php }?>
                </ul>
            </div>
            <div class="bz a<?php echo $step;?>"><div class="jj_bg"></div></div>
        </div>
        <div class="ct">
            <div class="bg_t"></div>
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
                <div class="ct_box">
                    <div class="nr">
                        <div id="dr_check_html">
                            <p>正在执行安装程序...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg_b"></div>
        </div>
        <div class="btn_box">
            <a href="javascript:history.back();" class="btn btn-success"> 返回上一步 </a>
            <a href="javascript:void(0);" class="btn default" id="finish">正在执行安装程序</a>
        </div>            
    </div>
</div>
<form id="myform" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="sql">
</form>
<form id="install" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="6">
</form>
</body>
<script language="javascript">
$().ready(function() {
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
</script>
</html>