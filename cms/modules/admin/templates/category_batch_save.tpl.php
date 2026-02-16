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
    <p><a href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');"><?php echo L('变更栏目属性之后，需要一键更新栏目配置信息');?></a></p>
</div>
<div class="portlet bordered light form-horizontal">
    <div class="portlet-body">
        <div class="form-body">

            <form action="?m=admin&c=category&a=public_batch_category" class="form-horizontal" method="post" name="myform" id="myform">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('ismenu')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='1' checked> <?php echo L('yes');?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='info[ismenu]' value='0'> <?php echo L('no');?> <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('继承下级')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[getchild]' value='1'> <?php echo L('open');?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[getchild]' value='0' checked> <?php echo L('close');?> <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('可用')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='0' checked> <?php echo L('可用');?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='info[disabled]' value='1'> <?php echo L('禁用');?> <span></span></label>
                        </div>
                        <span class="help-block"><?php echo L('禁用状态下此栏目不能正常访问')?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('您现在的位置')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='1' checked> <?php echo L('display');?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[iscatpos]' value='0'> <?php echo L('hidden');?> <span></span></label>
                        </div>
                        <span class="help-block"><?php echo L('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示')?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('左侧')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='1' checked> <?php echo L('display');?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[isleft]' value='0'> <?php echo L('hidden');?> <span></span></label>
                        </div>
                        <span class="help-block"><?php echo L('前端栏目调用左侧不会显示，但可以正常访问')?></span>
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