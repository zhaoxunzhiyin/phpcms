<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="container">
    <h2>百度主动推送</h2>
    <div class="content-text">
        <p>接口地址：<a href="https://ziyuan.baidu.com/linksubmit/index" target="_blank">https://ziyuan.baidu.com/linksubmit/index</a><br/></p>
        <p>使用说明</p>
        <ul class="tip-list list-paddingleft-2" style="list-style-type: none;">
            <li><p>1. 链接提交工具是网站主动向百度搜索推送数据的工具，本工具可缩短爬虫发现网站链接时间，网站时效性内容建议使用链接提交工具，实时向搜索推送数据。本工具可加快爬虫抓取速度，无法解决网站内容是否收录问题</p></li>
            <li><p>2. 百度搜索资源平台为站长提供链接提交通道，您可以提交想被百度收录的链接，百度搜索引擎会按照标准处理，但不保证一定能够收录您提交的链接。</p></li>
            <li><p><img src="<?php echo IMG_PATH;?>bdts/bdts1.png"/><br/></p></li>
        </ul>
        <p><br/></p>
        <p>将以上参数填写到CMS后台即可</p>
        <p><img src="<?php echo IMG_PATH;?>bdts/bdts2.png"/></p>
        <p><br/></p>
        <p>可以随时随地查看错误日志</p>
        <p><img src="<?php echo IMG_PATH;?>bdts/bdts3.png"/></p>
    </div>


    <p> &nbsp;&nbsp;</p>
</div>
<style>
body {
    background-color: #fff;
}
img {max-width: 80%}
.h1, .h2, .h3, h1, h2, h3 {
    margin-top: 20px;
    margin-bottom: 10px;
}
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    font-family: inherit;
    font-weight: 500;
    line-height: 1.1;
    color: inherit;
}
h2 {
    padding-bottom: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e7e7eb;
}
.h2, h2 {
    font-size: 30px;
    color: inherit;
}
p {
    margin: 0 0 10px;
}
img,video {
    border: 2px solid #f1f3f4;
    padding: 10px;
    border-radius: 5px;
    margin: 5px;
}
.content-text table {
    border: 1px solid #000000;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100% !important;
    word-break: break-all;
}
.content-text table th {
    padding: 8px !important;
    line-height: 30px !important;
    border: 1px solid #000000;
        background-color: rgb(191, 191, 191);
}
.content-text table td {
    word-wrap: break-word;
    border: 1px solid #000000;
    padding: 4px 8px !important;
    font-size: 12px;
    line-height: 30px !important;
    vertical-align: middle;
}
</style>
</body>
</html>