<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<script type="text/javascript">
var pc_file = '<?php echo WEB_PATH?>';
</script>
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<div class="admin-main layui-anim layui-anim-upbit">
    <blockquote class="layui-elem-quote">
        当前共引入<span class="layui-red iconsLength"></span>个外部图标。<span class="layui-word-aux">【点击可复制】此页面并非后台模版需要的，只是为了让大家了解都引入了哪些外部图标，实际应用中可删除。</span>
    </blockquote>
    <textarea id="copyText"></textarea>
    <ul class="icons layui-row"></ul>
</div>
<script type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<script src="<?php echo JS_PATH?>icons.js" charset="utf-8"></script>
</body>
</html>