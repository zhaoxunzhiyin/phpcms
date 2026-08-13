<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
var pc_file = '<?php echo WEB_PATH?>';
var icon = '<?php echo $this->input->get('value')?>';
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="portlet bordered light">
    <div class="portlet-body form">
        <div class="form-body">
        <ul class="icons row"></ul>
        </div>
    </div>
</div>
</div>
</div>
</div>
<script src="<?php echo JS_PATH?>menu_icons.js" charset="utf-8"></script>
</body>
</html>