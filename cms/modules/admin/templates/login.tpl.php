<?php defined('IN_ADMIN') or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo L('logon')?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<link href="<?php echo CSS_PATH?>admin/css/login.css" rel="stylesheet" type="text/css" />
<link href="<?php echo CSS_PATH?>font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>styleswitch.js"></script>
<script src="<?php echo JS_PATH?>jquery.backstretch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo JS_PATH?>layer/layer.js"></script>
<script src="<?php echo JS_PATH?>jquery.md5.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH?>jquery.particleground.min.js" type="text/javascript"></script>
</head>
<body>
<div class="container login">
    <form class="layui-form layui-form-pane" action="?m=admin&c=index&a=<?php echo SYS_ADMIN_PATH;?>&dosubmit=1" method="post" id="myform" name="myform" onsubmit="return login()">
        <div id="content" class="content">
            <div id="large-header" class="large-header">
                <div id="canvas"></div>
                <div class="main-title">
                    <div class="beg-login-box">
                        <header>
                            <h1>站点后台管理系统</h1>
                            <em>Management System</em>
                        </header>
                        <div class="beg-login-main">
                            <form class="layui-form layui-form-pane" method="post">
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-username"></span>
                                    </label>
                                    <input type="text" id="username" name="username" placeholder="账号" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <i class="layui-icon layui-icon-password"></i>
                                    </label>
                                    <input type="password" id="password" name="password" placeholder="密码" autocomplete="off" class="layui-input">
                                    <span class="bind-password icon icon-4"></span>
                                </div>
                                <?php if (!$sysadmincode) {?>
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-vercode"></span>
                                    </label>
                                    <input type="text" id="captcha" name="code" placeholder="验证码" autocomplete="off" maxlength="4" class="layui-input">
                                    <div class="captcha">
                                        <?php echo form::checkcode('code_img')?>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="layui-form-item">
                                    <button type="submit" class="layui-btn btn-submit btn-blog">立即登陆</button>
                                </div>
                            </form>
                        </div>
                        <footer>
                            <p>&copy;&nbsp;2006-<script language="javaScript">document.write(new Date().getFullYear());</script>&nbsp;Kaixin100&nbsp;<span>www.kaixin100.cn</span>&nbsp;<?php echo pc_base::load_config('version','cms_version');?></p>
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
$(document).ready(function() {
    if(self.parent.frames.length!=0){
        self.parent.location=document.location.href;
    }
    if(document.myform.username.value == '') {
        document.myform.username.focus();
    } else {
        document.myform.username.select();
    }
    $('#canvas').particleground({
        dotColor: 'rgba(255,255,255,0.2)',
        lineColor: 'rgba(255,255,255,0.2)'
    });
    $('#large-header').backstretch([
        "<?php echo IMG_PATH?>admin_img/bg-screen1.jpg","<?php echo IMG_PATH?>admin_img/bg-screen2.jpg","<?php echo IMG_PATH?>admin_img/bg-screen3.jpg","<?php echo IMG_PATH?>admin_img/bg-screen4.jpg","<?php echo IMG_PATH?>admin_img/bg-screen5.jpg","<?php echo IMG_PATH?>admin_img/bg-screen6.jpg","<?php echo IMG_PATH?>admin_img/bg-screen7.jpg"], {
        fade: 1000,
        duration: 8000
    });
    $('.bind-password').on('click', function () {
        if ($(this).hasClass('icon-5')) {
            $(this).removeClass('icon-5');
            $("input[name='password']").attr('type', 'password');
        } else {
            $(this).addClass('icon-5');
            $("input[name='password']").attr('type', 'text');
        }
    });
});
</script>
<script>
//监听提交
function login() {
    if (!$('#username').val()){
        layer.msg('账号不能为空！', {icon: 5, anim: 6, time: 1000});
        $('#username').focus();
        return false;
    }
    if (!$('#password').val()){
        layer.msg('密码不能为空！', {icon: 5, anim: 6, time: 1000});
        $('#password').focus();
        return false;
    }
    <?php if (!$sysadmincode) {?>
    if (!$('#captcha').val()){
        layer.msg('验证码不能为空！', {icon: 5, anim: 6, time: 1000});
        $('#captcha').focus();
        return false;
    }
    <?php }?>
    loading = layer.load(1, {shade: [0.1,'#fff'] });//0.1透明度的白色背景
    // 这里进行md5加密存储
    var pwd = $('#password').val();
    pwd = $.md5(pwd); // 进行md5加密
    $('#password').val(pwd);
    $.ajax({
        type: 'post',
        url: '?m=admin&c=index&a=<?php echo SYS_ADMIN_PATH;?>&dosubmit=1',
        data: $("#myform").serialize(),
        dataType: 'json',
        success: function(res) {
            layer.close(loading);
            if(res.code == 1){
                layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                    location.href = res.data.url;
                });
            /*}else if(res.code == 2){
                $('#username').val('');
                $('#username').focus();
                layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                <?php if (!$sysadmincode) {?>
                $('#captcha').val('');
                $('#code_img').trigger('click');
                <?php }?>
            }else if(res.code == 3){
                $('#password').val('');
                $('#password').focus();
                layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                <?php if (!$sysadmincode) {?>
                $('#captcha').val('');
                $('#code_img').trigger('click');
                <?php }?>*/
            <?php if (!$sysadmincode) {?>
            /*}else if(res.code == 4){
                $('#captcha').focus();
                $('#captcha').val('');
                layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                $('#code_img').trigger('click');*/
            <?php }?>
            }else{
                layer.msg(res.msg, {icon: 2, anim: 6, time: 1000});
                <?php if (!$sysadmincode) {?>
                $('#captcha').val('');
                $('#code_img').trigger('click');
                <?php }?>
            }
        }
    });
    return false;
}
</script>
</body>
</html>