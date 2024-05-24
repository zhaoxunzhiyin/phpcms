<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p>服务器的相关配置，最好是联系服务商专业的技术为您配置环境</p>
</div>

<p>php.ini文件位置不固定，每个主机的目录不一样，需要咨询服务商此文件的位置</p>



post_max_size
<p>
表单提交最大数值,此项不是限制上传单个文件的大小,而是针对整个表单的提交数据进行限制的
默认为8M，设置为自己需要的值，此参数建议要设置比upload_max_filesize大一些
</p>


upload_max_filesize
<p>
允许上传文件大小的最大值，默认为2M，设置为自己需要的值此参数建议不要超过post_max_size值，因为它受控于post_max_size值（就算upload_max_filesize设置了1G，而post_max_size只设置了2M时，大于2M的文件照样传不上去，因为它受控于post_max_size值）
</p>


max_input_vars
<p>
用来限制提交的表单数量，默认值为 1000， 如果你网站栏目太多的话，而且需要配置用户权限的时候会发现无法保存，这时候说明这个值太小了，设置10000一般够用。
</p>


max_execution_time
<p>
每个PHP页面运行的最大时间值(秒)，默认30秒
</p>

max_input_time
<p>
每个PHP页面接收数据所需的最大时间，默认60秒
</p>

memory_limit
<p>
每个PHP页面所需要的最大内存，默认8M
</p>

allow_url_fopen
<p>
使用QQ登录、微信、微博快捷登录、在线支付、下载远程图片等功能时必须开启allow_url_fopen，设置为 allow_url_fopen = On。
</p>
</div>
</div>
</div>
</body>
</html>