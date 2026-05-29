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
                <div class="r ct_box nobrd">
                    <div class="nr">
                        <table cellpadding="0" cellspacing="0" class="table_list">
                            <tr>
                                <th class="col1">目录文件</th>
                                <th class="col2">所需状态</th>
                                <th class="col3">当前状态</th>
                            </tr>
                            <?php foreach ($path as $name=>$code) {?>
                            <tr>
                                <td><?php echo str_replace(array(CMS_PATH, '\\'), array('', '/'), $name) ? str_replace(array(CMS_PATH, '\\'), array('', '/'), $name) : '网站根目录';?></td>
                                <td><span><img src="images/correct.png" />&nbsp;可写</span></td>
                                <td><?php echo $code ? '<span><img src="images/correct.png" />&nbsp;可写</span>' : '<font class="red"><img src="images/error.png" />&nbsp;不可写</font>'?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="btn_box"><?php if(!$error) {?>
    <a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="btn btn-success">下一步安装</a>
    <?php } else {?>
    <a onClick="Dialog.alert('存在不可写目录或者文件');" class="btn default">无法进行下一步安装</a>
    <?php } ?></div>
</div>
<form id="install" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="4">
</form>
</body>
</html>