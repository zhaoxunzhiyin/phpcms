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
当前目录是：<?php echo TPLPATH;?><br>
例如，我把此目录移动到新目录为：/www/cms/template/<br>

1、把<?php echo TPLPATH;?>目录移动到服务器中的/www/cms/template/<br>
2、打开web目录中的<?php echo CMS_PATH;?>index.php<br>
3、在【执行主程序】之前加上下面的代码<br>
<pre>
// 此代码放到【执行主程序】代码之前<br>
define('TPLPATH', '/www/cms/template/');
</pre>
<br>4、赋予新目录（/www/cms/template/）可读写权限，如果网站正常访问就表示ok了
</div>
</div>
</div>
</div>
</body>
</html>