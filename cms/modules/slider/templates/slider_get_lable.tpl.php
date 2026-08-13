<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript" src="<?php echo JS_PATH?>clipboard.min.js"></script>
<div class="pad_10">
<div style="font-size:14px;line-height:25px;">
<div class="explain-col"> 
温馨提示：您可以手动或者点下面按钮把下面代码复制到模板中，根据情况选择标签使用。
</div>
<div style="margin-top:6px;"></div>
<textarea id="lable" rows="12" cols="80">{pc:slider action="lists" postion="<?php echo $typeid;?>" siteid="$siteid" order="desc" num="4"}
    {loop $data $r}
    <p>排序编号：{$r['listorder']}</p>
    <p>名称：{$r['name']}</p>
    <p>链接地址：{$r['url']}</p>
    <p>图片：{dr_get_file($r['image'])}</p>
    <p>手机图片：{dr_get_file($r['pic'])}</p>
    <p>图标标示：{$r['icon']}</p>
    <p>描述：{$r['description']}</p>
    {/loop}
{/pc}</textarea>
<p style="margin-top:6px;"><button class="btn green" data-clipboard-action="copy" data-clipboard-target="#lable" id="copy_btn">点击复制</button></p>
</div>
<script type="text/javascript">
//点击复制
var clipboard = new ClipboardJS('.btn');
$("#copy_btn").click(function() {
    var input = $('#lable');
    input.select();
    // 执行浏览器复制命令
    document.execCommand("Copy");
    //提示已复制
    dr_tips(1, '已复制');
})
</script>
</div>
</body>
</html>