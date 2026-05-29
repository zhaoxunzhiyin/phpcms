<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
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
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('basic_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('basic_setting');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('搜索限制').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-search"></i> <?php if (is_pc()) {echo L('搜索限制');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('URL结构').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-link"></i> <?php if (is_pc()) {echo L('URL结构');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="$('#dr_page').val('3')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('sphinx_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-code"></i> <?php if (is_pc()) {echo L('sphinx_setting');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('搜索功能');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[use]" value="0"<?php echo (!$use) ? ' checked' : '';?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[use]" value="1"<?php echo ($use) ? ' checked' : '';?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('选择关闭将不能进行内容搜索');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('搜索结果为空时跳转404');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search_404]" value="1"<?php echo ($search_404) ? ' checked' : '';?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[search_404]" value="0"<?php echo (!$search_404) ? ' checked' : '';?> /> <?php echo L('close');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('开启后遇到搜索内容为空时直接跳转404页面');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pagination_count')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="pagesize" name="setting[pagesize]" value="<?php echo $pagesize?>" ></label>
                            <span class="help-block"><?php echo L('pagination_count_desc')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('每次搜索间隔')?></label>
                        <div class="col-md-9">
                            <div class="input-inline input-medium">
                                <div class="input-group">
                                    <input class="form-control" type="text" name="setting[search_time]" value="<?php echo intval($search_time);?>">
                                    <span class="input-group-addon">
                                        <?php echo L('miao')?>
                                    </span>
                                </div>
                            </div>
                            <span class="help-block"><?php echo L('每一次搜索的时间间隔，0表示不限制');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('最大搜索结果数量');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[max]" value="<?php echo ($max) ? $max : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索结果最大数据量限制，0表示不限制结果数量');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('最小关键字长度');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[length]" value="<?php echo ($length) ? $length : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索关键字最小字符长度，一个汉字占两位，0表示不限制长度');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('最大关键字长度');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[maxlength]" value="<?php echo ($maxlength) ? $maxlength : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索关键字最大字符长度，一个汉字占两位，0表示不限制长度');?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">

                <div class="form-body">

                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('搜索URL规则');?></label>
                        <div class="col-md-9">
                            <?php echo form::urlrule('search','search',0,$urlrule,'name="setting[urlrule]"','动态地址');?>
                            <?php $menu_data = $this->menu_db->get_one(array('name' => 'urlrule_manage', 'm' => 'admin', 'c' => 'urlrule', 'a' => 'init'));?>
                            <label><a class="btn btn-sm blue" href="javascript:;" layuimini-content-href="?m=admin&c=urlrule&a=init&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token()?>" data-title="URL规则管理" data-icon="fa fa-link"><i class="fa fa-link"></i> URL规则管理</a></label>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('搜索参数连接符号');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[param_join]" value="<?php echo ($param_join) ? $param_join : '-';?>" ></label>
                            <span class="help-block"><?php echo L('用于伪静态时搜索参数的连接，默认-，例如: 字段1-值-字段2-值');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('搜索参数字符串规则');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input onclick="$('.param_rule_0').hide();$('.param_rule_1').show()" type="radio" name="setting[param_rule]" value="1"<?php echo ($param_rule) ? ' checked' : '';?> /> <?php echo L('固定匹配');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input onclick="$('.param_rule_0').show();$('.param_rule_1').hide()" type="radio" name="setting[param_rule]" value="0"<?php echo (!$param_rule) ? ' checked' : '';?> /> <?php echo L('自由组合');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group dr_search param_rule_0" style="display: none;">
                        <label class="col-md-2 control-label"><?php echo L('自由组合字段映射关系');?></label>
                        <div class="col-md-9">
                            <textarea class="form-control" rows="7" name="setting[param_field]"><?php echo ($param_field);?></textarea>
                            <span class="help-block"><?php echo L('字段映射是指伪静态时将搜索字段重新命名，字段全称|缩写字母，例如keyword|k: 意思是把k作为keyword，多个字段映射回车符号分隔');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search param_rule_1" style="display: none;">
                        <label class="col-md-2 control-label"><?php echo L('固定匹配字段参数设置');?></label>
                        <div class="col-md-9">
                            <label><select name="setting[param_join_field][0]" class="form-control">
                                <option value="">-</option>
                                <option value="keyword"<?php if ($param_join_field[0] == 'keyword') {?> selected<?php }?>><?php echo L('搜索词');?>（keyword）</option>
                                <option value="order"<?php if ($param_join_field[0] == 'order') {?> selected<?php }?>><?php echo L('排序');?>（order）</option>
                                <option value="page"<?php if ($param_join_field[0] == 'page') {?> selected<?php }?>><?php echo L('分页');?>（page）</option>
                            </select></label>
                            <span class="help-block"><?php echo L('由一组固定的字符串参数作为搜索变量，例如：栏目id-城市-分类-搜索词-排序-分页.html');?></span>
                        </div>
                    </div>
                    <div class="form-group dr_search param_rule_1" style="display: none;">
                        <label class="col-md-2 control-label"><?php echo L('匹配字段默认填充值');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="setting[param_join_default_value]" value="<?php echo ($param_join_default_value) ? $param_join_default_value : 0;?>" ></label>
                            <span class="help-block"><?php echo L('搜索变量为空时的填充值，例如：0-0-0-搜索词-排序-分页.html');?></span>
                        </div>
                    </div>
                    <script>
                    $('.param_rule_<?php echo intval($param_rule);?>').show();
                    </script>
                    <div class="form-group dr_search">
                        <label class="col-md-2 control-label"><?php echo L('按字段参数值指定模板');?></label>
                        <div class="col-md-9">
                            <label><select name="setting[tpl_field]" class="form-control">
                                <option value=""><?php echo L('默认模板');?></option>
                                <option value="keyword"<?php if ($tpl_field == 'keyword') {?> selected<?php }?>><?php echo L('搜索词');?>（keyword）</option>
                                <option value="order"<?php if ($tpl_field == 'order') {?> selected<?php }?>><?php echo L('排序');?>（order）</option>
                                <option value="page"<?php if ($tpl_field == 'page') {?> selected<?php }?>><?php echo L('分页');?>（page）</option>
                            </select></label>
                            <span class="help-block"><?php echo L('用于按字段参数值显示不同的搜索模板文件，模板命名格式为：/list_字段参数值.html');?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('sphinxenable')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sphinxenable]" value="1" <?php if($sphinxenable) {?>checked<?php }?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sphinxenable]" value="0" <?php if(!$sphinxenable) {?>checked<?php }?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('host')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="sphinxhost" name="setting[sphinxhost]" value="<?php echo $sphinxhost?>" ></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('port')?></label>
                        <div class="col-md-9">
                            <div class="input-group input-large">
                                <input class="form-control" type="text" id="sphinxport" name="setting[sphinxport]" value="<?php echo $sphinxport?>" >
                                <span class="input-group-btn">
                                    <button class="btn blue" onclick="test_sphinx()" type="button"><i class="fa fa-wrench"></i> <?php echo L('test');?></button>
                                </span>
                            </div>
                            <span class="help-block" id='testing'></span>
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
<script src="<?php echo JS_PATH?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo CSS_PATH?>layuimini/js/lay-config.js?v=2.0.0" charset="utf-8"></script>
<script type="text/javascript">
layui.use(['layer', 'miniTab'], function () {
    var $ = layui.jquery,
        layer = layui.layer,
        miniTab = layui.miniTab;
    miniTab.listen();
});
function test_sphinx() {
    $('#testing').html('<?php echo L('testing')?>');
    $.post('?m=search&c=search_admin&a=public_test_sphinx',{sphinxhost:$('#sphinxhost').val(),sphinxport:$('#sphinxport').val(),<?php echo csrf_token();?>:$("#myform input[name='<?php echo csrf_token();?>']").val()}, function(data){
        // token 更新
        if (data.token) {
            var token = data.token;
            $("#myform input[name='"+token.name+"']").val(token.value);
        }
        dr_tips(data.code, data.msg);
        $('#testing').html(data.msg);
    },'json');
}
</script>
</div>
</div>
</body>
</html>