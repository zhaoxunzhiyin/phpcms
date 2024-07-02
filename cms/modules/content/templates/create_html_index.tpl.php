<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="portlet bordered light form-horizontal">
    <div class="portlet-body">
        <div class="form-body">

            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('首页静态');?></label>
                <div class="col-md-9">
                    <label><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=content&c=create_html&a=public_index_edit&share=1&pc_hash='+pc_hash, 0)" class="badge badge-<?php echo (!$ishtml ? 'no' : 'yes');?>"> <i class="fa fa-<?php echo (!$ishtml ? 'times' : 'check');?>"></i> </a></label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('移动端与PC端URL同步');?></label>
                <div class="col-md-9">
                    <label><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=content&c=create_html&a=public_index_edit&share=0&pc_hash='+pc_hash, 0)" class="badge badge-<?php echo (!$mobilehtml ? 'no' : 'yes');?>"> <i class="fa fa-<?php echo (!$mobilehtml ? 'times' : 'check');?>"></i> </a></label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('网站首页');?></label>
                <div class="col-md-9">
                    <label><button type="button" onclick="dr_admin_menu_ajax('?m=content&c=create_html&a=public_index_ajax', 1)" class="btn blue"> <i class="fa fa-file-o"></i> <?php echo L('生成首页静态文件');?> </button></label>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>