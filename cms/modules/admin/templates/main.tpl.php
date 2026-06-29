<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo CHARSET;?>">
<title><?php echo L('website_manage');?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<?php echo load_css(CSS_PATH.'bootstrap/css/bootstrap.min.css');?>
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(JS_PATH.'layui/css/layui.css');?>
<?php echo load_css(CSS_PATH.'layuimini/css/public.css');?>
<?php if(!$this->get_siteid()) dr_admin_msg(0,L('admin_login'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<script type="text/javascript">
var admin_file = '<?php echo SELF;?>';
var is_admin = <?php if (cleck_admin(param::get_session('roleid'))) {?>1<?php } else { ?>0<?php } ?>;
var is_cms = 0;
var web_dir = '<?php echo WEB_PATH;?>';
var pc_hash = '<?php echo dr_get_csrf_token();?>';
var csrf_hash = '<?php echo csrf_hash();?>';
</script>
<?php echo load_js(JS_PATH.'admin_common.js');?>
<?php echo load_js(JS_PATH.'layer/layer.js');?>
<?php echo load_js(JS_PATH.'main.js');?>
<script type="text/javascript">
$(function () {
    <?php $sitelist_ccache = getcache('sitelist', 'commons');
    $common_ccache = getcache('common', 'commons');
    if(!module_exists('member') && (!is_array($sitelist_ccache) || !is_array($common_ccache))) {?>
    $.ajax({type: "GET",dataType:"json", url: "?m=admin&c=cache_all&a=init&pc_hash=<?php echo dr_get_csrf_token();?>&is_ajax=1",
        success: function(json) {
            if (json.code) {
                dr_tips(json.code, json.msg)
            }
        }
    });
    <?php }?>
});
</script>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md8">
                <div class="layui-row layui-col-space15">
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-user icon"></i><?php echo L('personal_information')?></div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10">
                                        <p><span id="nowtime"></span></p>
                                        <p><?php echo L('main_dear')?><span style="color:#ff0000;"><?php echo $admin_username?></span><span id="main_hello"></span></p>
                                        <p><?php echo L('main_role')?><?php echo $rolename?></p>
                                        <p><?php echo L('main_last_logintime')?><?php echo dr_date($logintime,null,'red')?></p>
                                        <p><?php echo L('main_last_loginip')?><?php echo $loginip?><a class="label layui-bg-green ml10" href="javascript:dr_show_ip('<?php echo WEB_PATH;?>api.php?op=ip_address', '<?php echo $loginip;?>');"><i class="fa fa-eye" /></i> 查看</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md6">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-warning icon"></i><?php echo L('main_safety_tips')?></div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10 layuimini-qiuck" style="color:#ff0000;">
                                        <?php if(!$is_ok) {
                                        $menu_data = $this->menu_db->get_one(array('name' => 'scan', 'm' => 'scan', 'c' => 'index', 'a' => 'init'));?>
                                        <p><?php echo L('※ 当前环境未能通过安全验证，请按要求设置参数，<a href="javascript:;" layuimini-content-href="?m=scan&c=index&a=safe_index&menuid='.$menu_data['id'].'&pc_hash='.dr_get_csrf_token().'" data-title="安全监测" data-icon="fa fa-shield"><i class="fa fa-shield"></i><cite>点击设置</cite></a>')?></p>
                                        <?php } ?>
                                        <?php if(SELF == 'admin.php') {?>
                                        <p><?php echo L('※ 为了系统安全，请修改根目录admin.php的文件名')?></p>
                                        <?php } ?>
                                        <?php if(IS_DEV) {?>
                                        <p><?php echo L('※ 当前环境参数已经开启开发者模式，网站上线后建议关闭开发者模式')?></p>
                                        <?php } ?>
                                        <?php if($pc_writeable) {?>
                                        <p><?php echo L('main_safety_permissions')?></p>
                                        <?php } ?>
                                        <?php if(IS_EDIT_TPL) {?>
                                        <p><?php echo L('main_safety_tpledit')?></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-col-md12">
                        <div class="layui-card">
                            <div class="layui-card-header"><i class="fa fa-credit-card icon icon-blue"></i>快捷入口</div>
                            <div class="layui-card-body">
                                <div class="welcome-module">
                                    <div class="layui-row layui-col-space10 layuimini-qiuck">
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="?m=admin&c=index&a=public_icon" data-title="图标管理" data-icon="fa fa-cog">
                                                <i class="fa fa-cog"></i>
                                                <cite>图标管理</cite>
                                            </a>
                                        </div>
                                        <?php foreach($adminpanel as $v) {?>
                                        <div class="layui-col-xs3 layuimini-qiuck-module">
                                            <a href="javascript:;" layuimini-content-href="<?php echo $v['url'].'&menuid='.$v['menuid'];?>&pc_hash=<?php echo dr_get_csrf_token();?>" data-title="<?php echo L($v['name'])?>" data-icon="<?php echo $v['icon']?>">
                                                <i class="<?php echo $v['icon']?>"></i>
                                                <cite><?php echo L($v['name'])?></cite>
                                            </a>
                                        </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md4">

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-fire icon"></i>版本信息</div>
                    <div class="layui-card-body layui-text">
                        <table class="layui-table">
                            <colgroup>
                                <col width="120">
                                <col>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>系统版本</td>
                                <?php $menu_data = $this->menu_db->get_one(array('name' => 'my_website', 'm' => 'admin', 'c' => 'cloud', 'a' => 'init'));?>
                                <td><a href="javascript:;" layuimini-content-href="?m=admin&c=cloud&a=init&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token();?>" data-title="我的网站" data-icon="fa fa-cog"><i class="fa fa-cog"></i> Cms <?php echo CMS_VERSION?> [<?php echo CMS_RELEASE?>]</a><?php $menu_data = $this->menu_db->get_one(array('name' => 'version_update', 'm' => 'admin', 'c' => 'cloud', 'a' => 'upgrade'));?><a id="dr_cms_update" href="javascript:;" layuimini-content-href="?m=admin&c=cloud&a=upgrade&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="版本升级" data-icon="fa fa-refresh" style="display: none" class="badge badge-danger badge-roundless ml10">  </a></td>
                            </tr>
                            <script>
                            $(function () {
                                $.ajax({type: "GET",dataType:"json", url: "?m=admin&c=index&a=public_version_cms",
                                    success: function(json) {
                                        if (json.code) {
                                            $('#dr_cms_update').show();
                                            $('#dr_cms_update').html('<i class="fa fa-refresh"></i> '+json.msg);
                                        }
                                    }
                                });
                            });
                            </script>
                            <tr>
                                <td><?php echo L('main_os')?></td>
                                <td><?php echo $sysinfo['os']?></td>
                            </tr>
                            <tr>
                                <td><?php echo L('main_web_server')?></td>
                                <td><?php echo $sysinfo['web_server']?></td>
                            </tr>
                            <tr>
                                <td><?php echo L('MySQL')?></td>
                                <td><?php echo $sysinfo['mysqlv']?></td>
                            </tr>
                            <tr>
                                <td><?php echo L('main_upload_limit')?></td>
                                <td><?php echo $sysinfo['fileupload']?></td>
                            </tr>
                            <tr>
                                <td>下载地址</td>
                                <td>
                                    修改版：<a href="https://gitee.com/zhaoxunzhiyin/phpcms/" target="_blank">gitee</a> / <a href="https://github.com/zhaoxunzhiyin/phpcms/" target="_blank">github</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Gitee</td>
                                <td style="padding-bottom: 0;">
                                    <div class="layui-btn-container">
                                        <a href="https://gitee.com/zhaoxunzhiyin/phpcms/" target="_blank" style="margin-right: 15px"><img src="https://gitee.com/zhaoxunzhiyin/phpcms/badge/star.svg?theme=dark" alt="star"></a>
                                        <a href="https://gitee.com/zhaoxunzhiyin/phpcms/" target="_blank"><img src="https://gitee.com/zhaoxunzhiyin/phpcms/badge/fork.svg?theme=dark" alt="fork"></a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Github</td>
                                <td style="padding-bottom: 0;">
                                    <div class="layui-btn-container">
                                        <iframe src="https://ghbtns.com/github-btn.html?user=zhaoxunzhiyin&repo=phpcms&type=star&count=true" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
                                        <iframe src="https://ghbtns.com/github-btn.html?user=zhaoxunzhiyin&repo=phpcms&type=fork&count=true" frameborder="0" scrolling="0" width="100px" height="20px"></iframe>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="layui-card">
                    <div class="layui-card-header"><i class="fa fa-paper-plane-o icon"></i>作者心语</div>
                    <div class="layui-card-body layui-text layadmin-text">
                        <p><?php echo L('main_product_planning')?><?php echo $designer;?><?php echo $programmer;?></p>
                        <p><?php echo L('main_product_qq')?>（<?php echo $qq;?>）<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $qq;?>&site=qq&menu=yes" class="tooltips" data-container="body" data-placement="top" data-original-title="点击这里给我发消息"><i class="fa fa-qq"></i> QQ在线</a></p>
                        <p><?php echo L('main_support')?><?php echo $designer;?><?php echo $programmer;?></p>
                        <p>技术交流QQ群（<?php echo $qqgroup;?>）<a target="_blank" href="https://jq.qq.com/?_wv=1027&k=NdLwEXcR" class="tooltips" data-container="body" data-html="true" data-placement="top" data-original-title="点击这里加入群聊<br>【PHPCMS二次开发】"><i class="fa fa-users"></i> 加入QQ群</a></p>
                        <p>（加群请备注来源：如gitee官网等）</p>
                        <p>喜欢此后台模板的可以给我的Gitee加个Star支持一下</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="scroll-to-top">
    <i class="bi bi-arrow-up-circle-fill"></i>
</div>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
layui.use(['layer', 'miniTab'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniTab = layui.miniTab;
    miniTab.listen();
});
</script>
</body>
</html>