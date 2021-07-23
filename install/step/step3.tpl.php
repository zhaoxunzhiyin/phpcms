<?php include CMS_PATH.'install/step/header.tpl.php';?>
<div class="body_box">
    <div class="main_box">
        <div class="hd">
            <div class="hd_menu">
                <ul>
                    <li class="ma1 on">准备安装</li>
                    <li class="ma2 on">检查环境</li>
                    <li class="ma3 on">模块选择</li>
                    <li class="ma4">权限检测</li>
                    <li class="ma5">配置信息</li>
                    <li class="ma6">开始安装</li>
                    <li class="ma7">安装完成</li>
                </ul>
            </div>
            <div class="bz a3"><div class="jj_bg"></div></div>
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
                        <form id="install" action="<?php echo SELF;?>" method="post">
                            <input type="hidden" name="step" value="4">
                            <fieldset>
                                <legend>配置</legend>
                                <div class="content">
                                    <div class="mt-radio-inline">
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="install" id="install_1" value="1" checked> 全新安装 <span></span></label>
                                    </div>
                                </div>
                            </fieldset>                    
                            <fieldset>
                                <legend>必选模块</legend>
                                <div class="content">
                                    <div class="mt-checkbox-inline">
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="admin" value="admin" checked disabled> 后台管理模块 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="content" value="content" checked disabled> 内容模块 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="mobile" value="mobile" checked disabled> 手机模块 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="member" value="member" checked disabled>会员模型 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="pay" value="pay" checked disabled> 财务模块 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="special" value="special" checked disabled> 专题模块 <span></span></label>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="search" value="search" checked disabled> 全文搜索 <span></span></label>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend class="table-checkable"><label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes"> 可选模块 <span></span></label></legend>
                                <div class="content">
                                    <div class="mt-checkbox-inline">
                                    <?php
                                    $count = count($CMS_MODULES['name']);
                                    foreach($CMS_MODULES['name'] as  $i=>$module) {?>
                                        <label class="mt-checkbox mt-checkbox-outline" style="width:16%;"><input type="checkbox" name="selectmod[]" value="<?php echo $module?>" class="checkboxes"> <?php echo $CMS_MODULES['modulename'][$i]?>模块 <span></span></label>
                                    <?php }?>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg_b"></div>
        </div>
        <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a><a href="javascript:void(0);"  onClick="$('#install').submit();return false;" class="x_btn">下一步</a></div>
    </div>
</div>
<script type="text/javascript">
if ($('.table-checkable')) {
    var table = $('.table-checkable');
    table.find('.group-checkable').change(function () {
        var set = jQuery(this).attr("data-set");
        var checked = jQuery(this).is(":checked");
        jQuery(set).each(function () {
            if (checked) {
                $(this).prop("checked", true);
            } else {
                $(this).prop("checked", false);
            }
        });
    });
}
</script>
</body>
</html>