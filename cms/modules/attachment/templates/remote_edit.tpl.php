<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
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
<form action="?m=attachment&c=attachment&a=remote_edit&id=<?php echo $data['id']?>" class="form-horizontal" method="post" name="myform" id="myform">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li class="active">
                <a data-toggle="tab_0"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('存储策略').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cloud"></i> <?php if (is_pc()) {echo L('存储策略');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane active" id="tab_0">

                <div class="form-body form">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('名称');?></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control input-large" value="<?php echo html2code($data['name']);?>" name="data[name]" />
                            <span class="help-block"><?php echo L('给它一个描述名称');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('存储类型');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <?php foreach ($this->type as $i=>$n) {?>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[type]" onclick="dr_remote('<?php echo $i;?>')" value="<?php echo $i;?>"<?php echo ((int)$data['type']==$i) ? ' checked' : ''?> /> <?php echo L($n['name']);?> <span></span> </label>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group r r0">
                        <label class="col-md-2 control-label"><?php echo L('使用说明');?></label>
                        <div class="col-md-9">
                            <p class="form-control-static"> <?php echo L('本地磁盘存储是将文件存储到本地的一块盘之中');?> </p>
                        </div>
                    </div>
                    <div class="form-group r r0">
                        <label class="col-md-2 control-label"><?php echo L('本地存储路径');?></label>
                        <div class="col-md-7">
                            <input class="form-control" type="text" name="data[value][0][path]" value="<?php echo html2code($data['value']['path']);?>" />
                            <span class="help-block"><?php echo L('填写磁盘绝对路径或者相当于附件目录的目录路径，一定要以“/”结尾');?></span>
                        </div>
                    </div>

                    <?php foreach ($this->load_file as $i=>$tp) {?>
                    <?php include $tp?>
                    <?php }?>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('附件远程访问URL');?></label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" value="<?php echo html2code($data['url']);?>" name="data[url]" />
                            <span class="help-block"><?php echo L('浏览器可访问的URL地址，必须以http://或https://开头，要以“/”结尾');?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('?m=attachment&c=attachment&a=remote_edit&id=<?php echo $data['id']?>', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('保存');?></button>
                <button type="button" onclick="dr_test_attach()" class="btn red"> <i class="fa fa-cloud"></i> <?php echo L('测试');?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript">
$(function() {
    dr_remote(<?php echo intval($data['type']);?>);
});
function dr_test_attach() {
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 10000
    });
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "?m=attachment&c=attachment&a=public_test_attach",
        data: $("#myform").serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            dr_tips(json.code, json.msg, -1);
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
function dr_remote(i) {
    $('.r').hide();
    $('.r'+i).show();
}
</script>
</div>
</div>
</body>
</html>