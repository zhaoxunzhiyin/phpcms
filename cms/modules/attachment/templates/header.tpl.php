<?php
defined('IN_CMS') or exit('No permission resources.');
defined('IS_ADMIN') or exit('No permission resources.');?>
<!DOCTYPE html>
<html>
<head>
<meta charset="<?php echo CHARSET;?>">
<title><?php echo L('website_manage');?></title>
<meta name="author" content="zhaoxunzhiyin" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<?php echo load_css(CSS_PATH.'bootstrap/css/bootstrap.min.css');?>
<?php echo load_css(CSS_PATH.'font-awesome/css/font-awesome.min.css');?>
<?php echo load_css(CSS_PATH.'admin/css/style.css');?>
<?php echo load_css(CSS_PATH.'table_form.css');?>
<?php echo load_css(CSS_PATH.'admin/css/my.css');?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<script type="text/javascript">
var admin_file = '<?php echo SELF;?>';
var is_admin = <?php if (cleck_admin(param::get_session('roleid'))) {?>1<?php } else { ?>0<?php } ?>;
var is_cms = 0;
var web_dir = '<?php echo WEB_PATH;?>';
var pc_hash = '<?php echo dr_get_csrf_token();?>';
var csrf_hash = '<?php echo csrf_hash();?>';
</script>
<?php echo load_js(JS_PATH.'admin_common.js');?>
<?php echo load_js(JS_PATH.'my.js');?>
<?php echo load_js(JS_PATH.'layer/layer.js');?>
<?php if(isset($show_validator)) { ?>
<?php echo load_js(JS_PATH.'formvalidator.js');?>
<?php echo load_js(JS_PATH.'formvalidatorregex.js');?>
<?php } ?>
<?php if(!get_siteid()) exit('error');?>
</head>
<body class="page-content-white<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="scroll-to-top">
    <i class="bi bi-arrow-up-circle-fill"></i>
</div>