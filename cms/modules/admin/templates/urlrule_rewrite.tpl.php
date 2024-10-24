<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<style type="text/css">
hr, p {margin: 20px 0;}
.portlet>.portlet-title>.caption>.caption-helper {padding: 0;margin: 0;line-height: 13px;color: #9eacb4;font-size: 13px;font-weight: 400;}
textarea {min-height: auto;}
.alert>p {margin: 0;}
.alert>p, .alert>ul {margin-bottom: 0;}
</style>
<script type="text/javascript">
function dr_test(obj, name, domain) {
    $(obj).html('<?php echo L('在检测中');?>');
    $.ajax({
        url: domain+"/rewrite-test.html",
        type: 'GET',
        timeout: 1000,
        dataType: "jsonp",
        jsonp: "callback",
        jsonpCallback: "callback"
    }).done(function(data) {
        $(obj).html('<?php echo L('环境检测');?>');
        if (data.code) {
            dr_tips(1, data.msg);
        } else {
            Dialog.alert('域名【'+name+'】不支持伪静态，首先需要确定服务器支持rewrite模块并开启了，其次需要正确配置上面的方法');
        }
    }).fail(function() {
        $(obj).html('<?php echo L('环境检测');?>');
        Dialog.alert('域名【'+name+'】不支持伪静态，首先需要确定服务器支持rewrite模块并开启了，其次需要正确配置上面的方法');
    });
}
function dr_rewrite_config() {
    $('#dr_write').hide();
    $.ajax({
        type: "GET",
        url: "?m=admin&c=urlrule&a=public_rewrite_add",
        dataType: "json",
        success: function (data) {
            if (data.code) {
                dr_tips(1, data.msg);
                $('#dr_write').show();
                $('#dr_code').html(data.data.code);
                $('#dr_error').html(data.data.error);
            } else {
                dr_tips(0, data.msg);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, this, thrownError);
        }
    });
}
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('当前服务器是：<b>'.$name.'</b>');?></p>
</div>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-green sbold"><?php echo $name;?></span>
            <span class="caption-helper">需要在服务器中配置，不懂的可以咨询服务器空间商</span>
        </div>
    </div>
    <div class="portlet-body">
        <p><?php echo $note;?></p>
        <?php if ($code) {?>
        <p><textarea class="form-control" style="width:90%; height:<?php echo $count * 25;?>px;"><?php echo $code;?></textarea></p>
        <?php }?>

        <ul class="list-group" style="width:90%;">
        <?php if(is_array($domain)){
        foreach($domain as $name=>$cname){?>
            <li class="list-group-item"><?php echo $cname;?>：<?php echo $name;?>
                <span class="badge badge badge-danger" style="cursor: pointer"  onClick="dr_test(this, '<?php echo $name;?>', '<?php echo $name;?>')"><?php echo L('环境检测');?> </span>
            </li>
        <?php }}?>
        </ul>
        </div>
        <p>伪静态URL解析规则配置文件：/caches/configs/rewrite.php </p>
        <p>使用自定义URL之后必须要设置解析规则，否则是无法正常打开页面的</p>
        <p>
            <a href="javascript:;" class="btn green" onClick="dr_rewrite_config()"> <?php echo L('生成解析规则');?> </a>
        </p>
    </div>

<div class="portlet light bordered" id="dr_write" style="display: none">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-green sbold">生成结果</span>
        </div>
    </div>
    
    <div class="alert alert-danger margin-top-30">
        <p>生成的代码不一定准确，仅供参考语法</p>
        <p>一定一定一定不要全部复制到配置文件中，一定要逐条调试</p>
        <p>规则的优先级别一定要根据正则表达式的优先级别来写，否则会出现指向错乱的情况</p>
    </div>

    <div class="portlet-body">
        <div id="dr_code"></div>
    </div>

    <div class="portlet-body" id="dr_error" style="color: red">

    </div>
</div>
<style>
#dr_code textarea{
    margin: 5px 0;
}
#dr_code {
    line-height: 20px;
    color: #999;
}
</style>
</div>
</div>
</div>
</body>
</html>
