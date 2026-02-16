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
                    <div class="nr">
                        <?php echo format_textarea($license)?>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn_box"><a href="javascript:void(0);" disabled="" id="ok" class="btn btn-success" onclick=""> ... </a></div>
        <form id="install" action="<?php echo SELF;?>" method="post">
        <input type="hidden" name="step" value="2">
        </form>
    </div>
</div>
<script>
    var Seconds = 10;
    var setIntervalID;
    function ok() {
        var ok = $("#ok");
        if (Seconds <= 0) {
            ok.html("同意协议");
            ok.attr('onclick', '$("#install").submit();return false;');
            ok.attr('disabled', false);
            clearInterval(setIntervalID);
        } else {
            ok.html("请仔细阅读协议还剩下（" + Seconds + "）秒");
        }
        Seconds--;
    }
    setIntervalID=setInterval("ok()", 1000);
</script>
</body>
</html>