<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <li><a class="tooltips" href="?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?>"><i class="fa fa-code"></i> <?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips" href="?m=content&c=sitemodel_field&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('add_field');?>"><i class="fa fa-plus"></i> <?php echo L('add_field');?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips on" href="<?php echo dr_now_url();?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('edit_field');?>"><i class="fa fa-edit"></i> <?php echo L('edit_field');?></a></li>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
        <a class="tooltips" href="?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?>"> <i class="fa fa-code"></i> <?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?></a>
        <i class="fa fa-circle"></i>
        <a class="tooltips" href="?m=content&c=sitemodel_field&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('add_field');?>"> <i class="fa fa-plus"></i> <?php echo L('add_field');?></a>
        <i class="fa fa-circle"></i>
        <a href="<?php echo dr_now_url();?>" class="tooltips on" data-container="body" data-placement="bottom" data-original-title="<?php echo L('edit_field');?>"> <i class="fa fa-edit"></i> <?php echo L('edit_field');?></a>
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
<input name="fieldid" type="hidden" value="<?php echo $fieldid?>">
<input name="oldfield" type="hidden" value="<?php echo $field?>">
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
                <li class="<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo 'hide';}?><?php if ($page==3) {?> active<?php }?>">
                    <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('字段权限').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user"></i> <?php if (is_pc()) {echo L('字段权限');}?> </a>
                </li>
            </ul>
        </div>
        <div class="portlet-body">
            <div class="tab-content">
                <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                    <div class="form-body">
                        <div class="form-group" id="dr_row_formtype">
                            <label class="col-md-2 control-label"><?php echo L('field_type');?></label>
                            <div class="col-md-9">
                                <input type="hidden" name="info[formtype]" value="<?php echo $formtype;?>">
                                <?php echo form::select($all_field,$formtype,'name="info[formtype]" id="formtype" onchange="javascript:field_setting(this.value);" disabled',L('select_fieldtype'));?>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('issystem_field')?></label>
                            <div class="col-md-9">
                                <input type="hidden" name="issystem" id="issystem" value="<?php echo $issystem ? 1 : 0;?>">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[issystem]" id="field_basic_table1" value="1" <?php if($issystem) echo 'checked';?> disabled> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" id="field_basic_table0" name="info[issystem]" value="0" <?php if(!$issystem) echo 'checked';?> disabled> <?php echo L('no');?> <span></span></label>
                                </div>
                                <span class="help-block"><?php echo L('issystem_field_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="dr_row_name">
                            <label class="col-md-2 control-label"><?php echo L('field_nickname')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[name]" value="<?php echo $name?>" id="dr_name" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','dr_name','dr_field',12);"></label>
                                <span class="help-block"><?php echo L('nickname_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="dr_row_field">
                            <label class="col-md-2 control-label"><?php echo L('fieldname')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[field]" value="<?php echo $field?>" <?php if(dr_in_array($field,$forbid_delete)) echo 'readonly';?> id="dr_field"></label>
                                <span class="help-block"><?php echo L('fieldname_tips')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-body"><?php echo $form_data;?></div>
                </div>
                <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('field_tip')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:120px" name="info[tips]"><?php echo new_html_special_chars($tips);?></textarea>
                                <span class="help-block"><?php echo L('field_tips')?></span>
                            </div>
                        </div>
                        <?php if(dr_in_array($formtype,$att_css_js)) {?>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('form_attr')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[formattribute]" value="<?php echo new_html_special_chars($formattribute);?>">
                                <span class="help-block"><?php echo L('form_attr_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group" id="css">
                            <label class="col-md-2 control-label"><?php echo L('form_css_name')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[css]" value="<?php echo new_html_special_chars($css);?>">
                                <span class="help-block"><?php echo L('form_css_name_tips')?></span>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                </div>
                <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('string_size')?></label>
                            <div class="col-md-9">
                                <label><?php echo L('minlength');?>：</label>
                                <label><input class="form-control" type="text" name="info[minlength]" value="<?php echo $minlength;?>" id="field_minlength"></label>
                                <label><?php echo L('maxlength');?>：</label>
                                <label><input class="form-control" type="text" name="info[maxlength]" value="<?php echo $maxlength;?>" id="field_maxlength"></label>
                                <span class="help-block"><?php echo L('string_size_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('data_preg')?></label>
                            <div class="col-md-9">
                                <label><input class="form-control" type="text" name="info[pattern]" value="<?php echo $pattern;?>" id="pattern"></label>
                                <label><select name="pattern_select" onchange="javascript:$('#pattern').val(this.value)">
                                    <option value=""><?php echo L('often_preg');?></option>
                                    <option value="/^[0-9.-]+$/"><?php echo L('figure');?></option>
                                    <option value="/^[0-9-]+$/"><?php echo L('integer');?></option>
                                    <option value="/^[a-z]+$/i"><?php echo L('letter');?></option>
                                    <option value="/^[0-9a-z]+$/i"><?php echo L('integer_letter');?></option>
                                    <option value="/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/">E-mail</option>
                                    <option value="/^[0-9]{5,20}$/">QQ</option>
                                    <option value="/^http(s?):\/\//"><?php echo L('hyperlink');?></option>
                                    <option value="/^(1)[0-9]{10}$/"><?php echo L('mobile_number');?></option>
                                    <option value="/^[0-9-]{6,13}$/"><?php echo L('tel_number');?></option>
                                    <option value="/^[0-9]{6}$/"><?php echo L('zip');?></option>
                                </select></label>
                                <span class="help-block"><?php echo L('data_preg_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('data_passed_msg')?></label>
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="info[errortips]" value="<?php echo new_html_special_chars($errortips);?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                    <div class="form-body">
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('unique')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isunique]" value="1" id="field_allow_isunique1" <?php if($isunique) echo 'checked';?> <?php if(!$field_allow_isunique) echo 'disabled'; ?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isunique]" value="0" id="field_allow_isunique0" <?php if(!$isunique) echo 'checked';?> <?php if(!$field_allow_isunique) echo 'disabled'; ?>> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('basic_field')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isbase]" value="1" <?php if($isbase) echo 'checked';?>> <?php echo L('the_default');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isbase]" value="0" <?php if(!$isbase) echo 'checked';?>> <?php echo L('right_field');?> <span></span></label>
                                </div>
                                <span class="help-block"><?php echo L('basic_field_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('as_search_field')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[issearch]" value="1" id="field_allow_search1" <?php if($issearch) echo 'checked';?> <?php if(!$field_allow_search) echo 'disabled'; ?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[issearch]" value="0" id="field_allow_search0" <?php if(!$issearch) echo 'checked';?> <?php if(!$field_allow_search) echo 'disabled'; ?>> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('allow_contributor')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isadd]" value="1" <?php if($isadd) echo 'checked';?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isadd]" value="0" <?php if(!$isadd) echo 'checked';?>> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('as_fulltext_field')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isfulltext]" value="1" id="field_allow_fulltext1" <?php if($isfulltext) echo 'checked';?> <?php if(!$field_allow_fulltext) echo 'disabled'; ?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isfulltext]" value="0" id="field_allow_fulltext0" <?php if(!$isfulltext) echo 'checked';?> <?php if(!$field_allow_fulltext) echo 'disabled'; ?>> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('as_omnipotent_field')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isomnipotent]" value="1" <?php if($isomnipotent) echo 'checked';?> <?php 
      if(dr_in_array($field,$forbid_fields)) echo 'disabled'; ?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isomnipotent]" value="0" <?php if(!$isomnipotent) echo 'checked';?>> <?php echo L('no');?> <span></span></label>
                                </div>
                                <span class="help-block"><?php echo L('as_omnipotent_field_tips')?></span>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('as_postion_info')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isposition]" value="1" <?php if($isposition) echo 'checked';?>> <?php echo L('yes');?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="info[isposition]" value="0" <?php if(!$isposition) echo 'checked';?>> <?php echo L('no');?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('disabled_groups_field')?></label>
                            <div class="col-md-9">
                                <label style="min-width: 200px;">
                                    <select class="form-control bs-select" name="unsetgroupids[]" id="unsetgroupids" multiple data-actions-box="true">
                                        <?php if(is_array($grouplist)){
                                        foreach($grouplist as $key=>$value){?>
                                        <option<?php if (dr_in_array($key, $unsetgroupids)) {?> selected<?php }?> value="<?php echo $key;?>"><?php echo $value;?></option>
                                        <?php }}?>
                                    </select>
                                </label>
                                <span class="help-block">前端发布内容时该会员组将不会看到这个字段，如果“在前台投稿中显示”关闭了此功能就无效</span>
                            </div>
                        </div>
                        <div class="form-group<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' hide';}?>">
                            <label class="col-md-2 control-label"><?php echo L('disabled_role_field')?></label>
                            <div class="col-md-9">
                                <label style="min-width: 200px;">
                                    <select class="form-control bs-select" name="unsetroleids[]" id="unsetroleids" multiple data-actions-box="true">
                                        <?php if(is_array($roles)){
                                        foreach($roles as $key=>$value){?>
                                        <option<?php if (dr_in_array($key, $unsetroleids)) {?> selected<?php }?> value="<?php echo $key;?>"><?php echo $value;?></option>
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
</div>
</div>
</div>
</div>
</body>
</html>