<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<script type="text/javascript">
var pc_file = '<?php echo WEB_PATH?>';
</script>
<link rel="stylesheet" href="<?php echo JS_PATH?>layui/css/layui.css" media="all" />
<div class="admin-main layui-anim layui-anim-upbit">
    <ul class="icons layui-row"><?php echo $this->input->get('value')?></ul>
</div>
<script type="text/javascript" src="<?php echo JS_PATH?>layui/layui.js"></script>
<script src="<?php echo JS_PATH?>menu_icons.js" charset="utf-8"></script>
</body>
</html>