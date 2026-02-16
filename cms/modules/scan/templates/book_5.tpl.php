<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
hr, p {margin: 20px 0;}
</style>
<div  class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<p>
    <?php echo SYS_UPLOAD_PATH;?>目录是附件存放的目录，安全起见，强烈推荐进行分离
</p>
<p>
    1、将<?php echo SYS_UPLOAD_PATH;?>目录移动到指定目录，例如/www/file/uploadfile/
</p>
<p>
    2、再web服务器中为此目录绑定一个域名，例如：
</p>
<pre class="brush:html;toolbar:false">file.kaixin100.cn</pre>
<p>
    顶级域名二级域名都可以
</p>
<p>
    3、必须设置此网站不能执行php代码，以宝塔BT服务器为例的配置：
</p>
<p>
    <img src="<?php echo IMG_PATH;?>/admin_img/1.png"/>
</p>
<p>
    纯静态的目的是为了此目录下的不允许执行php文件，增强被非法写入的安全性
</p>
<p>
    4、进入cms后台，设置，附件设置
</p>
<p>
    <img src="<?php echo IMG_PATH;?>/admin_img/2.png"/>
</p>
<p>
    5、保存再更新缓存后，测试上传附件试试是否正常
</p>
<p>
    <br/>
</p>
</div>
</div>
</div>
</div>
</body>
</html>