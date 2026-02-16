<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<?php echo load_js(JS_PATH.'jquery-ui/jquery-ui.js');?>
<?php echo load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');?>
<?php echo load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="?m=admin&c=site&a=add" class="form-horizontal" method="post" name="myform" id="myform">
<input name="dosubmit" type="hidden" value="1">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="pc_hash" type="hidden" value="<?php echo dr_get_csrf_token();?>">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('basic_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('basic_configuration');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('mobile_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-mobile"></i> <?php if (is_pc()) {echo L('mobile_configuration');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('seo_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-internet-explorer"></i> <?php if (is_pc()) {echo L('seo_configuration');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('release_point_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-send"></i> <?php if (is_pc()) {echo L('release_point_configuration');}?> </a>
            </li>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="$('#dr_page').val('4')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('template_style_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-html5"></i> <?php if (is_pc()) {echo L('template_style_configuration');}?> </a>
            </li>
            <li<?php if ($page==5) {?> class="active"<?php }?>>
                <a data-toggle="tab_5" onclick="$('#dr_page').val('5')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('site_att_config').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-folder"></i> <?php if (is_pc()) {echo L('site_att_config');}?> </a>
            </li>
            <li<?php if ($page==6) {?> class="active<?php if (SYS_EDITOR) {?> hide<?php }?>"<?php }else{?><?php if (SYS_EDITOR) {?> class="hide"<?php }}?>>
                <a data-toggle="tab_6" onclick="$('#dr_page').val('6')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('att_ueditor').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-edit"></i> <?php if (is_pc()) {echo L('att_ueditor');}?> </a>
            </li>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <li<?php if ($page==7) {?> class="active"<?php }?>>
                <a data-toggle="tab_7" onclick="$('#dr_page').val('7')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('extention_field').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-code"></i> <?php if (is_pc()) {echo L('extention_field');}?> </a>
            </li>
            <?php }?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group" id="dr_row_name">
                        <label class="col-md-2 control-label"><?php echo L('site_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="name" name="info[name]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_dirname">
                        <label class="col-md-2 control-label"><?php echo L('site_dirname')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="dirname" name="info[dirname]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_domain">
                        <label class="col-md-2 control-label"><?php echo L('site_domain')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="domain" name="info[domain]" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('html_home')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[ishtml]" value="1"> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[ishtml]" value="0" checked> <?php echo L('close');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_statu')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[site_close]" onclick="$('.dr_close_msg').hide()" value="0" checked> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[site_close]" onclick="$('.dr_close_msg').show()" value="1"> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('site_close_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group dr_close_msg">
                        <label class="col-md-2 control-label"><?php echo L('site_close_msg')?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" style="height:100px" name="info[site_close_msg]">网站升级中....</textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('access_mode')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilemode]" value="-1" onclick="$('.dr_zsy').hide();$('.dr_mode_0').hide();$('.dr_mode_1').hide();$('.dr_mode_2').show();" checked> <?php echo L('close_mode');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilemode]" value="0" onclick="$('.dr_zsy').show();$('.dr_mode_0').show();$('.dr_mode_1').hide();$('.dr_mode_2').hide();"> <?php echo L('directory_mode');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilemode]" value="1" onclick="$('.dr_zsy').show();$('.dr_mode_0').hide();$('.dr_mode_1').show();$('.dr_mode_2').hide();"> <?php echo L('domain_mode');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group dr_mode_2">
                        <label class="col-md-2 control-label"><?php echo L('self_adaption')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo L('self_adaption_desc')?></div>
                        </div>
                    </div>
                    <div id="dr_row_mobile_dirname" class="form-group dr_mode_0" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('mobile_dirname')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="mobile_dirname" name="info[mobile_dirname]" value="mobile" >
                        </div>
                    </div>
                    <div id="dr_row_mobile_domain" class="form-group dr_mode_1" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('mobile_domain')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="mobile_domain" name="info[mobile_domain]" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mobile_auto')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobileauto]" value="1"> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobileauto]" value="0" checked> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('mobile_auto_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group dr_zsy" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('html_mobile')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilehtml]" value="1"> <?php echo L('html_mobile_url');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobilehtml]" value="0" checked> <?php echo L('dynamic_address');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('html_mobile_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mobile_not_pad')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[not_pad]" value="1"> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[not_pad]" value="0" checked> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('mobile_not_pad_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('mobile_template')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo L('mobile_template_style')?></div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_title')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="site_title" name="info[site_title]" maxlength="255" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('keyword_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="keywords" name="info[keywords]" maxlength="255" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('description')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="description" name="info[description]" maxlength="255" value="" >
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <div class="form-body">

                    <div class="form-group" id="dr_row_release_point">
                        <label class="col-md-2 control-label"><?php echo L('release_point')?></label>
                        <div class="col-md-9">
                            <label><select name="info[release_point][]" id="release_point" multiple title="<?php echo L('ctrl_more_selected')?>">
                                <option value='' selected><?php echo L('not_use_the_publishers_some')?></option>
                                <?php if(is_array($release_point_list) && !empty($release_point_list)){
                                foreach($release_point_list as $v){?>
                                <option value="<?php echo $v['id']?>"><?php echo $v['name']?></option>
                                <?php }}?>
                            </select></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">
                <div class="form-body">

                    <div class="form-group" id="dr_row_template">
                        <label class="col-md-2 control-label"><?php echo L('style_name')?></label>
                        <div class="col-md-9">
                            <label><select name="template[]" id="template" multiple title="<?php echo L('ctrl_more_selected')?>" onchange="default_list()" ondblclick="default_list()">
                                <?php if(is_array($template_list)){
                                foreach ($template_list as $key=>$val){?>
                                <option value="<?php echo $val['dirname']?>"><?php echo $val['name']?></option>
                                <?php }}?>
                            </select></label>
                        </div>
                        <script type="text/javascript">
                        function default_list() {
                            var html = '';
                            var old = $('#default_style_input').val();
                            var checked = '';
                            $('#template option:selected').each(function(i,n){
                                if (old == $(n).val()) {
                                    checked = 'checked';
                                }
                                 html += '<div class="mt-radio-inline"><label class="mt-radio mt-radio-outline"><input type="radio" name="default_style_radio" value="'+$(n).val()+'" onclick="$(\'#default_style_input\').val(this.value);" '+checked+'> '+$(n).text()+' <span></span></label></div>';
                            });
                            if(!checked)  $('#default_style_input').val('0');
                            $('#default_style').html(html);
                        }
                        </script>
                    </div>
                    <div class="form-group" id="dr_row_default_style">
                        <label class="col-md-2 control-label"><?php echo L('default_style')?></label>
                        <div class="col-md-9">
                            <input type="hidden" name="info[default_style]" id="default_style_input" value="0">
                            <span id="default_style">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="default_style_radio" disabled> <span></span></label>
                                </div>
                            </span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==5) {?> active<?php }?>" id="tab_5">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_upload_maxsize')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="upload_maxsize" name="setting[upload_maxsize]" value="2" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="upload_allowext" name="setting[upload_allowext]" value="jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_gb_check')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $this->check_gd();?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_enable')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark_enable]" value="1" checked> <?php echo L('site_att_watermark_open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark_enable]" value="0"> <?php echo L('site_att_watermark_close');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_type')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[type]" value="0" onclick="dr_type(0)" checked> <?php echo L('site_att_photo');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[type]" value="1" onclick="dr_type(1)"> <?php echo L('site_att_text');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group dr_sy dr_sy_1">
                        <label class="col-md-2 control-label"><?php echo L('site_att_text_font')?></label>
                        <div class="col-md-9">
                            <?php if ($waterfile) {?>
                            <label><select class="form-control" name="setting[wm_font_path]" id="wm_font_path">
                                <?php foreach($waterfile as $t) {
                                if (strpos($t, '.ttf') !== false) {?>
                                <option<?php if ($t=='default.ttf') {?> selected=""<?php }?> value="<?php echo $t;?>"><?php echo $t;?></option>
                                <?php }}?>
                            </select></label>
                            <?php }?>
                            <label class="wm-fileupload-font"><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span><?php echo L('upload');?></span> <input type="file" name="file_data" title=""> </span> </label>
                            <span class="help-block"><?php echo L('site_att_text_font_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group dr_sy dr_sy_1">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_text')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="wm_text" name="setting[wm_text]" value="cms" >
                            <span class="help-block"><?php echo L('site_att_text_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group dr_sy dr_sy_1">
                        <label class="col-md-2 control-label"><?php echo L('site_att_text_size')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="wm_font_size" name="setting[wm_font_size]" value="" >
                            <span class="help-block"><?php echo L('site_att_text_size_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group dr_sy dr_sy_1">
                        <label class="col-md-2 control-label"><?php echo L('site_att_text_color')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="wm_font_color" name="setting[wm_font_color]" value="" >
                        </div>
                    </div>
                    <div class="form-group dr_sy dr_sy_0">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_img')?></label>
                        <div class="col-md-9">
                            <?php if ($waterfile) {?>
                            <label><select class="form-control" name="setting[wm_overlay_path]" id="wm_overlay_path">
                                <?php foreach($waterfile as $t) {
                                if (strpos($t, '.png') !== false) {?>
                                <option<?php if ($t=='default.png') {?> selected=""<?php }?> value="<?php echo $t;?>"><?php echo $t;?></option>
                                <?php }}?>
                            </select></label>
                            <?php }?>
                            <label class="wm-fileupload-img"><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span><?php echo L('upload');?></span> <input type="file" name="file_data" title=""> </span> </label>
                            <span class="help-block"><?php echo L('site_att_watermark_img_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_pct')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="wm_opacity" name="setting[wm_opacity]" value="100" >
                            <span class="help-block"><?php echo L('site_att_watermark_pct_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_quality')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="quality" name="setting[quality]" value="80" >
                            <span class="help-block"><?php echo L('site_att_watermark_quality_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_padding')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="wm_padding" name="setting[wm_padding]" value="0" placeholder="<?php echo L('px')?>" >
                            <span class="help-block"><?php echo L('site_att_watermark_padding_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_offset')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-small">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('site_att_watermark_hor_offset')?></span>
                                    <input type="text" name="setting[wm_hor_offset]" id="wm_hor_offset" value="" class="form-control" placeholder="<?php echo L('px')?>">
                                </div>
                            </div>
                            <div class="input-inline input-small">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('site_att_watermark_vrt_offset')?></span>
                                    <input type="text" name="setting[wm_vrt_offset]" id="wm_vrt_offset" value="" class="form-control" placeholder="<?php echo L('px')?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_photo')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('site_att_watermark_minwidth')?></span>
                                    <input type="text" name="setting[width]" id="width" value="" class="form-control" placeholder="<?php echo L('px')?>">
                                </div>
                            </div>
                            <div class="input-inline input-large">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo L('site_att_watermark_minheight')?></span>
                                    <input type="text" name="setting[height]" id="height" value="" class="form-control" placeholder="<?php echo L('px')?>">
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('site_att_watermark_photo_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_watermark_pos')?></label>
                        <div class="col-md-9">
                            <div class="btn-group c-3x3" data-toggle="buttons">
                                <?php foreach ($locate as $i=>$t) {?>
                                <label class="btn btn-default<?php if ($i == 'right-bottom') {?> active<?php }?><?php if (strpos($i, 'bottom')!==false) {?> btn2<?php }?>"><input type="radio" name="setting[locate]" value="<?php echo $i?>"<?php if ($i == 'right-bottom') {?> checked<?php }?> class="toggle"><?php echo L($t)?></label>
                                <?php }?>
                            </div>
                            <span class="help-block"><?php echo L('选择水印在图片中的位置')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('site_att_ueditor')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ueditor]" value="0" checked> <?php echo L('site_att_watermark_ueditor');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ueditor]" value="1"> <?php echo L('site_att_watermark_all');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('site_att_ueditor_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('缩略图水印')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[thumb]" value="0" checked> <?php echo L('按调用参数');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[thumb]" value="1"> <?php echo L('site_att_watermark_all');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('是否对缩略图函数thumb的图片进行强制水印')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-9">
                            <button type="button" onclick="dr_preview()" class="btn red btn-sm"> <i class="fa fa-photo"></i> <?php echo L('site_att_watermark_review');?></button>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==6) {?> active<?php }?>" id="tab_6">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_filename')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="filename" name="setting[filename]" value="{yyyy}/{mm}{dd}/{time}{rand:6}" >
                            <span class="help-block"><?php echo L('ueditor_filename_desc')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_image_max_size')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="imageMaxSize" name="setting[imageMaxSize]" value="2" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_image_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="imageAllowFiles" name="setting[imageAllowFiles]" value="png|jpg|jpeg|gif|bmp" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_catcher_max_size')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="catcherMaxSize" name="setting[catcherMaxSize]" value="2" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_catcher_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="catcherAllowFiles" name="setting[catcherAllowFiles]" value="png|jpg|jpeg|gif|bmp" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_video_max_size')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="videoMaxSize" name="setting[videoMaxSize]" value="100" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_video_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="videoAllowFiles" name="setting[videoAllowFiles]" value="flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_music_max_size')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="musicMaxSize" name="setting[musicMaxSize]" value="20" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_music_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="musicAllowFiles" name="setting[musicAllowFiles]" value="flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_file_max_size')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" id="fileMaxSize" name="setting[fileMaxSize]" value="50" >
                                    <span class="input-group-addon"><?php echo L('MB')?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_file_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="fileAllowFiles" name="setting[fileAllowFiles]" value="png|jpg|jpeg|gif|bmp|flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid|rar|zip|tar|gz|7z|bz2|cab|iso|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|md|xml" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_imagemanager_max_size')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="imageManagerListSize" name="setting[imageManagerListSize]" value="20" >
                        </div>
                    </div>
                    <div class="form-group" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_imagemanager_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="imageManagerAllowFiles" name="setting[imageManagerAllowFiles]" value="png|jpg|jpeg|gif|bmp" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_filemanager_max_size')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="fileManagerListSize" name="setting[fileManagerListSize]" value="20" >
                        </div>
                    </div>
                    <div class="form-group" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_filemanager_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="fileManagerAllowFiles" name="setting[fileManagerAllowFiles]" value="png|jpg|jpeg|gif|bmp|flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid|rar|zip|tar|gz|7z|bz2|cab|iso|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|md|xml" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_videomanager_max_size')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="videoManagerListSize" name="setting[videoManagerListSize]" value="20" >
                        </div>
                    </div>
                    <div class="form-group" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_videomanager_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="videoManagerAllowFiles" name="setting[videoManagerAllowFiles]" value="flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_musicmanager_max_size')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="musicManagerListSize" name="setting[musicManagerListSize]" value="20" >
                        </div>
                    </div>
                    <div class="form-group" style="display: none">
                        <label class="col-md-2 control-label"><?php echo L('ueditor_musicmanager_allow_ext')?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" id="musicManagerAllowFiles" name="setting[musicManagerAllowFiles]" value="flv|swf|mkv|avi|rm|rmvb|mpeg|mpg|ogg|ogv|mov|wmv|mp4|webm|mp3|wav|mid" >
                        </div>
                    </div>

                </div>
            </div>
            <?php if($forminfos && is_array($forminfos['base'])) {?>
            <div class="tab-pane<?php if ($page==7) {?> active<?php }?>" id="tab_7">
                <div class="form-body">
<?php
foreach($forminfos['base'] as $field=>$info) {
    if($info['isomnipotent']) continue;
    if($info['formtype']=='omnipotent') {
        foreach($forminfos['base'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
        foreach($forminfos['senior'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
    }
?>
                    <div class="form-group" id="dr_row_<?php echo $field?>">
                        <label class="col-md-2 control-label"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
                        <div class="col-md-9">
                            <?php echo $info['form']?>
                            <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
                        </div>
                    </div>
<?php }?>

                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>
</form>
</div>
</div>
</div>
<link rel="stylesheet" href="<?php echo JS_PATH?>ion-rangeslider/ion.rangeSlider.min.css">
<script src="<?php echo JS_PATH?>ion-rangeslider/ion.rangeSlider.min.js"></script>
<link href="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
<script type="text/javascript">
function dr_type(v) {
    $('.dr_sy').hide();
    $('.dr_sy_'+v).show();
}
function dr_preview() {
    var linkurl = '?m=admin&c=site&a=public_preview&setting[type]='+$('input[name="setting[type]"]:checked').val()+'&setting[wm_font_path]='+$('#wm_font_path').val()+'&setting[wm_text]='+$('#wm_text').val()+'&setting[wm_font_size]='+$('#wm_font_size').val()+'&setting[wm_font_color]='+$('#wm_font_color').val()+'&setting[wm_overlay_path]='+$('#wm_overlay_path').val()+'&setting[wm_opacity]='+$('#wm_opacity').val()+'&setting[quality]='+$('#quality').val()+'&setting[wm_padding]='+$('#wm_padding').val()+'&setting[wm_hor_offset]='+$('#wm_hor_offset').val()+'&setting[wm_vrt_offset]='+$('#wm_vrt_offset').val()+'&setting[width]='+$('#width').val()+'&setting[height]='+$('#height').val()+'&setting[locate]='+$('input[name="setting[locate]"]:checked').val();
    if (typeof pc_hash == 'string') linkurl += (linkurl.indexOf('?') > -1 ? '&': '?') + 'pc_hash=' + pc_hash;
    if (linkurl.toLowerCase().indexOf("http://") != -1 || linkurl.toLowerCase().indexOf("https://") != -1) {
    } else {
        linkurl = geturlpathname()+linkurl;
    }
    var width = '50%';
    var height = '60%';
    if (is_mobile()) {
        width = height = '100%';
    }
    if (width=='100%' && height=='100%') {
        var drag = false;
    } else {
        var drag = true;
    }
    var diag = new Dialog({
        id:'preview',
        title:'水印预览',
        html:'<div style="text-align:center"><img style="max-width: 400px;width: 100%;-webkit-user-select: none;" src="'+linkurl+'"></div>',
        width:width,
        height:height,
        modal:true,
        draggable:drag
    });
    diag.onOk = function(){
        diag.close();
    };
    diag.show();
}
$(function(){
    $("#wm_font_color").minicolors({
        control: $(this).attr('data-control') || 'hue',
        defaultValue: $(this).attr('data-defaultValue') || '',
        inline: $(this).attr('data-inline') === 'true',
        letterCase: $(this).attr('data-letterCase') || 'lowercase',
        opacity: $(this).attr('data-opacity'),
        position: $(this).attr('data-position') || 'bottom left',
        change: function(hex, opacity) {
            if (!hex) return;
            if (opacity) hex += ', ' + opacity;
            if (typeof console === 'object') {
                console.log(hex);
            }
        },
        theme: 'bootstrap'
    });
    $('.dr_close_msg').hide();
    dr_type(0);
    $("#wm_opacity").ionRangeSlider({
        grid: true,
        min: 1,
        max: 100,
        from: 100
    });
    $("#quality").ionRangeSlider({
        grid: true,
        min: 1,
        max: 100,
        from: 80
    });
    // 初始化上传组件
    $('.wm-fileupload-font').fileupload({
        disableImageResize: false,
        autoUpload: true,
        maxFileSize: 2,
        url: '?m=admin&c=site&a=public_upload_index&at=font',
        dataType: 'json',
        formData : {
            '<?php echo SYS_TOKEN_NAME;?>': '<?php echo csrf_hash();?>',
        },
        progressall: function (e, data) {
            // 上传进度条 all
            var progress = parseInt(data.loaded / data.total * 100, 10);
            layer.msg(progress+'%');
        },
        add: function (e, data) {
            data.submit();
        },
        done: function (e, data) {
            //console.log($(this).html());
            dr_tips(data.result.code, data.result.msg);
            if (data.result.code) {
                setTimeout("window.location.reload(true)", 2000);
            }

        },
    });
    // 初始化上传组件
    $('.wm-fileupload-img').fileupload({
        disableImageResize: false,
        autoUpload: true,
        maxFileSize: 2,
        url: '?m=admin&c=site&a=public_upload_index&at=img',
        dataType: 'json',
        formData : {
            '<?php echo SYS_TOKEN_NAME;?>': '<?php echo csrf_hash();?>',
        },
        progressall: function (e, data) {
            // 上传进度条 all
            var progress = parseInt(data.loaded / data.total * 100, 10);
            layer.msg(progress+'%');
        },
        add: function (e, data) {
            data.submit();
        },
        done: function (e, data) {
            //console.log($(this).html());
            dr_tips(data.result.code, data.result.msg);
            if (data.result.code) {
                setTimeout("window.location.reload(true)", 2000);
            }

        },
    });
});
</script>
</body>
</html>