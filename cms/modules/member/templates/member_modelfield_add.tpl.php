<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header','admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> 菜单 <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <li><a class="tooltips" href="?m=member&c=member_modelfield&a=manage&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_manage');?>"><i class="fa fa-code"></i> <?php echo L('member_modelfield_manage');?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips on" href="<?php echo dr_now_url();?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_add');?>"><i class="fa fa-plus"></i> <?php echo L('member_modelfield_add');?></a></li>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
        <a class="tooltips" href="?m=member&c=member_modelfield&a=manage&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_manage');?>"><i class="fa fa-code"></i> <?php echo L('member_modelfield_manage');?></a><i class="fa fa-circle"></i>
        <a class="tooltips on" href="<?php echo dr_now_url();?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_add');?>"><i class="fa fa-plus"></i> <?php echo L('member_modelfield_add');?></a>
    </div>
    <?php }?>
</div>
<div class="content-header"></div>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input name="info[modelid]" type="hidden" value="<?php echo $modelid;?>">
    <div class="myfbody">
    <div class="portlet bordered light">
        <div class="portlet-title tabbable-line">
            <ul class="nav nav-tabs" style="float:left;">
                <li<?php if ($page==0) {?> class="active"<?php }?>>
                    <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('基本设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('基本设置');}?> </a>
                </li>
                <li<?php if ($page==1) {?> class="active"<?php }?>>
                    <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('字段样式').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-code"></i> <?php if (is_pc()) {echo L('字段样式');}?> </a>
                </li>
                <li<?php if ($page==2) {?> class="active"<?php }?>>
                    <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('数据验证').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-crop"></i> <?php if (is_pc()) {echo L('数据验证');}?> </a>
                </li>
                <li<?php if ($page==3) {?> class="active"<?php }?>>
                    <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('字段权限').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user"></i> <?php if (is_pc()) {echo L('字段权限');}?> </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">
                <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                    <div class="form-body">
                        <div class="form-group" id="dr_row_formtype">
                            <label class="col-md-2 control-label"><?php echo L('filedtype');?></label>
                            <div class="col-md-9">
                                <?php echo form::select($all_field,'','name="info[formtype]" id="formtype" onchange="javascript:field_setting(this.value);"',L('filedtype_need'));?>
                                <label id="dr_loading" style="display:none">
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    <img width="16" src="<?php echo JS_PATH;?>layer/theme/default/loading-2.gif">
                                </label>
                            </div>
                        </div>
                        <div class="form-group" id="dr_row_name">
                            <label class="col-md-2 control-label"><?php echo L('filed_nickname')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[name]" value="" id="dr_name" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','dr_name','dr_field',12);"></label>
                                <span class="help-block"><?php echo L('exaple_title')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="dr_row_field">
                            <label class="col-md-2 control-label"><?php echo L('filedname')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[field]" value="" id="dr_field"></label>
                                <span class="help-block"><?php echo L('username_rule')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-body" id="setting"></div>
                </div>
                <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('field_cue')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:120px" name="info[tips]"></textarea>
                                <span class="help-block"><?php echo L('nickname_alert')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="formattribute">
                            <label class="col-md-2 control-label"><?php echo L('extra_attribute')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[formattribute]" value="">
                                <span class="help-block"><?php echo L('add_javascript')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="css">
                            <label class="col-md-2 control-label"><?php echo L('form_css')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[css]" value="">
                                <span class="help-block"><?php echo L('user_form_css')?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('string_len_range')?></label>
                            <div class="col-md-9">
                                <label><?php echo L('min');?>：</label>
                                <label><input class="form-control" type="text" name="info[minlength]" value="0" id="field_minlength"></label>
                                <label><?php echo L('max');?>：</label>
                                <label><input class="form-control" type="text" name="info[maxlength]" value="" id="field_maxlength"></label>
                                <span class="help-block"><?php echo L('post_alert')?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('date_regular')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[pattern]" value="" id="pattern"></label>
                                <label><select name="pattern_select" onchange="javascript:$('#pattern').val(this.value)">
                                    <option value=""><?php echo L('common_regular')?></option>
                                    <option value="/^[0-9.-]+$/"><?php echo L('number')?></option>
                                    <option value="/^[0-9-]+$/"><?php echo L('int')?></option>
                                    <option value="/^[a-z]+$/i"><?php echo L('alphabet')?></option>
                                    <option value="/^[0-9a-z]+$/i"><?php echo L('alphabet')?>+<?php echo L('number')?></option>
                                    <option value="/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/">E-mail</option>
                                    <option value="/^[0-9]{5,20}$/">QQ</option>
                                    <option value="/^http(s?):\/\//"><?php echo L('http')?></option>
                                    <option value="/^(1)[0-9]{10}$/"><?php echo L('mp')?></option>
                                    <option value="/^[0-9-]{6,13}$/"><?php echo L('tel')?></option>
                                    <option value="/^[0-9]{6}$/"><?php echo L('postcode')?></option>
                                </select></label>
                                <span class="help-block"><?php echo L('validity_alert')?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('error_alert')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[errortips]" value="">
                                <span class="help-block"><?php echo L('form_error_alert')?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('unique')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isunique]" value="1" id="field_allow_isunique1"> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isunique]" value="0" id="field_allow_isunique0" checked> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('can_empty')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isbase]" value="1" checked> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isbase]" value="0"> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('isadd_condition')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isadd]" value="1" checked> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isadd]" value="0"> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('isomnipotent_condition')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isomnipotent]" value="1"> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isomnipotent]" value="0" checked> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('deny_set_field_group')?></label>
                            <div class="col-md-9">
                                <label style="min-width: 200px;">
                                    <select class="form-control bs-select" name="unsetgroupids[]" id="unsetgroupids" multiple data-actions-box="true">
                                        <?php if(is_array($grouplist)){
                                        foreach($grouplist as $key=>$value){?>
                                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php }}?>
                                    </select>
                                </label>
                                <span class="help-block">前端发布内容时该会员组将不会看到这个字段，如果“是否在前台显示”关闭了此功能就无效</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('deny_set_field_role')?></label>
                            <div class="col-md-9">
                                <label style="min-width: 200px;">
                                    <select class="form-control bs-select" name="unsetroleids[]" id="unsetroleids" multiple data-actions-box="true">
                                        <?php if(is_array($roles)){
                                        foreach($roles as $key=>$value){?>
                                        <option value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php }}?>
                                    </select>
                                </label>
                                <span class="help-block">后台发布内容时该角色将不会看到这个字段</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
            <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo $reply_url;?>')" class="btn yellow"> <i class="fa fa-mail-reply-all"></i> <?php echo L('保存并返回')?></button></label>
        </div>
    </div>
</form>
</div>
<script type="text/javascript">
function field_setting(fieldtype) {
    $("#dr_loading").show();
    if(fieldtype) {
        $.getJSON("?m=member&c=member_modelfield&a=public_field_setting&fieldtype="+fieldtype, function(data){
            if(data.field_allow_isunique=='1') {
                $('#field_allow_isunique0').attr("disabled",false);
                $('#field_allow_isunique1').attr("disabled",false);
            } else {
                $('#field_allow_isunique0').prop("checked",true);
                $('#field_allow_isunique0').attr("disabled",true);
                $('#field_allow_isunique1').attr("disabled",true);
            }
            $('#field_minlength').val(data.field_minlength);
            $('#field_maxlength').val(data.field_maxlength);
            $('#setting').html(data.setting);
            $("#dr_loading").hide();
        });
    } else {
        $('#setting').html('');
        $("#dr_loading").hide();
    }
}
</script>
</div>
</div>
</div>
</div>
</body>
</html>