<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
var pc_file = '<?php echo WEB_PATH?>';
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p>当前共引入<span class="font-red iconslength"></span>个外部图标。【点击可复制】此页面并非后台模版需要的，只是为了让大家了解都引入了哪些外部图标，实际应用中可删除。</p>
</div>
<div class="portlet bordered light">
    <div class="portlet-body form">
        <div class="form-body">
            <textarea id="copyText"></textarea>
            <ul class="icons row"></ul>
        </div>
    </div>
</div>
</div>
</div>
</div>
<script src="<?php echo JS_PATH?>icons.js" charset="utf-8"></script>
</body>
</html>