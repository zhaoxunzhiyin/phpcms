<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>clipboard.min.js"></script>
<div class="pad_10">
<div style="font-size:14px;line-height:25px;">
<div class="explain-col"> 
温馨提示：您可以手动或者点下面按钮把下面代码复制到模板中,根据情况选择标签使用。
</div>
<div style="margin-top:6px;"></div>
<textarea name="lable" id="lable" rows="12" cols="90">{pc:slider action="lists" postion="<?php echo $typeid;?>" siteid="$siteid" order="desc" num="4"}
    {loop $data $r}
    <p>排序编号：{$r[listorder]}</p>
	<p>描述：{$r[name]}</p>
	<p>链接地址：{$r[url]}</p>
	<p>图片：{$r[image]}</p>
	<p>描述：{$r[description]}</p>
    {/loop}
{/pc}</textarea>
<p style="margin-top:6px;"><input type="button" class="button" data-clipboard-target="#lable" value="复制标签代码到剪切板"/></p>
</div>
<script type="text/javascript">
var clipboard = new ClipboardJS('.button');
clipboard.on('success', function(e) {
    Dialog.alert('恭喜，复制成功！');
});
clipboard.on('error', function(e) {
    Dialog.alert('复制四百！');
});
</script>
</div>
</body>
</html>