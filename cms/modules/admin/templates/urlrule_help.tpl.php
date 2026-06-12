<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="container">
        <h2>自定义URL规则</h2>
        <div class="content-text">
    <p><strong>一、栏目、内容、栏目搜索</strong></p><p>1、栏目</p><p>常见标签</p><pre class="brush:html;toolbar:false">{$catid}&nbsp;表示栏目catid
{$page}&nbsp;表示分页号
{$catdir}&nbsp;表示栏目目录名称
{$parentdir}&nbsp;包含父级层次的目录
{$categorydir}&nbsp;表示父级目录名称</pre><p style="white-space: normal;">对应的动态地址</p><pre class="brush:html;toolbar:false">index.php?m=content&amp;c=index&amp;a=lists&amp;catid=1</pre><p><br/></p><p>2、内容</p><p style="white-space: normal;">常见标签</p><pre class="brush:html;toolbar:false">{$id}&nbsp;表示栏目id
{$catid}&nbsp;表示栏目catid
{$page}&nbsp;表示分页号
{$catdir}&nbsp;表示栏目目录名称
{$parentdir}&nbsp;包含父级层次的目录
{$categorydir}&nbsp;表示父级目录名称
{$year}&nbsp;表示年
{$month}&nbsp;表示月
{$day}&nbsp;表示日
{$prefix}&nbsp;表示自定义名（需要添加自定义字段：prefix）</pre><p style="white-space: normal;">对应的动态地址</p><pre class="brush:html;toolbar:false">index.php?m=content&amp;c=index&amp;a=show&amp;catid=1&amp;id=1</pre><p><br/></p><p>3、栏目搜索</p><p style="white-space: normal;">常见标签</p><pre class="brush:html;toolbar:false">{$param}&nbsp;表示搜索参数</pre><p style="white-space: normal;">对应的动态地址</p><pre class="brush:html;toolbar:false">index.php?m=content&amp;c=search&amp;a=init&amp;catid=1
index.php?m=content&amp;c=search&amp;a=init&amp;catid=1&amp;字段=值</pre><p><br/></p><p><strong>二、全站搜索</strong></p><p style="white-space: normal;">常见标签</p><pre class="brush:html;toolbar:false">{param}&nbsp;表示搜索参数</pre><p style="white-space: normal;">对应的动态地址</p><pre class="brush:html;toolbar:false">index.php?m=search&amp;c=index&amp;a=init
index.php?m=search&amp;c=index&amp;a=init&amp;字段=值</pre></div>


    <p> &nbsp;&nbsp;</p>
</div>
<style>
body {
    background: #fff;
    font-family: Menlo,Monaco,Consolas,"Courier New",monospace;
}
img {max-width: 80%}
h2 {
    padding-bottom: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid #e7e7eb;
}
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
img,video {
    border: 2px solid #f1f3f4;
    padding: 10px;
    border-radius: 5px;
    margin: 5px;
}
p {
    margin: 0 0 10px;
}
.container {
    width: 100%;
    padding: 0px 28px;
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