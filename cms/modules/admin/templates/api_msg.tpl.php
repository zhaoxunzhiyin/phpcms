<?php defined('IS_ADMIN') or exit('No permission resources.');?>
<!DOCTYPE html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>" />
<title><?php echo $meta_title ? $meta_title : L('message_tips');?></title>
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
<style>
p {margin: 20px 0;}
</style>
<body class="page-container-bg-solid" style=" background-color: #eef1f5;">
<div class="page-content">
    <div class="container" style="padding-top:110px">
        <div class="portlet light">
            <div class="portlet-body">
                <div class="row msg-body">
                    <div class="col-md-12 text-center">
                        <div class="msg-info">
                            <p class="msg-title"><?php echo $msg;?></p>
                            <p>
                                系统检测到前端缺失<?php echo pc_base::load_sys_class('service')->get_filename();?>模板，本页面是临时的显示模板。
                            </p>
                            <p class="msg-url">
                                <?php if($url) {?>
                                <a href="<?php echo $url;?>">如果您的浏览器没有自动跳转，请点击这里</a>
                                <meta http-equiv="refresh" content="<?php echo $time;?>; url=<?php echo $url;?>">
                                <?php } else {?>
                                <a href="<?php echo $backurl;?>" >[点击返回上一页]</a>
                                <?php }?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>