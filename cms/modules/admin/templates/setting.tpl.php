<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
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
<input name="pc_hash" type="hidden" value="<?php echo dr_get_csrf_token();?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_basic_cfg').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('setting_basic_cfg');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_safe_cfg').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-shield"></i> <?php if (is_pc()) {echo L('setting_safe_cfg');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_mail_cfg').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-envelope-open"></i> <?php if (is_pc()) {echo L('setting_mail_cfg');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_connect').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-html5"></i> <?php if (is_pc()) {echo L('setting_connect');}?> </a>
            </li>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="$('#dr_page').val('4')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_keyword_enable').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-tags"></i> <?php if (is_pc()) {echo L('setting_keyword_enable');}?> </a>
            </li>
            <li<?php if ($page==5) {?> class="active"<?php }?>>
                <a data-toggle="tab_5" onclick="$('#dr_page').val('5')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_confound').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-th-large"></i> <?php if (is_pc()) {echo L('setting_confound');}?> </a>
            </li>
            <li<?php if ($page==6) {?> class="active"<?php }?>>
                <a data-toggle="tab_6" onclick="$('#dr_page').val('6')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('setting_jump').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-link"></i> <?php if (is_pc()) {echo L('setting_jump');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_bdmap_api')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="bdmap_api" name="setconfig[bdmap_api]" value="<?php echo $bdmap_api;?>" ></label>
                            <label><a class="btn btn-sm blue" href="http://lbsyun.baidu.com/apiconsole/center" target="_blank"> <?php echo L('setting_apply_immediately');?> </a></label>
                            <span class="help-block"><?php echo L('setting_bdmap_api_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_sys_admin_pagesize">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_pagesize')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="sys_admin_pagesize" name="setconfig[sys_admin_pagesize]" value="<?php echo $sys_admin_pagesize;?>" >
                            <span class="help-block"><?php echo L('setting_admin_pagesize_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_admin_email">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_email')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_admin_email" name="setting[admin_email]" value="<?php echo $admin_email;?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_category_ajax')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="category_ajax" name="setting[category_ajax]" value="<?php echo $category_ajax;?>" >
                            <span class="help-block"><?php echo L('setting_category_ajax_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_tpl_edit')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setconfig[tpl_edit]" value="1" <?php echo $tpl_edit ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_gzip')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setconfig[gzip]" value="1" <?php echo $gzip ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('editormode')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_editor]" value="0"<?php echo (!$sys_editor) ? ' checked' : ''?>> <?php echo L('UEditor');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_editor]" value="1"<?php echo ($sys_editor) ? ' checked' : ''?>> <?php echo L('CKEditor');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_site_theme')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[site_theme]" value="0"<?php echo (!$site_theme) ? ' checked' : ''?>> <?php echo L('setting_website_resources');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[site_theme]" value="1"<?php echo ($site_theme) ? ' checked' : ''?>> <?php echo L('setting_remote_address');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_js_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_js_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_js_path" name="setconfig[js_path]" value="<?php echo $js_path;?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_css_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_css_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_css_path" name="setconfig[css_path]" value="<?php echo $css_path;?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_img_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_img_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_img_path" name="setconfig[img_path]" value="<?php echo $img_path;?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_mobile_js_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_mobile_js_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_mobile_js_path" name="setconfig[mobile_js_path]" value="<?php echo $mobile_js_path;?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_mobile_css_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_mobile_css_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_mobile_css_path" name="setconfig[mobile_css_path]" value="<?php echo $mobile_css_path;?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_mobile_img_path">
                        <label class="col-md-2 control-label"><?php echo L('setting_mobile_img_path')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dr_mobile_img_path" name="setconfig[mobile_img_path]" value="<?php echo $mobile_img_path;?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_timezone')?></label>
                        <div class="col-md-9">
                            <label><select class="form-control" name="setconfig[timezone]">
                                <option value=""> -- </option>
                                <option value="-12"<?php echo ($timezone=='-12') ? ' selected' : ''?>>(GMT -12:00)</option>
                                <option value="-11"<?php echo ($timezone=='-11') ? ' selected' : ''?>>(GMT -11:00)</option>
                                <option value="-10"<?php echo ($timezone=='-10') ? ' selected' : ''?>>(GMT -10:00)</option>
                                <option value="-9"<?php echo ($timezone=='-9') ? ' selected' : ''?>>(GMT -09:00)</option>
                                <option value="-8"<?php echo ($timezone=='-8') ? ' selected' : ''?>>(GMT -08:00)</option>
                                <option value="-7"<?php echo ($timezone=='-7') ? ' selected' : ''?>>(GMT -07:00)</option>
                                <option value="-6"<?php echo ($timezone=='-6') ? ' selected' : ''?>>(GMT -06:00)</option>
                                <option value="-5"<?php echo ($timezone=='-5') ? ' selected' : ''?>>(GMT -05:00)</option>
                                <option value="-4"<?php echo ($timezone=='-4') ? ' selected' : ''?>>(GMT -04:00)</option>
                                <option value="-3.5"<?php echo ($timezone=='-3.5') ? ' selected' : ''?>>(GMT -03:30)</option>
                                <option value="-3"<?php echo ($timezone=='-3') ? ' selected' : ''?>>(GMT -03:00)</option>
                                <option value="-2"<?php echo ($timezone=='-2') ? ' selected' : ''?>>(GMT -02:00)</option>
                                <option value="-1"<?php echo ($timezone=='-1') ? ' selected' : ''?>>(GMT -01:00)</option>
                                <option value="0"<?php echo ($timezone=='0') ? ' selected' : ''?>>(GMT)</option>
                                <option value="1"<?php echo ($timezone=='1') ? ' selected' : ''?>>(GMT +01:00)</option>
                                <option value="2"<?php echo ($timezone=='2') ? ' selected' : ''?>>(GMT +02:00)</option>
                                <option value="3"<?php echo ($timezone=='3') ? ' selected' : ''?>>(GMT +03:00)</option>
                                <option value="3.5"<?php echo ($timezone=='3.5') ? ' selected' : ''?>>(GMT +03:30)</option>
                                <option value="4"<?php echo ($timezone=='4') ? ' selected' : ''?>>(GMT +04:00)</option>
                                <option value="4.5"<?php echo ($timezone=='4.5') ? ' selected' : ''?>>(GMT +04:30)</option>
                                <option value="5"<?php echo ($timezone=='5') ? ' selected' : ''?>>(GMT +05:00)</option>
                                <option value="5.5"<?php echo ($timezone=='5.5') ? ' selected' : ''?>>(GMT +05:30)</option>
                                <option value="5.75"<?php echo ($timezone=='6') ? ' selected' : ''?>>(GMT +05:45)</option>
                                <option value="6"<?php echo ($timezone=='6.5') ? ' selected' : ''?>>(GMT +06:00)</option>
                                <option value="6.5"<?php echo ($timezone=='7') ? ' selected' : ''?>>(GMT +06:30)</option>
                                <option value="7"<?php echo ($timezone=='7.5') ? ' selected' : ''?>>(GMT +07:00)</option>
                                <option value="8"<?php echo ($timezone=='' || $timezone=='8') ? ' selected' : ''?>>(GMT +08:00)</option>
                                <option value="9"<?php echo ($timezone=='9') ? ' selected' : ''?>>(GMT +09:00)</option>
                                <option value="9.5"<?php echo ($timezone=='9.5') ? ' selected' : ''?>>(GMT +09:30)</option>
                                <option value="10"<?php echo ($timezone=='10') ? ' selected' : ''?>>(GMT +10:00)</option>
                                <option value="11"<?php echo ($timezone=='11') ? ' selected' : ''?>>(GMT +11:00)</option>
                                <option value="12"<?php echo ($timezone=='12') ? ' selected' : ''?>>(GMT +12:00)</option>
                            </select></label>
                            <span class="help-block"><?php echo L('setting_timezone_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_time_format')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="sys_time_format" name="setconfig[sys_time_format]" value="<?php echo $sys_time_format;?>" >
                            <span class="help-block"><?php echo L('setting_time_format_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_time')?></label>
                        <div class="col-md-9">
                            <p class="form-control-static" id="site_time"><?php echo dr_date(SYS_TIME);?></p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_go_404')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_go_404]" value="1" type="radio" <?php echo ($sys_go_404) ? ' checked' : ''?>> <?php echo L('open')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_go_404]" value="0" type="radio" <?php echo (!$sys_go_404) ? ' checked' : ''?>> <?php echo L('close')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_go_404_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_address_mode')?></label>
                        <div class="col-md-9">
                            <?php if (defined('IS_NOT_301')) {?>
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_301]" value="0" type="radio" disabled> <?php echo L('setting_unique_address')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_301]" value="1" type="radio" disabled checked> <?php echo L('setting_free_parameter')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_index_address_mode_desc')?></span>
                            <?php } else {?>
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_301]" value="0" type="radio" <?php echo (!$sys_301) ? ' checked' : ''?> onclick="$('.dr_url_only').show()"> <?php echo L('setting_unique_address')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_301]" value="1" type="radio" <?php echo ($sys_301) ? ' checked' : ''?> onclick="$('.dr_url_only').hide()"> <?php echo L('setting_free_parameter')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_address_mode_desc')?></span>
                            <?php }?>
                        </div>
                    </div>
                    <div class="form-group dr_url_only"<?php echo ($sys_301) ? ' style="display:none"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('setting_address_rule')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_url_only]" value="0" type="radio" <?php echo (!$sys_url_only) ? ' checked' : ''?>> <?php echo L('setting_fuzzy_matching')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[sys_url_only]" value="1" type="radio" <?php echo ($sys_url_only) ? ' checked' : ''?>> <?php echo L('setting_accurate_matching')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_address_rule_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_csrf')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_csrf]" value="2"<?php echo $sys_csrf==2 ? ' checked' : ''?> /> <?php echo L('strict_mode')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_csrf]" value="1"<?php echo $sys_csrf==1 ? ' checked' : ''?> /> <?php echo L('loose_pattern')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_csrf]" value="0"<?php echo empty($sys_csrf) ? ' checked' : ''?> /> <?php echo L('close')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_csrf_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_csrf_time')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_csrf_time]" value="1"<?php echo $sys_csrf_time ? ' checked' : ''?> /> <?php echo L('every_time')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setconfig[sys_csrf_time]" value="0"<?php echo empty($sys_csrf_time) ? ' checked' : ''?> /> <?php echo L('generated_periodically')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_csrf_time_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('need_check_come_url')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setconfig[needcheckcomeurl]" value="1" <?php echo $needcheckcomeurl ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <?php if(cleck_admin(param::get_session('roleid')) && dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_founders')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="admin_founders" name="setconfig[admin_founders]" value="<?php echo $admin_founders;?>" >
                            <span class="help-block"><?php echo L('setting_admin_founders_desc')?></span>
                        </div>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_debug')?></label>
                        <div class="col-md-9">
                            <?php if (IS_DEV) {?>
                            <input type="checkbox" name="setconfig[debug]" value="1" checked disabled data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('setting_debug_desc')?></span>
                            <?php } else {?>
                            <input type="checkbox" name="setconfig[debug]" value="1" <?php echo $debug ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('setting_admin_debug_desc')?></span>
                            <?php }?>    
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_log')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setconfig[admin_log]" value="1" <?php echo $admin_log ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_code')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincode]" value="0" type="radio" <?php echo (!$sysadmincode) ? ' checked' : ''?> onclick="dr_code(this);"> <?php echo L('open')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincode]" value="1" type="radio" <?php echo ($sysadmincode) ? ' checked' : ''?> onclick="dr_code(this);"> <?php echo L('close')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group<?php echo ($sysadmincode) ? ' hidden' : ''?>" id="sysadmincodemodel">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_code_model')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodemodel]" value="0" type="radio" <?php echo (!$sysadmincodemodel) ? ' checked' : ''?> onclick="$('#dr_row_captcha_charset').addClass('hidden');$('#sysadmincodevoicemodel').addClass('hidden');"> <?php echo L('setting_confusion')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodemodel]" value="1" type="radio" <?php echo ($sysadmincodemodel==1) ? ' checked' : ''?> onclick="$('#dr_row_captcha_charset').addClass('hidden');$('#sysadmincodevoicemodel').addClass('hidden');"> <?php echo L('setting_digital')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodemodel]" value="2" type="radio" <?php echo ($sysadmincodemodel==2) ? ' checked' : ''?> onclick="$('#dr_row_captcha_charset').addClass('hidden');$('#sysadmincodevoicemodel').removeClass('hidden');"> <?php echo L('setting_letters')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodemodel]" value="3" type="radio" <?php echo ($sysadmincodemodel==3) ? ' checked' : ''?> onclick="$('#dr_row_captcha_charset').removeClass('hidden');$('#sysadmincodevoicemodel').addClass('hidden');"> <?php echo L('setting_character')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group<?php echo ($sysadmincode || $sysadmincodemodel=='0' || $sysadmincodemodel=='1' || $sysadmincodemodel=='2') ? ' hidden' : ''?>" id="dr_row_captcha_charset">
                        <label class="col-md-2 control-label"><?php echo L('setting_code_character')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_captcha_charset" name="setting[captcha_charset]" value="<?php echo $captcha_charset;?>" >
                        </div>
                    </div>
                    <div class="form-group<?php echo ($sysadmincode || $sysadmincodemodel=='0' || $sysadmincodemodel=='1' || $sysadmincodemodel=='3') ? ' hidden' : ''?>" id="sysadmincodevoicemodel">
                        <label class="col-md-2 control-label"><?php echo L('setting_voice_model')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodevoicemodel]" value="0" type="radio" <?php echo (!$sysadmincodevoicemodel) ? ' checked' : ''?>> <?php echo L('setting_voice_default')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodevoicemodel]" value="1" type="radio" <?php echo ($sysadmincodevoicemodel==1) ? ' checked' : ''?>> <?php echo L('setting_voice_girl')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[sysadmincodevoicemodel]" value="2" type="radio" <?php echo ($sysadmincodevoicemodel==2) ? ' checked' : ''?>> <?php echo L('setting_voice_boy')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group<?php echo ($sysadmincode) ? ' hidden' : ''?>" id="dr_row_sysadmincodelen">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_code_len')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="setting[sysadmincodelen]" id="dr_sysadmincodelen" value="<?php echo intval($sysadmincodelen) ? intval($sysadmincodelen) : 4;?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('setting_code_position')?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_mobile_login')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_sms_login]" value="1" <?php echo ($admin_sms_login) ? ' checked' : ''?> /> <?php echo L('open')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_sms_login]" value="0" <?php echo (!$admin_sms_login) ? ' checked' : ''?> /> <?php echo L('close')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_admin_mobile_login_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_admin_mobile_check')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_sms_check]" value="1" <?php echo ($admin_sms_check) ? ' checked' : ''?> /> <?php echo L('open')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_sms_check]" value="0" <?php echo (!$admin_sms_check) ? ' checked' : ''?> /> <?php echo L('close')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_admin_mobile_check_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_maxloginfailedtimes')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="setting[maxloginfailedtimes]" id="maxloginfailedtimes" value="<?php echo intval($maxloginfailedtimes);?>" class="form-control">
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
                                    <input type="text" name="setting[sysadminlogintimes]" id="sysadminlogintimes" value="<?php echo intval($sysadminlogintimes);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('minutes')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('setting_time_limit_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group<?php if (!function_exists('openssl_decrypt')) {echo ' hidden';}?>">
                        <label class="col-md-2 control-label"><?php echo L('login_password_mode')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_login_aes]" value="0" <?php echo (!$admin_login_aes) ? ' checked' : ''?> /> <?php echo L('MD5')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[admin_login_aes]" value="1" <?php echo ($admin_login_aes) ? ' checked' : ''?> /> <?php echo L('AES(128)')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php if (!function_exists('openssl_decrypt')) {echo L('login_password_mode_desc');}?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_cookie')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="cookie_pre" name="setconfig[cookie_pre]" value="<?php echo $cookie_pre ? '************' : '';?>" ></label>
                            <label><button class="btn btn-sm blue" type="button" name="button" onclick="to_cookie()"> <i class="fa fa-refresh"></i> <?php echo L('setting_regenerate')?> </button></label>
                            <span class="help-block"><?php echo L('setting_cookie_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_keys')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="auth_key" name="setconfig[auth_key]" value="<?php echo $auth_key ? '************' : '';?>" ></label>
                            <label><button class="btn btn-sm blue" type="button" name="button" onclick="to_key()"> <i class="fa fa-refresh"></i> <?php echo L('setting_regenerate')?> </button></label>
                            <span class="help-block"><?php echo L('setting_keys_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_pwd_safe')?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[pwd_use][]" value="admin" type="checkbox" <?php if (dr_in_array('admin', $pwd_use)) {echo ' checked';}?>> <?php echo L('setting_admin')?> <span></span></label>
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[pwd_use][]" value="member" type="checkbox" <?php if (dr_in_array('member', $pwd_use)) {echo ' checked';}?>> <?php echo L('setting_member')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_safe_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pwd_is_edit')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setting[pwd_is_edit]" value="1" <?php echo $pwd_is_edit ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pwd_day_edit')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="setting[pwd_day_edit]" id="pwd_day_edit" value="<?php echo $pwd_day_edit;?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('pwd_day')?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pwd_is_login_edit')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setting[pwd_is_login_edit]" value="1" <?php echo $pwd_is_login_edit ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('pwd_is_login_edit_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pwd_is_rlogin_edit')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setting[pwd_is_rlogin_edit]" value="1" <?php echo $pwd_is_rlogin_edit ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('pwd_is_rlogin_edit_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_login_safe')?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[login_use][]" value="admin" type="checkbox" <?php if (dr_in_array('admin', $login_use)) {echo ' checked';}?>> <?php echo L('setting_admin')?> <span></span></label>
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[login_use][]" value="member" type="checkbox" <?php if (dr_in_array('member', $login_use)) {echo ' checked';}?>> <?php echo L('setting_member')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_safe_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('login_is_option')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setting[login_is_option]" value="1" <?php echo $login_is_option ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('login_is_option_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('login_exit_time')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="setting[login_exit_time]" id="login_exit_time" value="<?php echo $login_exit_time;?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('minutes')?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('login_city')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[login_city]" value="0"<?php echo (!$login_city) ? ' checked' : ''?>> <?php echo L('yes_city');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[login_city]" value="1"<?php echo ($login_city) ? ' checked' : ''?>> <?php echo L('no_city');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('login_city_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('login_llq')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[login_llq]" value="0"<?php echo (!$login_llq) ? ' checked' : ''?>> <?php echo L('yes_llq');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[login_llq]" value="1"<?php echo ($login_llq) ? ' checked' : ''?>> <?php echo L('no_llq');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('login_llq_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_safe')?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[safe_use][]" value="admin" type="checkbox" <?php if (dr_in_array('admin', $safe_use)) {echo ' checked';}?>> <?php echo L('setting_admin')?> <span></span></label>
                                <label class="mt-checkbox mt-checkbox-outline"><input name="setting[safe_use][]" value="member" type="checkbox" <?php if (dr_in_array('member', $safe_use)) {echo ' checked';}?>> <?php echo L('setting_member')?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('setting_safe_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('long_time_lock')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input type="text" name="setting[safe_wdl]" id="safe_wdl" value="<?php echo intval($safe_wdl);?>" class="form-control">
                                    <span class="input-group-addon">
                                        <?php echo L('pwd_day')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('long_time_lock_desc')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mail_type')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setting[mail_type]" value="1" onclick="showsmtp(this,'smtpcfg')" type="radio" <?php echo $mail_type ? ' checked' : ''?>> <?php echo L('mail_type_smtp')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setting[mail_type]" value="0" onclick="showsmtp(this,'smtpcfg')" type="radio" <?php echo !$mail_type ? ' checked' : ''?>> <?php echo L('mail_type_mail')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group smtpcfg<?php if(!$mail_type) echo ' hidden'?>">
                        <label class="col-md-2 control-label"><?php echo L('mail_server')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mail_server" name="setting[mail_server]" value="<?php echo $mail_server;?>" >
                        </div>
                    </div>
                    <div class="form-group smtpcfg<?php if(!$mail_type) echo ' hidden'?>">
                        <label class="col-md-2 control-label"><?php echo L('mail_port')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mail_port" name="setting[mail_port]" value="<?php echo $mail_port;?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mail_from')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mail_from" name="setting[mail_from]" value="<?php echo $mail_from;?>" >
                        </div>
                    </div>
                    <div class="form-group smtpcfg<?php if(!$mail_type) echo ' hidden'?>">
                        <label class="col-md-2 control-label"><?php echo L('mail_auth')?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="setting[mail_auth]" value="1" <?php echo $mail_auth ? ' checked' : ''?> data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                        </div>
                    </div>
                    <div class="form-group smtpcfg<?php if(!$mail_type) echo ' hidden'?>">
                        <label class="col-md-2 control-label"><?php echo L('mail_user')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mail_user" name="setting[mail_user]" value="<?php echo $mail_user;?>" >
                        </div>
                    </div>
                    <div class="form-group smtpcfg<?php if(!$mail_type) echo ' hidden'?>">
                        <label class="col-md-2 control-label"><?php echo L('mail_password')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mail_password" name="setting[mail_password]" value="<?php echo $mail_password ? '******' : '';?>" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mail_test')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="mail_to" name="mail_to" value="" ></label>
                            <label><a id="dr_sending" href="javascript:;" onclick="test_mail()" class="btn btn-sm blue"> <i class="fa fa-send"></i> <?php echo L('mail_test_send')?> </a></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_connect_sina')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon">APP key</span>
                                    <input type="text" id="sina_akey" name="setconfig[sina_akey]" value="<?php echo $sina_akey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon">APP secret key</span>
                                    <input type="text" id="sina_skey" name="setconfig[sina_skey]" value="<?php echo $sina_skey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <span class="help-block"><a class="btn btn-sm blue" href="https://open.weibo.com/connect" target="_blank"> <?php echo L('click_register');?> </a></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_connect_qq')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon">APP ID</span>
                                    <input type="text" id="qq_appid" name="setconfig[qq_appid]" value="<?php echo $qq_appid;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon">APP key</span>
                                    <input type="text" id="qq_appkey" name="setconfig[qq_appkey]" value="<?php echo $qq_appkey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_connect_qqcallback')?></span>
                                    <input type="text" id="qq_callback" name="setconfig[qq_callback]" value="<?php echo $qq_callback;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <span class="help-block"><a class="btn btn-sm blue" href="http://connect.qq.com" target="_blank"> <?php echo L('click_register');?> </a></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_keyword')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="0" type="radio"<?php echo ($keywordapi=='0') ? ' checked' : ''?> onclick="$('#baidu').addClass('hidden');$('#xunfei').addClass('hidden');"> <?php echo L('setting_default')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="1" type="radio"<?php echo ($keywordapi=='1') ? ' checked' : ''?> onclick="$('#baidu').removeClass('hidden');$('#xunfei').addClass('hidden');"> <?php echo L('setting_baidu')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input name="setconfig[keywordapi]" value="2" type="radio"<?php echo ($keywordapi=='2') ? ' checked' : ''?> onclick="$('#xunfei').removeClass('hidden');$('#baidu').addClass('hidden');"> <?php echo L('setting_xunfei')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_qcnum')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="baidu_qcnum" name="setconfig[baidu_qcnum]" value="<?php echo intval($baidu_qcnum);?>" >
                            <span class="help-block"><?php echo L('setting_qcnum_desc');?></span>
                        </div>
                    </div>
                    <div class="form-group<?php echo ($keywordapi=='2' || $keywordapi=='0') ? ' hidden' : ''?>" id="baidu">
                        <label class="col-md-2 control-label"><?php echo L('setting_baidu_enable')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_keyword_appid')?></span>
                                    <input type="text" id="baidu_aid" name="setconfig[baidu_aid]" value="<?php echo $baidu_aid;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_keyword_key')?></span>
                                    <input type="text" id="baidu_skey" name="setconfig[baidu_skey]" value="<?php echo $baidu_skey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_keyword_skey')?></span>
                                    <input type="text" id="baidu_arcretkey" name="setconfig[baidu_arcretkey]" value="<?php echo $baidu_arcretkey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <span class="help-block"><a class="btn btn-sm blue" href="https://console.bce.baidu.com/ai/#/ai/nlp/overview/index" target="_blank"> <?php echo L('setting_keyword_register');?> </a></span>
                            <span class="help-block"><?php echo L('setting_baidu_keyword')?></span>
                        </div>
                    </div>
                    <div class="form-group<?php echo ($keywordapi=='1' || $keywordapi=='0') ? ' hidden' : ''?>" id="xunfei">
                        <label class="col-md-2 control-label"><?php echo L('setting_xunfei_enable')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_keyword_appid')?></span>
                                    <input type="text" id="xunfei_aid" name="setconfig[xunfei_aid]" value="<?php echo $xunfei_aid;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('setting_keyword_key')?></span>
                                    <input type="text" id="xunfei_skey" name="setconfig[xunfei_skey]" value="<?php echo $xunfei_skey;?>" class="form-control" placeholder="">
                                </div>
                            </div>
                            <span class="help-block"><a class="btn btn-sm blue" href="https://console.xfyun.cn/services/ke" target="_blank"> <?php echo L('setting_keyword_register');?> </a></span>
                            <span class="help-block"><?php echo L('setting_xunfei_keyword')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_keyword_test')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-xlarge" type="text" id="mykw" name="data[mykw]" value="" ></label>
                            <label><a class="btn btn-sm red" href="javascript:iframe_show('<?php echo L('setting_keyword_word')?>', '?m=admin&c=setting&a=public_test_index&kw='+$('#mykw').val());"> <?php echo L('setting_keyword_participle')?> </a></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==5) {?> active<?php }?>" id="tab_5">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_confound')?></label>
                        <div class="col-md-9">
                            <textarea name='setting[downmix]' id='downmix' class="form-control" style="width:80%;height:120px;"><?php echo $downmix?></textarea>
                            <span class="help-block"><?php echo L('setting_confound_desc');?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==6) {?> active<?php }?>" id="tab_6">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('setting_whiteList')?></label>
                        <div class="col-md-9">
                            <textarea name='setting[whiteList]' id='whiteList' class="form-control" style="width:80%;height:120px;"><?php echo $whiteList?></textarea>
                            <span class="help-block"><?php echo L('setting_whiteList_desc');?></span>
                        </div>
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
</div>
</div>
<script type="text/javascript">
function showsmtp(obj,hiddenid){
    var status = $(obj).val();
    if(status == 1){
        $('.'+hiddenid).removeClass('hidden');
    } else {
        $('.'+hiddenid).addClass('hidden');
    }
}
function test_mail() {
    var mail_type = $('input[name="setting[mail_type]"]:checked').val();
    var mail_auth = $('input[name="setting[mail_auth]"]:checked').val();
    $("#dr_sending").html(" <i class='fa fa-send'></i> <?php echo L('发送中...');?>");
    $.ajax({type: "POST",dataType:"json", url: "?m=admin&c=setting&a=public_test_mail&mail_to="+$('#mail_to').val()+"&"+Math.random(), data: {mail_type:mail_type,mail_server:$('#mail_server').val(),mail_port:$('#mail_port').val(),mail_user:$('#mail_user').val(),mail_password:$('#mail_password').val(),mail_auth:mail_auth,mail_from:$('#mail_from').val(),<?php echo SYS_TOKEN_NAME;?>:$("#myform input[name='<?php echo SYS_TOKEN_NAME;?>']").val()},
        success: function(json) {
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            dr_tips(json.code, json.msg, -1);
            $("#dr_sending").html(" <i class='fa fa-send'></i> <?php echo L('发送测试');?>");
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, this, thrownError);
        }
    });
}
function dr_code(obj) {
    if($(obj).val() == 1) {
        $('#sysadmincodemodel').addClass('hidden');
        $('#dr_row_captcha_charset').addClass('hidden');
        $('#sysadmincodevoicemodel').addClass('hidden');
        $('#dr_row_sysadmincodelen').addClass('hidden');
    } else {
        $('#sysadmincodemodel').removeClass('hidden');
        if ($('input[name="setting[sysadmincodemodel]"]:checked').val()=="3") {
            $('#dr_row_captcha_charset').removeClass('hidden');
        }
        if ($("input[name='setting[sysadmincodemodel]']:checked").val()=="2") {
            $('#sysadmincodevoicemodel').removeClass('hidden');
        }
        $('#dr_row_sysadmincodelen').removeClass('hidden');
    }
}
function to_key() {
   $.get('?m=admin&c=setting&a=public_syskey&pc_hash='+pc_hash, function(data){
        $('#auth_key').val(data);
    });
}
function to_cookie() {
   $.get('?m=admin&c=setting&a=public_syskey&action=cookie_pre&pc_hash='+pc_hash, function(data){
        $('#cookie_pre').val(data);
    });
}
$(function() {
    setInterval(dr_site_time, 1000);
});
function dr_site_time() {
    $.ajax({
        type: "get",
        dataType: "json",
        url: "?m=admin&c=setting&a=public_site_time&pc_hash="+pc_hash,
        success: function(json) {
            $('#site_time').html(json.msg);
        }
    });
}
</script>
</body>
</html>