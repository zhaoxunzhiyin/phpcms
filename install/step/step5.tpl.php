<?php include CMS_PATH.'install/step/header.tpl.php';?>
<script type="text/javascript">
    $(document).ready(function() {
        $.formValidator.initConfig({formid:"install",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
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
                    <li class="ma1 on">准备安装</li>
                    <li class="ma2 on">检查环境</li>
                    <li class="ma3 on">模块选择</li>
                    <li class="ma4 on">权限检测</li>
                    <li class="ma5 on">配置信息</li>
                    <li class="ma6">开始安装</li>
                    <li class="ma7">安装完成</li>
                </ul>
            </div>
            <div class="bz a5"><div class="jj_bg"></div></div>
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
                        <form id="install" name="myform" action="<?php echo SELF;?>" method="post">    
                            <input type="hidden" name="step" value="6">    
                            <fieldset>
                                <legend>填写数据库信息</legend>
                                <div class="content">
                                    <table width="100%" cellspacing="1" cellpadding="0" >
                                        <tr>
                                            <th width="20%" align="right">数据库主机：</th>
                                            <td><input name="dbhost" type="text" id="dbhost" value="<?php echo $hostname?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库端口：</th>
                                            <td><input name="dbport" type="text" id="dbport" value="<?php echo $port?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库帐号：</th>
                                            <td><input name="dbuser" type="text" id="dbuser" value="<?php echo $username?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库密码：</th>
                                            <td><input name="dbpw" type="password" id="dbpw" value="<?php echo $password?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库名称：</th>
                                            <td><input name="dbname" type="text" id="dbname" value="<?php echo $database?>" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据表前缀：</th>
                                            <td><input name="tablepre" type="text" id="tablepre" value="<?php echo $tablepre?>" class="input-text" />  <img src="./images/help.png" style="cursor:pointer;" onmouseover="layer.tips('如果一个数据库安装多个cms，请修改表前缀',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" align="absmiddle" /><span id='helptablepre'></span></td>
                                        </tr>
                                        <tr>
                                            <th align="right">数据库字符集：</th>
                                            <td><div class="mt-radio-inline">
                                                <label class="mt-radio mt-radio-outline"><input name="dbcharset" type="radio" id="dbcharset" value="utf8mb4" <?php if(strtolower($charset)=='utf8mb4') echo '  checked="checked" '?> <?php if(strtolower($charset)=='gbk') echo 'disabled'?>/> utf8mb4 <span></span></label>
                                                <img src="./images/help.png" style="cursor:pointer;" onmouseover="layer.tips('如果Mysql版本为4.0.x，则请选择默认；如果Mysql版本为4.1.x或以上，则请选择其他字符集（一般选GBK）',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" align="absmiddle" />
                                                <span id='helpdbcharset'></span></div></td>
                                        </tr>
                                        <tr>
                                            <th align="right">启用持久连接：</th>
                                            <td><div class="mt-radio-inline">
                                                <label class="mt-radio mt-radio-outline"><input name="pconnect" type="radio" id="pconnect" value="1" <?php if($pconnect==1) echo ' checked="checked" '?>/> 是 <span></span></label>
                                                <label class="mt-radio mt-radio-outline"><input name="pconnect" type="radio" id="pconnect" value="0" <?php if($pconnect==0) echo ' checked="checked" '?>/> 否 <span></span></label>
                                                <img src="./images/help.png" style="cursor:pointer;" onmouseover="layer.tips('如果启用持久连接，则数据库连接上后不释放，保存一直连接状态；如果不启用，则每次请求都会重新连接数据库，使用完自动关闭连接。',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" align="absmiddle" /><span id='helppconnect'></span>
                                                <span id='helptablepre'></span></div></td>
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
                                            <td><?php echo $siteurl;?></td>
                                        </tr>
                                        <tr>
                                            <th align="right">网站名称：</th>
                                            <td><input name="name" type="text" id="name" value="CMS演示站" class="input-text" /></td>
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
                                            <td><input name="adminpath" id="adminpath" type="text" placeholder="设置后台登录地址" value="" class="input-text" /><button class="btn btn-sm blue" type="button" name="button" onclick="to_key()"> 自动生成 </button><br>后台登录地址设置同文件夹命名规则，可为空，为空不更改后台地址，例如:admin 安装完成后后台登录地址即为 <?php echo $siteurl?>admin</td>
                                        </tr>
                                        <tr>
                                            <th align="right">超级管理员帐号：</th>
                                            <td><input name="username" type="text" id="username" value="admin" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">管理员密码：</th>
                                            <td><input name="password" type="password" id="password" value="admin888" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">确认密码：</th>
                                            <td><input name="pwdconfirm" type="password" id="pwdconfirm" value="admin888" class="input-text" /></td>
                                        </tr>
                                        <tr>
                                            <th align="right">管理员E-mail：</th>
                                            <td><input name="email" type="text" id="email" value="zhaoxunzhiyin@163.com" class="input-text" />
                                                <input type="hidden" name="selectmod" value="<?php echo $selectmod?>" /></td>
                                        </tr>
                                    </table>
                                </div>
                            </fieldset>
                        </form>
                   </div>
               </div>
            </div>
            <div class="bg_b"></div>
        </div>
        <div class="btn_box"><a href="javascript:history.go(-1);" class="s_btn pre">上一步</a><a href="javascript:void(0);"  onClick="checkdb();return false;" class="x_btn">下一步</a></div>
    </div>
</div>
</body>
</html>
<script language="JavaScript">
<!--
var errmsg = new Array();
errmsg[0] = '您已经安装过CMS，系统会自动删除老数据！是否继续？';
errmsg[2] = '无法连接数据库服务器，请检查配置！';
errmsg[3] = '成功连接数据库，但是指定的数据库不存在并且无法自动创建，请先通过其他方式建立数据库！';
errmsg[6] = '数据库版本低于Mysql 4.0，无法安装CMS，请升级数据库版本！';
errmsg[7] = '后台登录口地址不能是数字开头或不能包含中文和特殊字符！';
errmsg[8] = '后台登录口地址不能使用CMS默认目录名（admin，api，caches，cms，login，html，mobile，statics，uploadfile），请重新设置！';
function checkdb() {
    if($('#dbhost').val()==''){
        Dialog.alert('数据库主机不能为空！',function(){$('#dbhost').focus();})
        return false;
    }
    if($('#dbport').val()==''){
        Dialog.alert('数据库端口不能为空！',function(){$('#dbport').focus();})
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
    $.ajax({
        type: "POST",
        url: '<?php echo SELF;?>',
        data: 'step=dbtest&adminpath='+$('#adminpath').val()+'&dbhost='+$('#dbhost').val()+'&dbport='+$('#dbport').val()+'&dbuser='+$('#dbuser').val()+'&dbpw='+$('#dbpw').val()+'&dbname='+$('#dbname').val()+'&tablepre='+$('#tablepre').val()+'&sid='+Math.random()*5,
        success: function(data){
            if(data > 1) {
                Dialog.alert(errmsg[data]);
                return false;
            } else if(data == 0) {
                Dialog.confirm(errmsg[0],function() {
                    $('#install').submit();
                });
            } else {
                $('#install').submit();
            }
        }
    });
    return false;
}
function to_key() {
	$.get('<?php echo SELF;?>?step=alpha', function(data){
		$('#adminpath').val(data);
	});
}
//-->
</script>