<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
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
<?php echo load_css(CSS_PATH.'bootstrap-tagsinput.css');?>
<?php echo load_css(CSS_PATH.'table_form.css');?>
<?php echo load_css(CSS_PATH.'admin/css/my.css');?>
<?php
if(!$this->get_siteid()) dr_admin_msg(0,L('admin_login'),'?m=admin&c=index&a='.SYS_ADMIN_PATH);
if(isset($show_dialog)) {?>
<?php echo load_js(JS_PATH.'dialog.js');?>
<?php } ?>
<?php echo load_js(JS_PATH.'Dialog/main.js');?>
<?php echo load_js(CSS_PATH.'bootstrap/js/bootstrap.min.js');?>
<?php echo load_js(JS_PATH.'jquery.slimscroll.min.js');?>
<?php echo load_js(JS_PATH.'bootstrap-tagsinput.min.js');?>
<script type="text/javascript">
var admin_file = '<?php echo SELF;?>';
var is_admin = <?php if (cleck_admin(param::get_session('roleid'))) {?>1<?php } else { ?>0<?php } ?>;
var is_cms = <?php if(in_array(ROUTE_M, array('admin', 'content', 'special')) && in_array(ROUTE_C, array('category', 'content', 'sitemodel_field')) && in_array(ROUTE_A, array('add', 'edit', 'public_priview'))) {?>0<?php } else { ?>1<?php } ?>;
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
<script type="text/javascript">
$(function(){
<?php if(!isset($show_pc_hash)) { ?>
    var html_a = document.getElementsByTagName('a');
    var num = html_a.length;
    for(var i=0;i<num;i++) {
        var href = html_a[i].href;
        if(href && href.indexOf('javascript:') == -1) {
            if(href.indexOf('pc_hash') == -1) {
                if(href.indexOf('?') != -1) {
                    html_a[i].href = href+'&pc_hash='+pc_hash;
                } else {
                    html_a[i].href = href+'?pc_hash='+pc_hash;
                }
            }
        }
    }

    var html_form = document.forms;
    var num = html_form.length;
    for(var i=0;i<num;i++) {
        var newNode = document.createElement("input");
        newNode.name = 'pc_hash';
        newNode.type = 'hidden';
        newNode.value = pc_hash;
        html_form[i].appendChild(newNode);
    }
<?php } ?>
<?php if(SYS_CSRF) { ?>
    var html_form2 = document.forms;
    var num2 = html_form2.length;
    for(var i=0;i<num2;i++) {
        var csrfNode = document.createElement("input");
        csrfNode.name = '<?php echo SYS_TOKEN_NAME;?>';
        csrfNode.type = 'hidden';
        csrfNode.value = csrf_hash;
        html_form2[i].appendChild(csrfNode);
    }
<?php } ?>
});
</script>
</head>
<body class="page-content-white<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<?php if((!param::get('hide_menu')) || param::get('is_menu')) {?>
<?php if((!isset($show_header) && !param::get('is_iframe'))) {?>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu ib-a">
        <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle on" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-double-down"></i></a>
            <ul class="dropdown-menu">
                <?php if(isset($big_menu)) {echo '<li><a class="add fb tooltips" href="'.$big_menu[0].'" data-container="body" data-placement="bottom" data-original-title="'.$big_menu[1].'"><i class="fa fa-plus"></i> '.$big_menu[1].'</a></li><div class="dropdown-line"></div>';} else {$big_menu = '';}?>
                <?php echo admin::submenu(param::get('menuid'),$big_menu);?>
            </ul>
        </li>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
    <?php if(isset($big_menu)) { echo '<a class="add fb tooltips" href="'.$big_menu[0].'" data-container="body" data-placement="bottom" data-original-title="'.$big_menu[1].'"><i class="fa fa-plus"></i> '.$big_menu[1].'</a><i class="fa fa-circle"></i>';} else {$big_menu = '';}?>
    <?php echo admin::submenu(param::get('menuid'),$big_menu);?>
    </div>
    <?php }?>
</div>
<div class="content-header"></div>
<?php }}?>
<div class="scroll-to-top">
    <i class="bi bi-arrow-up-circle-fill"></i>
</div>