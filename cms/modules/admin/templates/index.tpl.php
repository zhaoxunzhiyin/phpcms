<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo CHARSET;?>">
<title><?php echo L('admin_site_title')?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(JS_PATH.'layui/css/layui.css');?>
<?php echo load_css(CSS_PATH.'layuimini/css/layuimini.css');?>
<?php echo load_css(CSS_PATH.'layuimini/css/themes/default.css');?>
<?php echo load_css(CSS_PATH.'admin/css/my.css');?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(JS_PATH.'dialog.js');?>
<?php echo load_js(JS_PATH.'jquery.backstretch.min.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<script type="text/javascript">
var is_admin = 0;
var web_dir = '<?php echo WEB_PATH;?>';
var pc_hash = '<?php echo dr_get_csrf_token();?>';
var csrf_hash = '<?php echo csrf_hash();?>';
if (top.location!=self.location){
top.location="<?php echo SELF;?>";
}
</script>
<?php echo load_js(JS_PATH.'admin_common.js');?>
<?php echo load_js(JS_PATH.'jquery.slimscroll.min.js');?>
<?php echo load_js(JS_PATH.'layer/layer.js');?>
<?php echo load_js(JS_PATH.'index.js');?>
<?php if ($admin_login_aes) {?>
<?php echo load_js(JS_PATH.'crypto-js.min.js');?>
<?php } else {?>
<?php echo load_js(JS_PATH.'jquery.md5.js');?>
<?php }?>
<!--[if lt IE 9]>
<?php echo load_js(CSS_PATH.'layuimini/js/html5.min.js');?>
<?php echo load_js(CSS_PATH.'layuimini/js/respond.min.js');?>
<![endif]-->
<style id="layuimini-bg-color"></style>
</head>
<body class="layui-layout-body layuimini-all">
<div id="ew-lock-screen-group" style="display :<?php if(param::get_session('lock_screen')==0) echo 'none';?>">
    <div class="lock-screen-wrapper">
        <div class="lock-screen-time"></div>
        <div class="lock-screen-date" id="lock-screen-date"></div>
        <div class="lock-screen-date" id="lock-screen-week"></div>
        <div class="lock-screen-form">
            <form method="post" id="lock-screen-form">
                <input id="lock_password" name="lock_password" placeholder="<?php echo L('lockscreen_status');?>" class="lock-screen-psw" type="password">
                <input name="<?php echo SYS_TOKEN_NAME;?>" type="hidden" value="<?php echo csrf_hash();?>">
                <i class="layui-icon layui-icon-right lock-screen-enter"></i>
                <br>
                <div class="lock-screen-tip"></div>
            </form>
        </div>
        <div class="lock-screen-tool">
            <div class="lock-screen-tool-item">
                <i class="layui-icon layui-icon-logout" ew-event="logout" data-confirm="false" data-url="?m=admin&c=index&a=public_logout"></i>
                <div class="lock-screen-tool-tip"><?php echo L('exit_login');?></div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $('#ew-lock-screen-group').backstretch([<?php echo implode(',', $background);?>], {
        fade: 1000,
        duration: 8000
    });
    // 获取各个组件
    var $form = $('.lock-screen-form');
    var $psw = $('.lock-screen-psw');
    var $tip = $('.lock-screen-tip');
    var $tool = $('.lock-screen-tool-item');

    // 监听enter键
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            doVer();
        } else if (event.keyCode === 8 && !$psw.val()) {
            restForm();
            if (event.preventDefault) event.preventDefault();
            if (event.returnValue) event.returnValue = false;
        }
    });

    // 监听输入
    $psw.on('input', function () {
        var psw = $psw.val();
        if (psw) {
            $form.removeClass('show-back');
            $tip.text('');
        } else {
            $form.addClass('show-back');
        }
    });

    // 监听按钮点击
    $form.find('.lock-screen-enter').click(function (e) {
        doVer(true);
    });

    // 处理事件
    function doVer(emptyRest) {
        if ($form.hasClass('show-psw')) {
            $psw.focus();
            var psw = $psw.val();
            if (!psw) {
                emptyRest ? restForm() : $tip.text('<?php echo L('lockscreen_status_password');?>');
            } else {
                if (psw.length == 32) {
                    // 已经加密过的
                } else {
                    <?php if ($admin_login_aes) {?>
                    $('#lock-screen-form').append('<input type="hidden" name="is_aes" value="1">');
                    var key = CryptoJS.enc.Utf8.parse('<?php echo substr(md5(SYS_KEY), 0, 16);?>');
                    var iv = CryptoJS.enc.Utf8.parse('<?php echo substr(md5(SYS_KEY), 10, 16);?>');
                    var pw = psw;
                    psw = CryptoJS.AES.encrypt(psw, key, {
                        mode: CryptoJS.mode.CBC,
                        iv: iv,
                        padding: CryptoJS.pad.Pkcs7
                    });
                    <?php if (IS_DEV) {?>
                    pwd2 = CryptoJS.AES.decrypt(psw, key, {
                        mode: CryptoJS.mode.CBC,
                        iv: iv,
                        padding: CryptoJS.pad.Pkcs7
                    });
                    pwd2 = pwd2.toString(CryptoJS.enc.Utf8);
                    if (pwd2 != pw) {
                        $tip.text("CryptoJS密码解析失败");
                        dr_tips(0, "CryptoJS密码解析失败");
                        return;
                    }
                    <?php }?>
                    <?php } else {?>
                    psw = $.md5(psw); // 进行md5加密
                    <?php }?>
                    $psw.val(psw);
                }
                $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: "?m=admin&c=index&a=public_login_screenlock",
                    data: $("#lock-screen-form").serialize(),
                    success: function(json){
                        // token 更新
                        if (json.token) {
                            var token = json.token;
                            $("#lock-screen-form input[name='"+token.name+"']").val(token.value);
                        }
                        if (json.msg == '<?php echo L('admin_login');?>') {
                            setTimeout("window.location.reload(true)", 2000);
                        }
                        if (json.code == 1) {
                            $('#ew-lock-screen-group').css('display','none');
                            restForm();
                        } else {
                            $psw.val('');
                            $tip.text(json.msg);
                            $form.addClass('show-back');
                        }
                        dr_tips(json.code, json.msg);
                    }
                });
            }
        } else {
            $form.addClass('show-psw show-back');
            $psw.focus();
        }
    }

    // 重置
    function restForm() {
        $psw.blur();
        $psw.val('');
        $tip.text('');
        $form.removeClass('show-psw show-back');
    }
    
    $tool.on('click', function () {
        var tool = $tool.children().attr("data-url");
        dr_logout('<?php echo L('confirm_exit_login');?>', tool, '<?php echo SELF;?>');
    });
});
</script>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-logo layuimini-logo"></div>

        <div class="layuimini-header-content">
            <a>
                <div class="layuimini-tool"><i onmouseover="layer.tips('<?php echo L('spread_or_closed')?>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();" class="fa fa-outdent" data-side-fold="1"></i></div>
            </a>

            <!--电脑端头部菜单-->
            <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-menu-header-pc layuimini-pc-show">
            </ul>

            <!--手机端头部菜单-->
            <ul class="layui-nav layui-layout-left layuimini-header-menu layuimini-mobile-show">
                <li class="layui-nav-item">
                    <a href="javascript:;"><i class="fa fa-list-ul"></i></a>
                    <dl class="layui-nav-child layuimini-menu-header-mobile">
                    </dl>
                </li>
            </ul>

            <ul class="layui-nav layui-layout-right">
                <?php if (dr_count($sitelist) > 1 && is_array($sitelist)) {?>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-share="<?php echo L('多站')?>"><i class="fa fa-share-alt"></i></a>
                    <ul class="layui-nav-child">
                        <li>
                            <ul class="scroller" style="min-width: 160px;max-width: 275px;height:300px;overflow: scroll;" data-handle-color="#637283">
                                <?php foreach ($sitelist as $key=>$v) {?>
                                <dd>
                                    <a href="javascript:site_select(<?php echo $v['siteid']?>);" style="<?php if ($siteid==$v['siteid']) {echo 'color:red!important;';}?>border-bottom: 1px solid #EFF2F6!important;" data-title="<?php echo $v['name']?>" data-icon="fa fa-gears"><?php echo $v['name']?><?php if ($siteid==$v['siteid']) {echo '<span class="layui-badge-dot"></span>';}?></a>
                                </dd>
                                <?php }?>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php }?>
                <li class="layui-nav-item" lay-unselect>
                    <a <?php if (is_mobile()) {?>href="javascript:;"<?php } else {?>href="<?php echo $currentsite['domain'];?>" target="_blank"<?php }?> data-home="<?php echo L('site_homepage')?>"><i class="fa fa-home"></i></a>
                    <ul class="layui-nav-child">
                        <li>
                            <?php if (is_mobile()) {?>
                            <dd>
                                <a href="<?php echo $currentsite['domain'];?>" target="_blank" data-home="<?php echo L('site_homepage')?>"><i class="fa fa-user"></i> <?php echo L('site_homepage')?></a>
                            </dd>
                            <dd>
                                <hr>
                            </dd>
                            <?php }?>
                            <dd>
                                <a href="<?php echo WEB_PATH;?>index.php?m=member" target="_blank" data-member="<?php echo L('member_center')?>"><i class="fa fa-user"></i> <?php echo L('member_center')?></a>
                            </dd>
                            <dd>
                                <a href="<?php echo WEB_PATH;?>index.php?m=search" target="_blank" id="site_search" data-search="<?php echo L('search')?>"><i class="fa fa-search"></i> <?php echo L('站点').L('search')?></a>
                            </dd>
                        </li>
                    </ul>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-refresh="<?php echo L('刷新')?>"><i class="fa fa-refresh"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" data-clear="<?php echo L('update_backup')?>" class="layuimini-clear"><i class="fa fa-trash-o"></i></a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a href="javascript:;" onclick="lock_screen()" data-lock="<?php echo L('lockscreen')?>"><i class="fa fa-lock"></i></a>
                </li>
                <li class="layui-nav-item mobile layui-hide-xs" lay-unselect>
                    <a href="javascript:;" data-check-screen="full"><i class="fa fa-arrows-alt"></i></a>
                </li>
                <li class="layui-nav-item layuimini-setting">
                    <a href="javascript:;"><?php echo $admin_username;?></a>
                    <dl class="layui-nav-child">
                        <dd>
                            <?php $menu_data = $this->menu_db->get_one(array('name' => 'editinfo', 'm' => 'admin', 'c' => 'admin_manage', 'a' => 'public_edit_info'));?>
                            <a href="javascript:;" layuimini-content-href="?m=admin&c=admin_manage&a=public_edit_info&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="<?php echo L('基本资料');?>" data-icon="fa fa-user"> <i class="fa fa-user"></i> <?php echo L('基本资料');?><span class="layui-badge-dot"></span></a>
                        </dd>
                        <dd>
                            <?php $menu_data = $this->menu_db->get_one(array('name' => 'editpwd', 'm' => 'admin', 'c' => 'admin_manage', 'a' => 'public_edit_pwd'));?>
                            <a href="javascript:;" layuimini-content-href="?m=admin&c=admin_manage&a=public_edit_pwd&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="<?php echo L('修改密码');?>" data-icon="fa fa-unlock-alt"> <i class="fa fa-unlock-alt"></i> <?php echo L('修改密码');?></a>
                        </dd>
                        <dd>
                            <hr>
                        </dd>
                        <dd>
                            <a href="javascript:;" class="login-out"> <i class="fa fa-power-off"></i> <?php echo L('exit_login');?></a>
                        </dd>
                    </dl>
                </li>
                <li class="layui-nav-item layuimini-select-bgcolor" lay-unselect>
                    <a href="javascript:;" data-bgcolor="<?php echo L('配色方案');?>"><i class="fa fa-ellipsis-v"></i></a>
                </li>
            </ul>
        </div>
    </div>

    <!--无限极左侧菜单-->
    <div class="layui-side layui-bg-black layuimini-menu-left">
    </div>

    <!--初始化加载层-->
    <div class="layuimini-loader">
        <div class="layuimini-loader-inner"></div>
    </div>

    <!--手机端遮罩层-->
    <div class="layuimini-make"></div>

    <!-- 移动导航 -->
    <div class="layuimini-site-mobile"><i class="layui-icon"></i></div>

    <div class="layui-body">

        <div class="layuimini-tab layui-tab-rollTool layui-tab" lay-filter="layuiminiTab" lay-allowclose="true">
            <ul class="layui-tab-title">
                <li class="layui-this" id="layuiminiHomeTabId" lay-id=""></li>
            </ul>
            <div class="layui-tab-control">
                <li class="layuimini-tab-roll-left layui-icon layui-icon-left"></li>
                <li class="layuimini-tab-roll-right layui-icon layui-icon-right"></li>
                <li class="layui-tab-tool layui-icon layui-icon-down">
                    <ul class="layui-nav close-box">
                        <li class="layui-nav-item">
                            <a href="javascript:;"><span class="layui-nav-more"></span></a>
                            <dl class="layui-nav-child">
                                <dd><a href="javascript:;" layuimini-tab-close="current"><?php echo L('关 闭 当 前');?></a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="other"><?php echo L('关 闭 其 他');?></a></dd>
                                <dd><a href="javascript:;" layuimini-tab-close="all"><?php echo L('关 闭 全 部');?></a></dd>
                            </dl>
                        </li>
                    </ul>
                </li>
            </div>
            <div class="layui-tab-content">
                <div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show"></div>
                <div class="fav-nav">
                    <div id="panellist">
                        <?php foreach($adminpanel as $v) {?>
                                <span>
                                <a href="javascript:paneladdclass(this);" layuimini-content-href="<?php echo $v['url'].'&menuid='.$v['menuid'].'&pc_hash='.dr_get_csrf_token();?>" data-title="<?php echo L($v['name'])?>" data-icon="<?php echo $v['icon'];?>"><i class="<?php echo $v['icon'];?>"></i><cite><?php echo L($v['name'])?></cite></a>
                                <a class="panel-delete" href="javascript:delete_panel(<?php echo $v['menuid']?>, this);"></a></span>
                        <?php }?>
                    </div>
                    <div id="paneladd"></div>
                    <input type="hidden" id="menuid" value="">
                </div>
            </div>
        </div>

    </div>
</div>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
layui.use(['jquery', 'layer', 'miniAdmin'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniAdmin = layui.miniAdmin;

    var options = {
        iniUrl: "<?php echo SELF;?>?m=admin&c=index&a=public_menu",    // 初始化接口
        clearUrl: "<?php echo SELF;?>?m=admin&c=cache_all&a=init&pc_hash="+pc_hash, // 缓存清理接口
        urlHashLocation: true,      // 是否打开hash定位
        bgColorDefault: false,      // 主题默认配置
        multiModule: true,          // 是否开启多模块
        menuChildOpen: false,       // 是否默认展开菜单
        loadingTime: 0,             // 初始化加载时间
        pageAnim: true,             // iframe窗口动画
        maxTabNum: 20,              // 最大的tab打开数量
    };
    miniAdmin.render(options);

    $('.login-out').on("click", function () {
        dr_logout('<?php echo L('confirm_exit_login');?>', '?m=admin&c=index&a=public_logout', '<?php echo SELF;?>');
    });
});
function menu(menuid) {
    $("#menuid").val(menuid);
    $("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em><?php echo L('add')?></em></a>');
}
function add_panel() {
    var menuid = $("#menuid").val();
    $.ajax({
        type: "POST",
        dataType:"json",
        url: "?m=admin&c=index&a=public_ajax_add_panel",
        data: {'menuid': menuid, '<?php echo SYS_TOKEN_NAME;?>': csrf_hash},
        success: function(json){
            if(json.code == 1) {
                $("#panellist").html(json.data.jscode);
            }
            dr_tips(json.code, json.msg);
        }
    });
}
function delete_panel(menuid, id) {
    $.ajax({
        type: "POST",
        dataType:"json",
        url: "?m=admin&c=index&a=public_ajax_delete_panel",
        data: {'menuid': menuid, '<?php echo SYS_TOKEN_NAME;?>': csrf_hash},
        success: function(json){
            if(json.code == 1) {
                $("#panellist").html(json.data.jscode);
            }
            dr_tips(json.code, json.msg);
        }
    });
}
function paneladdclass(id) {
    $("#panellist span a[class='on']").removeClass();
    $(id).addClass('on')
}
<?php if (dr_count($sitelist) > 1 && is_array($sitelist)) {?>
//站点选择
function site_select(siteid) {
    Dialog.confirm('<?php echo L('你确定要切换到选中站点吗？');?>', function() {
        $.get("?m=admin&c=index&a=public_set_siteid&siteid="+siteid,function(data){
            if (data==1){
                location.reload(true);
            }
        });
    });
}
<?php }?>
//修改锁屏界面
function lock_screen() {
    $.get("?m=admin&c=index&a=public_lock_screen");
    $('#ew-lock-screen-group').css('display','');
    $('#lock_password').attr("placeholder","<?php echo L('setting_input_password');?>");
}
$(function(){
    <?php if($siteid!=1){?>
    $('#site_search').attr('href', '<?php echo WEB_PATH;?>index.php?m=search&siteid=<?php echo $siteid?>');
    <?php }?>
    var url = '<?php echo siteurl(1);?>';
    var p = url.split('/');
    var ptl = document.location.protocol;
    if ((p[0] == 'http:' || p[0] == 'https:') && ptl != p[0]) {
        Dialog.alert('当前访问是'+ptl.replace(':', '')+'模式，本项目设置的是'+p[0].replace(':', '')+'模式，请使用'+p[0].replace(':', '')+'模式访问，会导致部分功能无法正常使用');
    }
})
</script>
</body>
</html>