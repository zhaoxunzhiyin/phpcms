<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                    <li class="ma1 on">准备安装</li>
                    <li class="ma2 on">检查环境</li>
                    <li class="ma3">模块选择</li>
                    <li class="ma4">权限检测</li>
                    <li class="ma5">配置信息</li>
                    <li class="ma6">开始安装</li>
                    <li class="ma7">安装完成</li>
                </ul>
            </div>
            <div class="bz a2"><div class="jj_bg"></div></div>
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
                            <tr>
                                <td>PHP 版本</td>
                                <td>PHP <?php echo phpversion();?></td>
                                <td>PHP 7.0.0 及以上</td>
                                <td><?php if(phpversion() >= '7.0.0'){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;无法安装</font><?php }?></font></td>
                            </tr>
                            <tr>
                                <td>MYSQLI 扩展</td>
                                <td><?php if(extension_loaded('mysqli')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>必须开启</td>
                                <td><?php if(extension_loaded('mysqli')){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;无法安装</font><?php }?></td>
                            </tr>               
                            <tr>
                                <td>ICONV/MB_STRING 扩展</td>
                                <td><?php if(extension_loaded('iconv') || extension_loaded('mbstring')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>必须开启</td>
                                <td><?php if(extension_loaded('iconv') || extension_loaded('mbstring')){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;字符集转换效率低</font><?php }?></td>
                            </tr>
                            <tr>
                                <td>JSON扩展</td>
                                <td><?php if($PHP_JSON){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>必须开启</td>
                                <td><?php if($PHP_JSON){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不只持json,<a href="http://pecl.php.net/package/json" target="_blank">安装 PECL扩展</a></font><?php }?></td>
                            </tr>
                            <tr>
                                <td>GD 扩展</td>
                                <td><?php if($PHP_GD){ ?>√ （支持 <?php echo $PHP_GD;?>）<?php }else{ ?>×<?php }?></td>
                                <td>建议开启</td>
                                <td><?php if($PHP_GD){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不支持缩略图和水印</font><?php }?></td>
                            </tr>                                    
                            <tr>
                                <td>ZLIB 扩展</td>
                                <td><?php if(extension_loaded('zlib')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>建议开启</td>
                                <td><?php if(extension_loaded('zlib')){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不支持Gzip功能</font><?php }?></td>
                            </tr>
                            <tr>
                                <td>FTP 扩展</td>
                                <td><?php if(extension_loaded('ftp')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>建议开启</td>
                                <td><?php if(extension_loaded('ftp')){ ?><span><img src="images/correct.png" /></span><?php }elseif(ISUNIX){ ?><font class="red"><img src="images/error.png" />&nbsp;不支持FTP形式文件传送</font><?php }?></td>
                            </tr>
                            <tr>
                                <td>allow_url_fopen</td>
                                <td><?php if(ini_get('allow_url_fopen')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>建议打开</td>
                                <td><?php if(ini_get('allow_url_fopen')){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不支持保存远程图片</font><?php }?></td>
                            </tr>
                            <tr>
                                <td>fsockopen</td>
                                <td><?php if(function_exists('fsockopen')){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>建议打开</td>
                                <td><?php if($PHP_FSOCKOPEN=='1'){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不支持fsockopen函数</font><?php }?></td>
                            </tr>
                            <tr>
                                <td>DNS解析</td>
                                <td><?php if($PHP_DNS){ ?>√<?php }else{ ?>×<?php }?></td>
                                <td>建议设置正确</td>
                                <td><?php if($PHP_DNS){ ?><span><img src="images/correct.png" /></span><?php }else{ ?><font class="red"><img src="images/error.png" />&nbsp;不支持采集和保存远程图片</font><?php }?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="bg_b"></div>
        </div>
        <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a>
        <?php if($is_right) { ?>
        <a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn">下一步</a></div>
        <?php }else{ ?>
        <a onClick="Dialog.alert('当前配置不满足CMS安装需求，无法继续安装！');" class="x_btn pre">检测不通过</a>
         <?php }?>
        <form id="install" action="<?php echo SELF;?>" method="post">
        <input type="hidden" name="step" value="3">
        </form>
    </div>
</div>
</body>
</html>