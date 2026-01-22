<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
$menu_data = $this->menu_db->get_one(array('name' => 'sms', 'm' => 'sms', 'c' => 'sms', 'a' => 'init'));?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<link href="<?php echo JS_PATH;?>bootstrap-touchspin/bootstrap.touchspin.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-touchspin/bootstrap.touchspin.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript">
<!--
$(function(){
    $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
    $("#defualtpoint").formValidator({tipid:"pointtip",onshow:"<?php echo L('input').L('defualtpoint')?>",onfocus:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"});
    $("#defualtamount").formValidator({tipid:"starnumtip",onshow:"<?php echo L('input').L('defualtamount')?>",onfocus:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"});
    $("#rmb_point_rate").formValidator({tipid:"rmb_point_rateid",onshow:"<?php echo L('input').L('rmb_point_rate')?>",onfocus:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"});
});
//-->
</script>
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
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('常用设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('常用设置');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('登录设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user"></i> <?php if (is_pc()) {echo L('登录设置');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('注册设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user-plus"></i> <?php if (is_pc()) {echo L('注册设置');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('格式规范').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user-md"></i> <?php if (is_pc()) {echo L('格式规范');}?> </a>
            </li>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="$('#dr_page').val('4')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('后台列表显示字段').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-table"></i> <?php if (is_pc()) {echo L('后台列表显示字段');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('show_app_point')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[showapppoint]" value="1" <?php echo $member_setting['showapppoint'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_rmb_point_rate">
                        <label class="col-md-2 control-label"><?php echo L('rmb_point_rate')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="rmb_point_rate" name="info[rmb_point_rate]" value="<?php echo $member_setting['rmb_point_rate'];?>" ></label>
                            <span class="help-block"><?php echo L('rmb_point_rate').L('between_1_to_8_num')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('show_register_protocol')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[showregprotocol]" value="1" <?php echo $member_setting['showregprotocol'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('register_protocol')?></label>
                        <div class="col-md-9">
                            <textarea name='info[regprotocol]' id='regprotocol' class="form-control" style="width:80%;height:120px;"><?php echo $member_setting['regprotocol']?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('register_verify_message')?></label>
                        <div class="col-md-9">
                            <textarea name='info[registerverifymessage]' id='registerverifymessage' class="form-control" style="width:80%;height:120px;"><?php echo $member_setting['registerverifymessage']?></textarea>
                            <span class="help-block"><?php echo L('register_func_tips')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('forgetpasswordmessage')?></label>
                        <div class="col-md-9">
                            <textarea name='info[forgetpassword]' id='forgetpassword' class="form-control" style="width:80%;height:120px;"><?php echo $member_setting['forgetpassword']?></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('登录主字段')?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" checked disabled /> <?php echo L('username')?> <span></span></label>
                                <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="info[login][field][]" value="phone" <?php if (dr_in_array('phone', (array)$member_setting['login']['field'])){?>checked<?php }?> /> <?php echo L('手机号')?> <span></span></label>
                                <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="info[login][field][]" value="email" <?php if (dr_in_array('email', (array)$member_setting['login']['field'])){?>checked<?php }?> /> <?php echo L('email')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('可同时选择多个字段作为登录主字段')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('登录图片验证码')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[login][code]" value="1" <?php echo $member_setting['login']['code'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('网页中的图片验证码')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_maxloginfailedtimes')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="info[maxloginfailedtimes]" id="maxloginfailedtimes" value="<?php echo intval($member_setting['maxloginfailedtimes']);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('login_ci')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('setting_maxloginfailedtimes_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_time_limit')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="info[syslogintimes]" id="syslogintimes" value="<?php echo intval($member_setting['syslogintimes']);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('minutes')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('setting_time_limit_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('登录超时时间')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="info[logintime]" id="logintime" value="<?php echo $member_setting['logintime'];?>"></label>
                            <span class="help-block"><?php echo L('单位秒，为空时默认为86400秒，为0时表示随浏览器进程，超时之后自动退出账号')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allow_register')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[allowregister]" value="1" <?php echo $member_setting['allowregister'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('register_model')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[choosemodel]" value="1" <?php echo $member_setting['choosemodel'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('注册图片验证码')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[register][code]" value="1" <?php echo $member_setting['register']['code'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('网页中的图片验证码')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('register_email_auth')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[enablemailcheck]" value="1"<?php if($mail_disabled) {echo ' disabled';}else{echo $member_setting['enablemailcheck'] ? ' checked' : '';}?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <?php if (!$sms_disabled) {?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mobile_checktype')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobile_checktype]" value="2"<?php echo ($member_setting['mobile_checktype']=='2') ? ' checked' : ''?><?php echo ($sms_disabled) ? ' disabled' : ''?> onclick="$('#sendsms_titleid').hide();"> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobile_checktype]" value="0"<?php echo (!$member_setting['mobile_checktype']) ? ' checked' : ''?> onclick="$('#sendsms_titleid').hide();"> <?php echo L('no');?> <span></span></label>
                            </div>
                            <label><a class="btn btn-sm red" href="javascript:;" layuimini-content-href="?m=sms&c=sms&a=sms_setting&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="<?php echo L('短信平台配置');?>" data-icon="fa fa-cog"> <i class="fa fa-envelope"></i> <?php echo L('短信平台配置');?> </a></label>
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-group" id="sendsms_titleid" <?php if($member_setting['mobile_checktype']!='1'){?> style="display: none; " <?php }?>>
                        <label class="col-md-2 control-label"><?php echo L('user_sendsms_title')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="user_sendsms_title" name="info[user_sendsms_title]" value="<?php echo $member_setting['user_sendsms_title'];?>" ></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('register_verify')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[registerverify]" value="1" <?php echo $member_setting['registerverify'] ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_defualtpoint">
                        <label class="col-md-2 control-label"><?php echo L('defualtpoint')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="defualtpoint" name="info[defualtpoint]" value="<?php echo $member_setting['defualtpoint'];?>" >
                            <span class="help-block"><?php echo L('defualtpoint').L('between_1_to_8_num')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_defualtamount">
                        <label class="col-md-2 control-label"><?php echo L('defualtamount')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="defualtamount" name="info[defualtamount]" value="<?php echo $member_setting['defualtamount'];?>" >
                            <span class="help-block"><?php echo L('defualtamount').L('between_1_to_8_num')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('不允许账号的字符串')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" style="height:150px; width:100%;" name="info[notallow]"><?php echo $member_setting['notallow']?></textarea>
                            <span class="help-block"><?php echo L('设置不允许包含在账号中的字符串，多个字符串以逗号“,”分隔')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('账号最小长度')?></label>
                        <div class="col-md-9">
                            <div class="input-small"><input class="form-control" id="dr_username" type="text" name="info[config][userlen]" value="<?php echo intval($member_setting['config']['userlen'])?>">
                                <script type="text/javascript">
                                    $(function(){
                                        $("#dr_username").TouchSpin({
                                            buttondown_class: "btn default",
                                            buttonup_class: "btn default",
                                            verticalbuttons: false,
                                            step: 1,
                                            min: 0,
                                            max: 50
                                        });
                                    });
                                </script></div>
                            <span class="help-block"><?php echo L('账号名的最小长度控制，0表示不限制')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('账号最大长度')?></label>
                        <div class="col-md-9">
                            <div class="input-small"><input class="form-control" id="dr_usernamemax" type="text" name="info[config][userlenmax]" value="<?php echo intval($member_setting['config']['userlenmax'])?>">
                                <script type="text/javascript">
                                    $(function(){
                                        $("#dr_usernamemax").TouchSpin({
                                            buttondown_class: "btn default",
                                            buttonup_class: "btn default",
                                            verticalbuttons: false,
                                            step: 1,
                                            min: 0,
                                            max: 50
                                        });
                                    });
                                </script></div>
                            <span class="help-block"><?php echo L('账号名的最大长度控制，最大50个字符，0表示不限制')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('密码最小长度')?></label>
                        <div class="col-md-9">
                            <div class="input-small"><input class="form-control" id="dr_password" type="text" name="info[config][pwdlen]" value="<?php echo intval($member_setting['config']['pwdlen'])?>">
                                <script type="text/javascript">
                                    $(function(){
                                        $("#dr_password").TouchSpin({
                                            buttondown_class: "btn default",
                                            buttonup_class: "btn default",
                                            verticalbuttons: false,
                                            step: 1,
                                            min: 0,
                                            max: 50
                                        });
                                    });
                                </script></div>
                            <span class="help-block"><?php echo L('密码的最小长度控制，最大设置50位数')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('密码最大长度')?></label>
                        <div class="col-md-9">
                            <div class="input-small"><input class="form-control" id="dr_passwordmax" type="text" name="info[config][pwdmax]" value="<?php echo intval($member_setting['config']['pwdmax'])?>">
                                <script type="text/javascript">
                                    $(function(){
                                        $("#dr_passwordmax").TouchSpin({
                                            buttondown_class: "btn default",
                                            buttonup_class: "btn default",
                                            verticalbuttons: false,
                                            step: 1,
                                            min: 0,
                                            max: 50
                                        });
                                    });
                                </script></div>
                            <span class="help-block"><?php echo L('密码的最大长度控制，最大设置50位数')?></span>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('允许账号与密码相同')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="info[config][user2pwd]" value="1"<?php echo $member_setting['config']['user2pwd'] ? ' checked' : ''?> data-on-text="<?php echo L('允许')?>" data-off-text="<?php echo L('禁止')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('针对前端注册或修改密码时的验证')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('密码强度（正则）')?></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input class="form-control" type="text" name="info[config][pwdpreg]" value="<?php echo $member_setting['config']['pwdpreg']?>">
                                <span class="input-group-btn">
                                    <button class="btn blue" onclick="dr_iframe_show('<?php echo L('正则表达式')?>', '?m=member&c=member_setting&a=public_test_pattern')" type="button"><?php echo L('测试')?></button>
                                </span>
                            </div>
                            <span class="help-block"><?php echo L('针对前端注册或修改密码时的强度验证，可以设置自定义正则表达式，例如数字正则表达式格式：/^[0-9]+$/')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('账号规则（正则）')?></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <input class="form-control" type="text" name="info[config][preg]" value="<?php echo $member_setting['config']['preg']?>">
                                <span class="input-group-btn">
                                    <button class="btn blue" onclick="dr_iframe_show('<?php echo L('正则表达式')?>', '?m=member&c=member_setting&a=public_test_pattern')" type="button"><?php echo L('测试')?></button>
                                </span>
                            </div>
                            <span class="help-block"><?php echo L('针对前端注册时的账号格式验证，可以设置自定义正则表达式，例如数字正则表达式格式：/^[0-9]+$/')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">
                <div class="form-body">

                    <div class="table-list">
                        <table class="table table-striped table-bordered table-hover table-checkable dataTable">
                            <thead>
                            <tr class="heading">
                                <th class="myselect">
                                    <?php echo L('显示');?>
                                </th>
                                <th width="180"> <?php echo L('字段');?> </th>
                                <th width="150"> <?php echo L('名称');?> </th>
                                <th width="100"> <?php echo L('宽度');?> </th>
                                <th width="140"> <?php echo L('对其方式');?> </th>
                                <th> <?php echo L('回调方法');?> </th>
                            </tr>
                            </thead>
                            <tbody class="field-sort-items">
                            <?php 
                            if(is_array($field)){
                            foreach($field as $n=>$t){
                            if ($t['field']) {
                            ?>
                            <tr class="odd gradeX">
                                <td class="myselect">
                                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" name="setting[list_field][<?php echo $t['field'];?>][use]" value="1" <?php if ($data['setting']['list_field'][$t['field']]['use']){?> checked<?php }?> />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo L($t['name']);?> (<?php echo $t['field'];?>)</td>
                                <td><input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][name]" value="<?php echo $data['setting']['list_field'][$t['field']]['name'] ? htmlspecialchars($data['setting']['list_field'][$t['field']]['name']) : $t['name'];?>" /></td>
                                <td> <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][width]" value="<?php echo htmlspecialchars((string)$data['setting']['list_field'][$t['field']]['width']);?>" /></td>
                                <td><input type="checkbox" name="setting[list_field][<?php echo $t['field'];?>][center]" <?php if ($data['setting']['list_field'][$t['field']]['center']){?> checked<?php }?> value="1"  data-on-text="<?php echo L('居中');?>" data-off-text="<?php echo L('默认');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                </td>
                                <td> <div class="input-group" style="width:250px">
                                        <span class="input-group-btn">
                                            <a class="btn btn-success" href="javascript:help('?m=content&c=sitemodel&a=public_help&pc_hash='+pc_hash);"><?php echo L('回调');?></a>
                                        </span>
                                    <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][func]" value="<?php echo htmlspecialchars((string)$data['setting']['list_field'][$t['field']]['func']);?>" />
                                </div></td>
                            </tr>
                            <?php }}}?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript">
$(function() {
    $(".field-sort-items").sortable();
});
</script>
<script src="<?php echo JS_PATH;?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH;?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script>
layui.use(['layer', 'miniTab'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniTab = layui.miniTab;
    miniTab.listen();
});
</script>
</div>
</div>
</body>
</html>