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
<body style="background: #ffffff">
<div class="scroll-to-top">
    <i class="bi bi-arrow-up-circle-fill"></i>
</div>
<form class="form-horizontal" role="form" id="myform">
    <?php echo dr_form_hidden();?>
    <div style="padding: 20px;">
        <ul class="list-group">
            <?php if(is_array($list) && !empty($list)){ foreach ($list as $id=>$t) {?>
            <li class="list-group-item" style="overflow: hidden">
                <input type="hidden" name="data[<?php echo $id;?>]" value="" id="aid_<?php echo $id;?>">
                <a href="<?php echo $t;?>" target="_blank"><?php echo $t;?></a>
                <a id="content_<?php echo $id;?>" style="margin-top:0px;float: right;"> </a>
            </li>
            <script type="text/javascript">
                function dr_down_<?php echo $id;?>(){
                    $('#content_<?php echo $id;?>').html('<img width="15" src="<?php echo JS_PATH;?>layer/theme/default/loading-2.gif">');
                    $.ajax({
                        type: "GET",
                        url: "<?php echo $down_url;?>&id=<?php echo $id;?>",
                        dataType: "json",
                        success: function (json) {
                            if (json.code == 0) {
                                $('#content_<?php echo $id;?>').addClass("badge badge-danger");
                                $('#content_<?php echo $id;?>').html(json.msg);
                                $('#content_<?php echo $id;?>').attr("onclick", "dr_down_<?php echo $id;?>()");
                            } else {
                                $('#content_<?php echo $id;?>').removeClass("badge-danger");
                                $('#content_<?php echo $id;?>').addClass("badge badge-success");
                                $('#content_<?php echo $id;?>').html('<?php echo L('成功');?>');
                                $('#aid_<?php echo $id;?>').val(json.msg);
                            }
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            dr_ajax_alert_error(HttpRequest, this, thrownError);
                        }
                    });
                }
                $(function () {
                    dr_down_<?php echo $id;?>();
                });
            </script>
            <?php }}?>
        </ul>
    </div>
</form>
</body>
</html>