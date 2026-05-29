<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); ?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<title><?php echo L('message_tips');?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php echo load_css(CSS_PATH.'bootstrap/css/bootstrap.min.css');?>
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(CSS_PATH.'admin/css/style.css');?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<script type="text/javascript">
var is_admin = 0;
var web_dir = '<?php echo WEB_PATH;?>';
var pc_hash = '<?php echo dr_get_csrf_token();?>';
var csrf_hash = '<?php echo csrf_hash();?>';
</script>
<?php echo load_js(JS_PATH.'admin_common.js');?>
<?php echo load_js(JS_PATH.'layer/layer.js');?>
</head>
<body>
<div class="page-404-full-page">
    <div class="row">
        <div class="col-xs-12 page-404">
            <?php if ($code==1) {?>
            <div class="admin_msg number font-green"> <i class="fa fa-check-circle-o"></i> </div>
            <?php } else if ($code==2) {?>
            <div class="admin_msg number font-blue"> <i class="fa fa-info-circle"></i> </div>
            <?php } else {?>
            <div class="admin_msg number font-red"> <i class="fa fa-times-circle-o"></i> </div>
            <?php }?>
            <div class="details">
                <h4><?php echo $msg?></h4>
                <p class="alert_btnleft"><?php if($url_forward=='goback' || $url_forward=='') {?>
                <a href="javascript:history.back();" >[<?php echo L('return_previous');?>]</a>
                <?php } elseif($url_forward=="close") {?>
                <button type="button" id="close" class="btn red"> <i class="fa fa-close"></i> <?php echo L('close');?></button>
                <?php } elseif($url_forward=="blank") {?>
                <?php } elseif($url_forward) {if(strpos($url_forward,'&pc_hash')===false) $url_forward .= '&pc_hash='.dr_get_csrf_token();?>
                <a href="<?php echo $url_forward?>"><?php echo L('click_here');?></a>
                <script language="javascript">setTimeout("redirect('<?php echo $url_forward?>');",<?php echo $ms?>);</script> 
                <?php }?>
                <?php if($returnjs) { ?> <script style="text/javascript"><?php echo $returnjs;?></script><?php } ?>
                <?php if ($dialog):?><script style="text/javascript">$(function(){if (window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right) {window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.right.location.reload(true);}else{window.top.$(".layui-tab-item.layui-show").find("iframe")[0].contentWindow.location.reload(true);}if(window.top.art.dialog({id:"<?php echo $dialog?>"})){window.top.art.dialog({id:"<?php echo $dialog?>"}).close();}else{ownerDialog.close();}})</script><?php endif;?></p>
            </div>
        </div>
    </div>
</div>
<?php if($url_forward=="close") {?>
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
layui.use(['form','miniTab'], function () {
    var form = layui.form,
        layer = layui.layer,
        miniTab = layui.miniTab;

    //监听关闭
    $('#close').on('click', function() {
        miniTab.deleteCurrentByIframe();
    });
});
</script>
<?php }?>
</body>
</html>