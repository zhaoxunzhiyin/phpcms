<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.js"></script>
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
<form action="?m=content&c=sitemodel&a=edit" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input type="hidden" name="modelid" value="<?php echo $modelid;?>" />
<input type="hidden" name="menuid" value="<?php echo $this->input->get('menuid');?>" />
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('basic_configuration').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('basic_configuration');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('后台列表显示字段').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-table"></i> <?php if (is_pc()) {echo L('后台列表显示字段');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('template_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-html5"></i> <?php if (is_pc()) {echo L('template_setting');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('other_template_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-internet-explorer"></i> <?php if (is_pc()) {echo L('other_template_setting');}?> </a>
            </li>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="$('#dr_page').val('4')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('搜索设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-search"></i> <?php if (is_pc()) {echo L('搜索设置');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('model_name');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="name" name="info[name]" value="<?php echo $name;?>"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('model_tablename');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="tablename" name="info[tablename]" value="<?php echo $tablename;?>" disabled></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('description');?></label>
                        <div class="col-md-9">
                            <textarea class="form-control " style="height:90px" name="info[description]"><?php echo $description;?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('封面栏目分页');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pcatpost]" value="1"<?php echo ($pcatpost) ? ' checked' : ''?>> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pcatpost]" value="0"<?php echo (!$pcatpost) ? ' checked' : ''?>> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('栏目封面模板可支持分页功能')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('上下篇循环显示');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[previous]" value="1"<?php echo ($previous) ? ' checked' : ''?>> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[previous]" value="0"<?php echo (!$previous) ? ' checked' : ''?>> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('上一篇下一篇循环显示')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('updatetime_check');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[updatetime_select]" value="0"<?php echo (!$updatetime_select) ? ' checked' : ''?>> <?php echo L('check_not_default');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[updatetime_select]" value="1"<?php echo ($updatetime_select) ? ' checked' : ''?>> <?php echo L('check_default');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('在后台内容编辑时的更新时间字段，是否勾选"不更新"，不勾选时将自动更新为当前时间')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('自动填充内容描述');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_auto]" value="0"<?php echo (!$desc_auto) ? ' checked' : ''?>> <?php echo L('自动填充');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_auto]" value="1"<?php echo ($desc_auto) ? ' checked' : ''?>> <?php echo L('手动填充');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('当描述为空时，系统提取内容中的文字来填充描述字段')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('提取内容描述字数');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="name" name="setting[desc_limit]" value="<?php echo $desc_limit;?>"></label>
                            <span class="help-block"><?php echo L('在内容中提取描述信息的最大字数限制')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('清理描述中的空格');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_clear]" value="0"<?php echo (!$desc_clear) ? ' checked' : ''?> /> <?php echo L('不清理');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[desc_clear]" value="1"<?php echo ($desc_clear) ? ' checked' : ''?> /> <?php echo L('清理空格');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('提取描述字段时是否情况空格符号，一般英文站点不需要清理空格');?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('列表默认排序');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-xlarge" type="text" name="setting[order]" value="<?php if ($order){?><?php echo htmlspecialchars($order);?><?php }else{?>listorder DESC,updatetime DESC<?php }?>" ></label>
                            <span class="help-block"><?php echo L('排序格式符号MySQL的语法，例如：主表字段 desc');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('列表时间搜索');?></label>
                        <div class="col-md-9">

                            <label><input class="form-control" type="text" name="setting[search_time]" value="<?php if ($search_time){?><?php echo htmlspecialchars($search_time);?><?php }else{?>updatetime<?php }?>" ></label>
                            <span class="help-block"><?php echo L('设置后台时间范围搜索字段，默认为更新时间字段：updatetime');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">列表默认搜索字段</label>
                        <div class="col-md-9">
                            <label><select name="setting[search_first_field]" class="form-control">
                                <?php foreach($this->field as $t) {?>
                                <?php if (dr_is_admin_search_field($t)) {?>
                                <option value="<?php echo $t['field'];?>"<?php if ($search_first_field==$t['field']) {?> selected<?php }?>><?php echo L($t['name']);?></option>
                                <?php }?>
                                <?php }?>
                                <option value="id"<?php if ($search_first_field=='id') {?> selected<?php }?>> ID </option>
                            </select></label>
                            <span class="help-block">设置后台列表的默认搜索字段，也就是第一个选中的字段</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('列表显示字段');?></label>
                        <div class="col-md-9">
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
                                                <input type="checkbox" class="checkboxes" name="setting[list_field][<?php echo $t['field'];?>][use]" value="1" <?php if ($list_field[$t['field']]['use']){?> checked<?php }?> />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td><?php echo L($t['name']);?> (<?php echo $t['field'];?>)</td>
                                        <td><input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][name]" value="<?php echo $list_field[$t['field']]['name'] ? htmlspecialchars($list_field[$t['field']]['name']) : $t['name'];?>" /></td>
                                        <td> <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][width]" value="<?php echo htmlspecialchars((string)$list_field[$t['field']]['width']);?>" /></td>
                                        <td><input type="checkbox" name="setting[list_field][<?php echo $t['field'];?>][center]" <?php if ($list_field[$t['field']]['center']){?> checked<?php }?> value="1"  data-on-text="<?php echo L('居中');?>" data-off-text="<?php echo L('默认');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                        </td>
                                        <td> <div class="input-group" style="width:250px">
                                                <span class="input-group-btn">
                                                    <a class="btn btn-success" href="javascript:help('?m=content&c=sitemodel&a=public_help&pc_hash='+pc_hash);"><?php echo L('回调');?></a>
                                                </span>
                                            <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][func]" value="<?php echo htmlspecialchars((string)$list_field[$t['field']]['func']);?>" />
                                        </div></td>
                                    </tr>
                                    <?php }}}?>
                                    </tbody>
                                </table>
                            </div>

                            <span class="help-block"><?php echo L('拖动字段可以进行顺序排列');?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('available_styles');?></label>
                        <div class="col-md-9">
                            <label><?php echo form::select($style_list, $default_style, 'name="info[default_style]" id="template_list" onchange="load_file_list(this.value)"', L('please_select'))?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('category_index_tpl');?></label>
                        <div class="col-md-9">
                            <label id="category_template"><?php echo form::select_template($default_style,'content', $category_template, 'name="setting[category_template]" id="template_category"', 'category')?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('category_list_tpl');?></label>
                        <div class="col-md-9">
                            <label id="list_template"><?php echo form::select_template($default_style,'content', $list_template, 'name="setting[list_template]" id="template_list"', 'list')?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('content_tpl');?></label>
                        <div class="col-md-9">
                            <label id="show_template"><?php echo form::select_template($default_style,'content', $show_template, 'name="setting[show_template]" id="template_show"','show')?></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('other_template_setting');?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="other" id="other" value="1"<?php echo ($admin_list_template) ? ' checked' : ''?>> <?php echo L('other_template_setting');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="other_tab" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('admin_content_list');?></label>
                        <div class="col-md-9">
                            <label id="admin_list_template"><?php echo $admin_list_template_f;?></label>
                        </div>
                    </div>
                    <div class="form-group" id="other_tab2" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('member_content_add');?></label>
                        <div class="col-md-9">
                            <label id="member_add_template"><?php echo form::select_template($default_style,'member', $member_add_template, 'name="setting[member_add_template]" id="template_member_add"', 'content_publish')?></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('搜索功能');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][use]" value="0"<?php echo (!$search['use']) ? ' checked' : ''?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][use]" value="1"<?php echo ($search['use']) ? ' checked' : ''?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('选择关闭将不能进行内容搜索');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('集成栏目页');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][catsync]" value="1"<?php echo ($search['catsync']) ? ' checked' : ''?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][catsync]" value="0"<?php echo (!$search['catsync']) ? ' checked' : ''?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('访问栏目页会定向到搜索页面，使栏目模板无效');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('搜索结果为空时跳转404');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_404]" value="1"<?php echo ($search['search_404']) ? ' checked' : ''?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_404]" value="0"<?php echo (!$search['search_404']) ? ' checked' : ''?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('开启后遇到搜索内容为空时直接跳转404页面');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('栏目catid不为空时显示结果');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_catid]" value="1"<?php echo ($search['search_catid']) ? ' checked' : ''?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_catid]" value="0"<?php echo (!$search['search_catid']) ? ' checked' : ''?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('默认情况下在catid不为空时不显示结果');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('搜索参数为空时不显示结果');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_param]" onclick="$('.dr_search_field').show()" value="1"<?php echo ($search['search_param']) ? ' checked' : ''?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][search_param]" onclick="$('.dr_search_field').hide()" value="0"<?php echo (!$search['search_param']) ? ' checked' : ''?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('默认情况下在搜索参数为空时会显示全部结果');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search_field">
                        <label class="col-md-2 control-label"><?php echo L('关键词匹配字段');?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-inline">
                                <?php foreach($search_field as $f) {?>
                                <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="search_field[]" value="<?php echo $f['field'];?>"<?php if (dr_in_array($f['field'], (array)explode(',', (string)$search['field']))) {?> checked<?php }?> /> <?php echo L($f['name']);?> （<?php echo $f['field'];?>）<span></span></label>
                                <?php }?>
                            </div>
                            <span class="help-block"><?php echo L('搜索关键词匹配字段只能设置主表字段，勾选字段越多查询速度就越慢');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('关键词匹配方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][complete]" value="1"<?php echo ($search['complete']) ? ' checked' : ''?> /> <?php echo L('完整匹配');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][complete]" value="0"<?php echo (!$search['complete']) ? ' checked' : ''?> /> <?php echo L('模糊匹配');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('完整匹配是关键词绝对相同才视为匹配；模糊匹配是关键词包含其中才视为匹配');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('字段词匹配方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][is_like]" value="0"<?php echo (!$search['is_like']) ? ' checked' : ''?> /> <?php echo L('完整匹配');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][is_like]" value="1"<?php echo ($search['is_like']) ? ' checked' : ''?> /> <?php echo L('模糊匹配');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('与关键词匹配方式选项相同，按字段作为搜索条件时，字段词与数据库储存词的匹配方式');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('多值匹配方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][is_double_like]" value="0"<?php echo (!$search['is_double_like']) ? ' checked' : ''?> /> <?php echo L('AND匹配');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search][is_double_like]" value="1"<?php echo ($search['is_double_like']) ? ' checked' : ''?> /> <?php echo L('OR匹配');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('字段的多值匹配模式下的多条件查询关系');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('每次搜索间隔')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" name="setting[search][search_time]" value="<?php echo intval($search['search_time']);?>">
                                    <span class="input-group-addon">
                                        <?php echo L('miao')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('每一次搜索的时间间隔，0表示不限制');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('最小关键字长度');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[search][length]" value="<?php echo ($search['length']) ? $search['length'] : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索关键字最小字符长度，一个汉字占两位，0表示不限制长度');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('最大关键字长度');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[search][maxlength]" value="<?php echo ($search['maxlength']) ? $search['maxlength'] : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索关键字最大字符长度，一个汉字占两位，0表示不限制长度');?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
                <?php if (!$is_iframe) {?><label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo $reply_url;?>')" class="btn yellow"> <i class="fa fa-mail-reply-all"></i> <?php echo L('保存并返回')?></button></label><?php }?>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript">
$(function() {
    <?php if (empty($search['search_param'])) {?>
    $('.dr_search_field').hide();
    <?php } else {?>
    $('.dr_search_field').show();
    <?php }?>
    $(".field-sort-items").sortable();
    handleBootstrapSwitch();
});
function load_file_list(id) {
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id, function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
}
$("#other").click(function() {
    if ($('#other').is(':checked')) {
        $('#other_tab').show();
        $('#other_tab2').show();
    } else {
        $('#other_tab').hide();
        $('#other_tab2').hide();
    }
})
if ($('#other').is(':checked')) {
    $('#other_tab').show();
    $('#other_tab2').show();
} else {
    $('#other_tab').hide();
    $('#other_tab2').hide();
}
</script>
</div>
</div>
</body>
</html>