<?php include CMS_PATH.'install/step/header.tpl.php';?>
<script type="text/javascript">
    $(document).ready(function() {
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#username").formValidator({onshow:"2到20个字符，不含非法字符！",onfocus:"请输入用户名3至20位"}).inputValidator({min:3,max:20,onerror:"用户名长度应为3至20位"})
        $("#password").formValidator({onshow:"6到20个字符<font color='FFFF00'>（默认为 admin888）</font>",onfocus:"密码合法长度为6至20位"}).inputValidator({min:6,max:20,onerror:"密码合法长度为6至20位"});
        $("#pwdconfirm").formValidator({onshow:"请再次输入密码",onfocus:"请输入确认密码",oncorrect:"两次密码相同"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不同"});
        $("#email").formValidator({onshow:"请输入email",onfocus:"请输入email",oncorrect:"email格式正确"}).regexValidator({regexp:"email",datatype:"enum",onerror:"email格式错误"})
        $("#dbhost").formValidator({onshow:"数据库服务器地址, 一般为 localhost",onfocus:"数据库服务器地址, 一般为 localhost",oncorrect:"数据库服务器地址正确",empty:false}).inputValidator({min:1,onerror:"数据库服务器地址不能为空"});
    })
</script>
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
                        <form id="myform" action="<?php echo SELF;?>" method="post">
                            <input type="hidden" name="step" value="5">
                            <input type="hidden" name="data[dbcharset]" value="utf8mb4">
                            <input type="hidden" name="data[pconnect]" value="0">
                            <fieldset>
                                <legend>填写数据库信息</legend>
                                <div class="content">
                                    <table width="100%" cellspacing="1" cellpadding="0" >
                                        <tr>
                                            <th width="20%" align="right">数据库主机：</th>
                                            <td><input name="data[dbhost]" type="text" id="dbhost" value="<?php echo $hostname?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库端口：</th>
                                            <td><input name="data[dbport]" type="text" id="dbport" value="<?php echo $port?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库帐号：</th>
                                            <td><input name="data[dbuser]" type="text" id="dbuser" value="<?php echo $username?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库密码：</th>
                                            <td><input name="data[dbpw]" type="password" id="dbpw" value="<?php echo $password?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库名称：</th>
                                            <td><input name="data[dbname]" type="text" id="dbname" value="<?php echo $database?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据表前缀：</th>
                                            <td><input name="data[tablepre]" type="text" id="tablepre" value="<?php echo $tablepre?>" class="input-text" />  <img src="./images/help.png" style="cursor:pointer;" onmouseover="layer.tips('如果一个数据库安装多个cms，请修改表前缀',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" align="absmiddle" /><span id='helptablepre'></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>填写网站信息</legend>
                                <div class="content">
                                    <table width="100%" cellspacing="1" cellpadding="0">
                                        <tr>
                                            <th width="20%" align="right">网站地址：</th>
                                            <td><?php echo FC_NOW_HOST.substr($rootpath, 1);?></td>
                                        </tr>
                                        <tr>
                                            <th align="right">网站名称：</th>
                                            <td><input name="data[name]" type="text" id="name" value="CMS演示站" class="input-text" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>填写帐号信息</legend>
                                <div class="content">
                                    <table width="100%" cellspacing="1" cellpadding="0">
                                        <tr>
                                            <th width="20%" align="right">后台登录口地址：</th>
                                            <td><input name="data[adminpath]" id="adminpath" type="text" placeholder="设置后台登录地址" value="" class="input-text" /><button class="btn btn-sm blue" type="button" name="button" onclick="to_key()"> 自动生成 </button><br>后台登录地址设置同文件夹命名规则，可为空，为空不更改后台地址，例如:admin 安装完成后后台登录地址即为 <?php echo FC_NOW_HOST.substr($rootpath, 1);?>admin</td>
                                        </tr>
                                        <tr>
                                            <th align="right">超级管理员帐号：</th>
                                            <td><input name="data[username]" type="text" id="username" value="admin" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">管理员密码：</th>
                                            <td><input name="data[password]" type="password" id="password" value="admin888" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">确认密码：</th>
                                            <td><input name="data[pwdconfirm]" type="password" id="pwdconfirm" value="admin888" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">管理员E-mail：</th>
                                            <td><input name="data[email]" type="text" id="email" value="zhaoxunzhiyin@163.com" class="input-text" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </fieldset>
                        </form>
                   </div>
               </div>
            </div>
        </div>
        <div class="btn_box"><a href="javascript:void(0);" onClick="checkdb();return false;" class="btn btn-success">下一步安装</a></div>
    </div>
</div>
<form id="install" action="<?php echo SELF;?>" method="post">
<input type="hidden" name="step" value="5">
</form>
</body>
</html>
<script language="javascript">
function checkdb() {
    if($('#dbhost').val()==''){
        Dialog.alert('数据库主机不能为空！',function(){$('#dbhost').focus();})
        return false;
    }
    if($('#dbport').val()==''){
        Dialog.alert('数据库端口不能为空！',function(){$('#dbport').focus();})
        return false;
    }
    if($('#dbuser').val()==''){
        Dialog.alert('数据库帐号不能为空！',function(){$('#dbuser').focus();})
        return false;
    }
    if($('#dbpw').val()==''){
        Dialog.alert('数据库密码不能为空！',function(){$('#dbpw').focus();})
        return false;
    }
    if($('#dbname').val()==''){
        Dialog.alert('数据库名称不能为空！',function(){$('#dbname').focus();})
        return false;
    }
    if($('#tablepre').val()==''){
        Dialog.alert('数据表前缀不能为空！',function(){$('#tablepre').focus();})
        return false;
    }
    if($('#name').val()==''){
        Dialog.alert('网站名称不能为空！',function(){$('#name').focus();})
        return false;
    }
    if($('#username').val()==''){
        Dialog.alert('超级管理员帐号不能为空！',function(){$('#username').focus();})
        return false;
    }else{
        if($('#username').val().length < 3 || $('#username').val().length > 20){
            Dialog.alert('超级管理员帐号长度应为3至20位！',function(){$('#username').focus();})
            return false;
        }
    }
    if($('#password').val()==''){
        Dialog.alert('管理员密码不能为空！',function(){$('#password').focus();})
        return false;
    }else{
        if($('#password').val().length < 6 || $('#password').val().length > 20){
            Dialog.alert('管理员密码长度应为6至20位！',function(){$('#password').focus();})
            return false;
        }
    }
    if($('#pwdconfirm').val()==''){
        Dialog.alert('确认密码不能为空！',function(){$('#pwdconfirm').focus();})
        return false;
    }
    if($('#password').val()!=$('#pwdconfirm').val()){
        Dialog.alert('两次密码输入不同请重新输入！',function(){$('#password').focus();})
        return false;
    }
    if($('#email').val()==''){
        Dialog.alert('管理员E-mail不能为空！',function(){$('#email').focus();})
        return false;
    }else{
        if(!/^([a-zA-Z]|[0-9])(\w|\-)+@[a-zA-Z0-9]+\.([a-zA-Z]{2,4})$/.test($('#email').val())){
            Dialog.alert('email格式错误！',function(){$('#email').focus();})
            return false;
        }
    }
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 100000000
    });
    $("#myform input[name='step']").val('dbtest');
    $.ajax({
        type: "POST",
        dataType:"json",
        url: '<?php echo SELF;?>',
        data: $("#myform").serialize(),
        success: function(json){
            if(json.code == 1) {
                $('#install').submit();
            } else if(json.code == 2) {
                layer.close(loading);
                $("#myform input[name='step']").val('dbdel');
                Dialog.confirm(json.msg,function() {
                    $.ajax({
                        type: "POST",
                        dataType:"json",
                        url: '<?php echo SELF;?>',
                        data: $("#myform").serialize(),
                        success: function(json){
                            if(json.code == 1) {
                                var loading = layer.load(2, {
                                    shade: [0.3,'#fff'], //0.1透明度的白色背景
                                    time: 100000000
                                });
                                $('#install').submit();
                            } else {
                                Dialog.alert(json.msg);
                                return false;
                            }
                        }
                    });
                });
            } else {
                layer.close(loading);
                Dialog.alert(json.msg);
                return false;
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            layer.closeAll('loading');
            Dialog.alert('无法连接到数据库，检查数据库是否启动或者数据库配置不对');
        }
    });
    return false;
}
function to_key() {
    $.ajax({
        type: "GET",
        dataType:"json",
        url: '<?php echo SELF;?>',
        data: 'step=alpha',
        success: function(json){
            if(json.code == 1) {
                $('#adminpath').val(json.msg);
            } else {
                Dialog.alert(json.msg);
                return false;
            }
        }
    });
}
</script>