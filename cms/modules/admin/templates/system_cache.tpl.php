<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
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
    <p><?php if ($run_time) {?>
    <font color="green"><?php echo L('最近自动执行时间为：'.$run_time);?></font>
    <?php } else {?>
    <font color="red"><?php echo L('当前服务器没有设置自动任务脚本');?></font>
    <?php }?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="pc_hash" type="hidden" value="<?php echo dr_get_csrf_token();?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('缓存设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('缓存设置');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('缓存开关');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE]" value="1"<?php if ($data['SYS_CACHE']) {?> checked<?php }?> /> <?php echo L('开启');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE]" value="0"<?php if (empty($data['SYS_CACHE'])) {?> checked<?php }?> /> <?php echo L('关闭');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('推荐开启缓存功能，可以大大提高系统运行效率');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('清理方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE_CLEAR]" value="0"<?php if (empty($data['SYS_CACHE_CLEAR'])) {?> checked<?php }?> /> <?php echo L('自动');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE_CLEAR]" value="1"<?php if ($data['SYS_CACHE_CLEAR']) {?> checked<?php }?> /> <?php echo L('手动');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('自动表示后台操作时会自动清理缓存数据');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('缓存方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE_TYPE]" value="0"<?php if (empty($data['SYS_CACHE_TYPE'])) {?> checked<?php }?> /> <?php echo L('文件');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE_TYPE]" value="1"<?php if ($data['SYS_CACHE_TYPE'] == 1) {?> checked<?php }?> /> <?php echo L('Memcached');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[SYS_CACHE_TYPE]" value="2"<?php if ($data['SYS_CACHE_TYPE'] == 2) {?> checked<?php }?> /> <?php echo L('Redis');?> <span></span></label>
                            </div>
                        </div>
                    </div>


                    <input type="hidden" name="data[SYS_CACHE_SMS]" value="<?php echo intval($data['SYS_CACHE_SMS']);?>">

                    <?php foreach ($cache_var as $value => $name) {?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L($name);?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="data[SYS_CACHE_<?php echo $value;?>]" value="<?php echo floatval($data['SYS_CACHE_'.$value]);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('小时');?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"> <?php echo L('0表示不缓存');?> </span>
                        </div>
                    </div>
                    <?php }?>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('任务自动清理');?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="data[SYS_CACHE_CRON]" value="<?php echo intval($data['SYS_CACHE_CRON']);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('天');?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-inline"> <?php echo L('缓存最大储存的时间，过期后将被任务队列清理缓存');?> </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
                <label><button type="button" onclick="dr_test_cache()" class="btn red"> <i class="fa fa-cloud"></i> <?php echo L('测试')?></button></label>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
function dr_test_cache() {
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 10000
    });
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "?m=admin&c=system_cache&a=public_test_cache",
        data: $('#myform').serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            dr_tips(json.code, json.msg);
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, this, thrownError);
        }
    });
}
</script>
</body>
</html>