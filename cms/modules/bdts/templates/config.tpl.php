<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<link href="<?php echo JS_PATH?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="dosubmit" type="hidden" value="1">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('bdts_bdts').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-paw"></i> <?php if (is_pc()) {echo L('bdts_bdts');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('是否显示按钮');?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[bottom]" value="1" <?php if ($data['bottom']) {echo ' checked';}?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('开启将在模型列表内容底部显示按钮')?></span>
                        </div>
                    </div>
                    <?php foreach((array)$sitemodel_data as $t){?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo $t['name'];?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[use][]" value="<?php echo $t['tablename'];?>" <?php if ($data['use'] && in_array($t['tablename'], $data['use'])) {echo ' checked';}?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-9">
                            <a href="javascript:add_menu();" class="btn blue"><i class="fa fa-plus"></i> <?php echo L('添加域名');?></a>
                        </div>
                    </div>
                    <div id="menu_body">
                        <?php foreach((array)$bdts as $t){?>
                        <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="data[bdts][][site]" placeholder="<?php echo L('站点域名');?>" value="<?php echo $t['site'];?>"></label>
                                <label><input class="form-control input-large" type="text" name="data[bdts][][token]" value="<?php echo $t['token'];?>" placeholder="<?php echo L('密钥token');?>"></label>
                                <label><a href="javascript:;" onClick="remove_menu(this)" class="btn red"><i class="fa fa-trash"></i> <?php echo L('删除');?></a></label>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('接口说明');?>：</label>
                        <div class="col-md-9">
                            <span class="help-block"><?php echo L('链接提交工具是网站主动向百度搜索推送数据的工具，本工具可缩短爬虫发现网站链接时间，网站时效性内容建议使用链接提交工具，实时向搜索推送数据。本工具可加快爬虫抓取速度，无法解决网站内容是否收录问题。');?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('the_save')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript">
function add_menu() {
    var data = '<div class="form-group"><label class="col-md-2 control-label">&nbsp;</label><div class="col-md-9"><label><input class="form-control" type="text" name="data[bdts][][site]" placeholder="<?php echo L('站点域名');?>" value=""></label>&nbsp;<label><input class="form-control input-large" type="text" name="data[bdts][][token]" placeholder="<?php echo L('密钥token');?>"></label><label>&nbsp;<a href="javascript:;" onClick="remove_menu(this)" class="btn red"><i class="fa fa-trash"></i> <?php echo L('删除');?></a></label></div></div>';
    $('#menu_body').append(data);
}
function remove_menu(_this) {
    $(_this).parent().parent().parent().remove()
}
</script>
</div>
</div>
</body>
</html>