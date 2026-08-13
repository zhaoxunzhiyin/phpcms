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
                                <th class="col1">检查项目</th>
                                <th class="col2">当前环境</th>
                                <th class="col3">CMS 建议</th>
                                <th class="col4">功能影响</th>
                            </tr>
                            <tr>
                                <td>操作系统</td>
                                <td><?php echo php_uname();?></td>
                                <td>Windows_NT/Linux/Freebsd</td>
                                <td><span><img src="images/correct.png" /></span></td>
                            </tr>
                            <tr>
                                <td>WEB 服务器</td>
                                <td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
                                <td>Apache/Nginx/IIS</td>
                                <td><span><img src="images/correct.png" /></span></td>
                            </tr>
                            <?php if(is_array($php)){
                            foreach($php as $t){?>
                            <tr>
                                <td><?php echo $t['name'];?></td>
                                <td><?php if($t['code']){ ?><?php echo $t['value'];?><?php }else{ ?><?php if($t['error']){ ?><?php $error = 1;?><?php }?>×<?php }?></td>
                                <td><?php echo $t['error_value'];?></td>
                                <td><?php if($t['code']){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" /><?php echo $t['help'];?></font><?php }?></td>
                            </tr>
                            <?php }}?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="btn_box"><?php if($error) { ?>
        <a onClick="Dialog.alert('当前配置不满足CMS安装需求，无法继续安装！');" class="btn default">无法进行下一步安装</a>
        <?php }else{ ?>
        <a href="javascript:void(0);" onClick="$('#install').submit();return false;" class="btn btn-success">下一步安装</a>
         <?php }?></div>
        <form id="install" action="<?php echo SELF;?>" method="post">
        <input type="hidden" name="step" value="3">
        </form>
    </div>
</div>
</body>
</html>