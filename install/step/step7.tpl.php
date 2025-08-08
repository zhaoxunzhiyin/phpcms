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
            <div class="bz a<?php echo $step;?>"></div>
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
                        <div class="gxwc"><h1>恭喜您，安装成功！</h1></div>
                        <div class="clj">
                            <ul>
                                <li>账号：<?php echo $data['username'];?></li>
                                <li>密码：<?php echo $data['password'];?></li>
                                <li><a href="<?php echo FC_NOW_HOST.substr($rootpath, 1).(pc_base::load_config('system','admin_login_path') ? pc_base::load_config('system','admin_login_path') : 'admin.php')?>" class="btn btn-success">后台管理</a></li>
                            </ul>
                        </div>                    
                        <div class="txt_c">
                        <?php if(pc_base::load_config('system','admin_login_path')){ ?>
                        <div class="warmtips">温馨提示：请将以下后台登录入口添加到你的收藏夹，为了你的安全，不要泄漏或发送给他人！如有泄漏请及时修改！<a href="<?php echo FC_NOW_HOST.substr($rootpath, 1).pc_base::load_config('system','admin_login_path')?>"><?php echo FC_NOW_HOST.substr($rootpath, 1).pc_base::load_config('system','admin_login_path')?></a></div>
                        <?php }?>
                        <span style="margin-right:8px;">*</span>安装完毕请登录后台生成首页，更新缓存<br/>
                        <span style="margin-right:8px;">*</span>为了您站点的安全，安装完成后即可将网站根目录下的“install”文件夹删除。</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h65"></div>
    </div>
</div>
</body>
</html>