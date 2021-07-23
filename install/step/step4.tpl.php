<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                    <li class="ma1 on">准备安装</li>
                    <li class="ma2 on">检查环境</li>
                    <li class="ma3 on">模块选择</li>
                    <li class="ma4 on">权限检测</li>
                    <li class="ma5">配置信息</li>
                    <li class="ma6">开始安装</li>
                    <li class="ma7">安装完成</li>
                </ul>
            </div>
            <div class="bz a4"><div class="jj_bg"></div></div>
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
                <div class="ct_box nobrd i6v">
                    <div class="nr">
                        <table cellpadding="0" cellspacing="0" class="table_list">
                            <tr>
                                <th class="col1">目录文件</th>
                                <th class="col2">所需状态</th>
                                <th class="col3">当前状态</th>
                            </tr>
                            <?php foreach ($filesmod as $filemod) {?>
                            <tr>
                                <td><?php echo $filemod['file']?></td>
                                <td><span><img src="images/correct.png" />&nbsp;可写</span></td>
                                <td><?php echo $filemod['is_writable'] ? '<span><img src="images/correct.png" />&nbsp;可写</span>' : '<font class="red"><img src="images/error.png" />&nbsp;不可写</font>'?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_b"></div>
    </div>
    <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn">上一步</a>
    <?php if($no_writablefile == 0) {?>
    <a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn">下一步</a>
    <?php } else {?>
    <a onClick="Dialog.alert('存在不可写目录或者文件');" class="x_btn pre">检测不通过</a>
    <?php } ?>
    </div>
    <form id="install" action="<?php echo SELF;?>" method="post">
    <input type="hidden" name="step" value="5">
    <input type="hidden" id="selectmod" name="selectmod" value="<?php echo $selectmod?>" />
    </form>
    </div>
</div>
</body>
</html>