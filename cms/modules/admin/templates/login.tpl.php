<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo CHARSET;?>">
<title><?php echo L('logon')?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(JS_PATH.'layui/css/layui.css');?>
<?php echo load_css(CSS_PATH.'admin/css/login.css');?>
<?php echo load_css(CSS_PATH.'admin/css/my.css');?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(JS_PATH.'layer/layer.js');?>
<?php echo load_js(JS_PATH.'sweetalert/sweetalert.min.js');?>
<?php if ($admin_login_aes) {?>
<?php echo load_js(JS_PATH.'crypto-js.min.js');?>
<?php } else {?>
<?php echo load_js(JS_PATH.'jquery.md5.js');?>
<?php }?>
<?php echo load_js(JS_PATH.'jquery.backstretch.min.js');?>
<?php echo load_js(JS_PATH.'jquery.particleground.min.js');?>
</head>
<body>
<div class="container login">
    <form class="layui-form layui-form-pane" method="post" onsubmit="return dr_submit()" id="kt_sign_in_form">
        <?php echo dr_form_hidden();?>
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
                                <?php if ($is_sms) {?>
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-username"></span>
                                    </label>
                                    <input type="text" id="phone" name="data[phone]" placeholder="<?php echo L('手机');?>" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-password"></span>
                                    </label>
                                    <input type="text" id="sms" name="data[sms]" placeholder="<?php echo L('手机验证码');?>" autocomplete="off" class="layui-input">
                                    <div class="captcha">
                                        <button type="button" onclick="dr_send_sms()" class="layui-btn layui-btn-default"><?php echo L('获取验证码');?></button>
                                    </div>
                                </div>
                                <?php } else {?>
                                <div class="layui-form-item dr_row_username">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-username"></span>
                                    </label>
                                    <input type="text" id="username" name="data[username]" placeholder="<?php echo L('账号');?>" autocomplete="off" class="layui-input">
                                </div>
                                <div class="layui-form-item dr_row_password">
                                    <label class="beg-login-icon fs1">
                                        <i class="layui-icon layui-icon-password"></i>
                                    </label>
                                    <input type="password" id="password" name="data[password]" placeholder="<?php echo L('密码');?>" autocomplete="off" class="layui-input">
                                    <span class="bind-password icon"><svg focusable="false" data-icon="eye-invisible" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M942.2 486.2Q889.47 375.11 816.7 305l-50.88 50.88C807.31 395.53 843.45 447.4 874.7 512 791.5 684.2 673.4 766 512 766q-72.67 0-133.87-22.38L323 798.75Q408 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 000-51.5zm-63.57-320.64L836 122.88a8 8 0 00-11.32 0L715.31 232.2Q624.86 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 000 51.5q56.69 119.4 136.5 191.41L112.48 835a8 8 0 000 11.31L155.17 889a8 8 0 0011.31 0l712.15-712.12a8 8 0 000-11.32zM149.3 512C232.6 339.8 350.7 258 512 258c54.54 0 104.13 9.36 149.12 28.39l-70.3 70.3a176 176 0 00-238.13 238.13l-83.42 83.42C223.1 637.49 183.3 582.28 149.3 512zm246.7 0a112.11 112.11 0 01146.2-106.69L401.31 546.2A112 112 0 01396 512z"></path><path d="M508 624c-3.46 0-6.87-.16-10.25-.47l-52.82 52.82a176.09 176.09 0 00227.42-227.42l-52.82 52.82c.31 3.38.47 6.79.47 10.25a111.94 111.94 0 01-112 112z"></path></svg></span>
                                </div>
                                <?php }?>
                                <div class="layui-form-item dr_check dr_check_phone" style="display: none"></div>
                                <div class="layui-form-item dr_check" style="display: none">
                                    <?php if (!$is_sms && $admin_sms_check) {?>
                                    <input type="hidden" name="data[phone]" id="phone">
                                    <?php }?>
                                    <input type="hidden" name="data[is_check]" id="is_check">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-password"></span>
                                    </label>
                                    <?php if (!$is_sms && $admin_sms_check) {?>
                                    <input type="text" id="sms" name="data[sms]" placeholder="<?php echo L('手机验证码');?>" autocomplete="off" class="layui-input">
                                    <?php }?>
                                    <div class="captcha">
                                        <button type="button" onclick="dr_send_sms()" class="layui-btn layui-btn-default"><?php echo L('获取验证码');?></button>
                                    </div>
                                </div>
                                <?php if (!$sysadmincode) {?>
                                <div class="layui-form-item">
                                    <label class="beg-login-icon fs1">
                                        <span class="layui-icon layui-icon-vercode"></span>
                                    </label>
                                    <input type="text" id="captcha" name="code" placeholder="<?php echo L('验证码');?>" autocomplete="off" maxlength="<?php echo $setting['sysadmincodelen'];?>" class="layui-input">
                                    <div class="captcha">
                                        <?php echo form::checkcode('code_img', $setting['sysadmincodelen']);?>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="layui-form-item">
                                    <button type="button" id="kt_sign_in_submit" class="layui-btn layui-btn-fluid">
                                        <span class="indicator-label"><?php echo L('立即登录');?></span>
                                        <span class="indicator-progress"><?php echo L('请求中...');?><span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                </div>
                                <?php if ($admin_sms_login) {?>
                                <div class="layui-form-item text-right">
                                    <?php if ($is_sms) {?>
                                    <a href="<?php echo SELF;?>"> <?php echo L('账号密码登录');?> </a>
                                    <?php } else {?>
                                    <a href="<?php echo SELF;?>?is_sms=1"> <?php echo L('手机验证码登录');?> </a>
                                    <?php }?>
                                </div>
                                <?php }?>
                            </form>
                        </div>
                        <footer>
                            <p>&copy;&nbsp;2006-<script type="text/javascript">document.write(new Date().getFullYear());</script>&nbsp;Kaixin100&nbsp;<span>www.kaixin100.cn</span>&nbsp;<?php echo pc_base::load_config('version','cms_version');?></p>
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
    <?php if ($is_sms) {?>
    if($('#phone').value == '') {
        $('#phone').focus();
    } else {
        $('#phone').select();
    }
    <?php } else {?>
    if($('#username').value == '') {
        $('#username').focus();
    } else {
        $('#username').select();
    }
    <?php }?>
    $('body').keydown(function(e){
        if (e.keyCode == 13) {
            dr_submit();
        }
    });
    $('#canvas').particleground({
        dotColor: 'rgba(255,255,255,0.2)',
        lineColor: 'rgba(255,255,255,0.2)'
    });
    $('#large-header').backstretch([<?php echo implode(',', $background);?>], {
        fade: 1000,
        duration: 8000
    });
    $('.bind-password').on('click', function () {
        if ($('#password').attr('type')=='text') {
            $(this).html('<svg focusable="false" data-icon="eye-invisible" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M942.2 486.2Q889.47 375.11 816.7 305l-50.88 50.88C807.31 395.53 843.45 447.4 874.7 512 791.5 684.2 673.4 766 512 766q-72.67 0-133.87-22.38L323 798.75Q408 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 000-51.5zm-63.57-320.64L836 122.88a8 8 0 00-11.32 0L715.31 232.2Q624.86 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 000 51.5q56.69 119.4 136.5 191.41L112.48 835a8 8 0 000 11.31L155.17 889a8 8 0 0011.31 0l712.15-712.12a8 8 0 000-11.32zM149.3 512C232.6 339.8 350.7 258 512 258c54.54 0 104.13 9.36 149.12 28.39l-70.3 70.3a176 176 0 00-238.13 238.13l-83.42 83.42C223.1 637.49 183.3 582.28 149.3 512zm246.7 0a112.11 112.11 0 01146.2-106.69L401.31 546.2A112 112 0 01396 512z"></path><path d="M508 624c-3.46 0-6.87-.16-10.25-.47l-52.82 52.82a176.09 176.09 0 00227.42-227.42l-52.82 52.82c.31 3.38.47 6.79.47 10.25a111.94 111.94 0 01-112 112z"></path></svg>');
            $('#password').attr('type', 'password');
        } else {
            $(this).html('<svg focusable="false" data-icon="eye" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="64 64 896 896"><path d="M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 000 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"></path></svg>');
            $('#password').attr('type', 'text');
        }
    });
});
if (typeof parent.layer == 'function') {
    parent.layer.closeAll('loading');
}
function dr_send_sms() {
    $.ajax({type: "POST",dataType:"json", url: '?m=admin&c=index&a=sms', data: $("#kt_sign_in_form").serialize(),
        success: function(json) {
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#kt_sign_in_form input[name='"+token.name+"']").val(token.value);
            }
            if (json.code == 1) {
                layer.msg('<i class="fa fa-check-circle"></i>&nbsp;&nbsp;'+json.msg, {time: 3000});
            } else {
                <?php if (!$sysadmincode) {?>
                $('#code_img').trigger('click');
                <?php }?>
                Swal.fire({
                    text: json.msg,
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                });
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            <?php if (!$sysadmincode) {?>
            $('#code_img').trigger('click');
            <?php }?>
            var msg = HttpRequest.responseText;
            if (!msg) {
                Swal.fire({
                    text: "<?php echo L('系统故障');?>",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                });
            } else {
                Swal.fire({
                    text: msg,
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                });
            }
        }
    });
}
$('body').on('click','#kt_sign_in_submit',function() {
    var e = $(this);
    e.attr("data-kt-indicator", "on");
    e.disabled = true;
    <?php if ($is_sms) {?>
    <?php } else {?>
    // 这里进行md5加密存储
    var pwd = $('#password').val();
    if (pwd.length == 32) {
        // 已经加密过的
    } else {
        <?php if ($admin_login_aes) {?>
        $('#kt_sign_in_form').append('<input type="hidden" name="is_aes" value="1">');
        var key = CryptoJS.enc.Utf8.parse('<?php echo substr(md5(SYS_KEY), 0, 16);?>');
        var iv = CryptoJS.enc.Utf8.parse('<?php echo substr(md5(SYS_KEY), 10, 16);?>');
        var pw = pwd;
        pwd = CryptoJS.AES.encrypt(pwd, key, {
            mode: CryptoJS.mode.CBC,
            iv: iv,
            padding: CryptoJS.pad.Pkcs7
        });
        <?php if (IS_DEV) {?>
        pwd2 = CryptoJS.AES.decrypt(pwd, key, {
            mode: CryptoJS.mode.CBC,
            iv: iv,
            padding: CryptoJS.pad.Pkcs7
        });
        pwd2 = pwd2.toString(CryptoJS.enc.Utf8);
        if (pwd2 != pw) {
            Swal.fire({
                text: "CryptoJS密码解析失败",
                icon: "error",
                buttonsStyling: !1,
                confirmButtonText: "<?php echo L('返回');?>",
                customClass: {
                    confirmButton: "btn btn-light"
                }
            });
            e.removeAttr("data-kt-indicator");
            e.disabled = false;
            return;
        }
        <?php }?>
        <?php } else {?>
        pwd = $.md5(pwd); // 进行md5加密
        <?php }?>
        $('#password').val(pwd);
    }
    <?php }?>
    $.ajax({type: "POST",dataType:"json", url: '?m=admin&c=index&a=<?php echo SYS_ADMIN_PATH.($is_sms ? '&is_sms=1' : '');?>', data: $("#kt_sign_in_form").serialize(),
        success: function(json) {
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#kt_sign_in_form input[name='"+token.name+"']").val(token.value);
            }
            if (json.code == 9) {
                // 二次验证
                $(".dr_row_username").hide();
                $(".dr_row_password").hide();
                $(".dr_check").show();
                $(".dr_check_phone").html(json.msg);
                $("#phone").val(json.data);
                $("#is_check").val("yes");
                <?php if (!$sysadmincode) {?>
                $('#code_img').trigger('click');
                <?php }?>
            } else if (json.code == 1) {
                layer.msg('<i class="fa fa-check-circle"></i>&nbsp;&nbsp;'+json.msg, {time: 1000}, function(){
                    window.location.href = json.data.url;
                });
            } else {
                <?php if ($admin_login_aes) {?>
                $('#password').val("");
                <?php }?>
                <?php if (!$sysadmincode) {?>
                $('#code_img').trigger('click');
                <?php }?>
                Swal.fire({
                    text: json.msg,
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-light"
                    }
                });
            }
            e.removeAttr("data-kt-indicator");
            e.disabled = false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            e.removeAttr("data-kt-indicator");
            e.disabled = false;
            <?php if (!$sysadmincode) {?>
            $('#code_img').trigger('click');
            <?php }?>
            var msg = HttpRequest.responseText;
            if (!msg) {
                Swal.fire({
                    text: "<?php echo L('系统故障');?>",
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            } else {
                Swal.fire({
                    text: msg,
                    icon: "error",
                    buttonsStyling: !1,
                    confirmButtonText: "<?php echo L('返回');?>",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            }
        }
    });
});
function dr_submit() {
    $("#kt_sign_in_submit").click();
}
</script>
</body>
</html>