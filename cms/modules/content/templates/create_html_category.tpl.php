<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('确保网站目录必须有可写权限');?></p>
</div>
<div class="portlet bordered light form-horizontal">
    <div class="portlet-body">
        <div class="form-body">

            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('网站首页');?></label>
                <div class="col-md-9">
                    <label><button type="button" onclick="dr_admin_menu_ajax('?m=content&c=create_html&a=public_index_ajax', 1)" class="btn blue"> <i class="fa fa-file-o"></i> <?php echo L('生成首页静态文件');?> </button></label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('快捷配置');?></label>
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

            <form id="myform_category">
                <input type="hidden" name="dosubmit" value="1">
                <div class="form-group" style="border-top: 1px dashed #eef1f5; padding-top: 10px;">
                    <label class="col-md-2 control-label"><?php echo L('最大分页限制');?></label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" value="" name="maxsize"></label>

                        <span class="help-block"><?php echo L('当栏目页数过多时，设置此数量可以生成指定的页数，后面页数就不会再生成');?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('按所选栏目');?></label>
                    <div class="col-md-9">
                        <select class="bs-select form-control"<?php if (dr_count($categorys) > 30) {echo 'data-live-search="true"';}?> name='catids[]' id='catids' multiple="multiple" style="width:350px;height:280px;" title="<?php echo L('no_limit_category');?>">
                            <option value='0' selected><?php echo L('no_limit_category');?></option>
                            <?php echo $string;?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('生成栏目页面');?></label>
                    <div class="col-md-9">
                        <label><button type="button" onclick="dr_bfb('<?php echo L('生成栏目页面');?>', 'myform_category', '?m=content&c=create_html&a=category')" class="btn dark"> <i class="fa fa-th-large"></i> <?php echo L('开始生成静态');?> </button></label>
                        <label><button type="button" onclick="dr_bfb('<?php echo L('生成栏目页面');?>', 'myform_category', '?m=content&c=create_html&a=public_category_point')" class="btn red"> <i class="fa fa-th-large"></i> <?php echo L('上次未执行完毕时继续执行');?> </button></label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>