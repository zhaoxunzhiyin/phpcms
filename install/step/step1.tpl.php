<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                    <li class="ma1 on">准备安装</li>
                    <li class="ma2">检查环境</li>
                    <li class="ma3">模块选择</li>
                    <li class="ma4">权限检测</li>
                    <li class="ma5">配置信息</li>
                    <li class="ma6">开始安装</li>
                    <li class="ma7">安装完成</li>
                </ul>
            </div>
            <div class="bz a1"><div class="jj_bg"></div></div>
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
                <?php echo format_textarea($license)?>
                </div>
                </div>
            </div>
            <div class="bg_b"></div>
        </div>
        <div class="btn_box"><a href="javascript:void(0);" class="is_btn" onclick="$('#install').submit();return false;">开始安装</a></div>
        <form id="install" action="<?php echo SELF;?>" method="post">
        <input type="hidden" name="step" value="2">
        </form>
    </div>
</div>
</body>
</html>