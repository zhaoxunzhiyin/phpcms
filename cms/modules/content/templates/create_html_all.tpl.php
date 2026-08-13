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
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('生成开关');?></label>
                <div class="col-md-9">
                    <label><button type="button" onclick="iframe_show('<?php echo L('统一设置')?>', '?m=content&c=create_html&a=public_batch_category&pc_hash='+pc_hash)" class="btn default"> <i class="fa fa-reorder"></i> <?php echo L('统一设置URL规则')?> </button></label>
                    <label><button type="button" onclick="iframe_show('<?php echo L('栏目设置')?>', '?m=content&c=create_html&a=public_html_index&pc_hash='+pc_hash)" class="btn default"> <i class="fa fa-reorder"></i> <?php echo L('按栏目设置URL规则')?> </button></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_sync_index&pc_hash='+pc_hash, '500px', '300px')" class="btn blue"> <i class="fa fa-cog"></i> <?php echo L('一键开启栏目静态')?> </a></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_sync2_index&pc_hash='+pc_hash, '500px', '300px')" class="btn red"> <i class="fa fa-cog"></i> <?php echo L('一键关闭栏目静态')?> </a></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_csync_index&pc_hash='+pc_hash, '500px', '300px')" class="btn blue"> <i class="fa fa-cog"></i> <?php echo L('一键开启内容静态')?> </a></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_csync2_index&pc_hash='+pc_hash, '500px', '300px')" class="btn red"> <i class="fa fa-cog"></i> <?php echo L('一键关闭内容静态')?> </a></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');" class="btn default"> <i class="fa fa-refresh"></i> <?php echo L('一键更新栏目URL');?> </a></label>
                </div>
            </div>
            <input type="hidden" name="dosubmit" value="1">
            <div class="form-group" style="border-top: 1px dashed #eef1f5; padding-top: 10px;">
                <label class="col-md-2 control-label"><?php echo L('最大分页限制');?></label>
                <div class="col-md-9">
                    <label><input type="text" class="form-control" value="" name="maxsize" id="maxsize"></label>

                    <span class="help-block"><?php echo L('当栏目页数过多时，设置此数量可以生成指定的页数，后面页数就不会再生成');?></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('一键生成');?></label>
                <div class="col-md-9">
                    <label><button type="button" onclick="dr_bfb('<?php echo L('一键生成');?>', 'myform_category', '?m=content&c=create_all_html&a=category&maxsize='+$('#maxsize').val()+'&go_url=1&pc_hash='+pc_hash)" class="btn dark"> <i class="fa fa-th-large"></i> <?php echo L('一键生成全站栏目和内容');?> </button></label>
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