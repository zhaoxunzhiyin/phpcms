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
    <p><a href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');"><?php echo L('变更栏目属性之后，需要一键更新栏目配置信息');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input type="hidden" name="menuid" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('栏目属性设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-table"></i> <?php if (is_pc()) {echo L('栏目属性设置');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('默认展开顶级栏目下层');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[popen]" value="1"<?php if ($data['popen']) {?> checked<?php }?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[popen]" value="0"<?php if (empty($data['popen'])) {?> checked<?php }?> /> <?php echo L('close');?> <span></span></label>
                            </div>

                            <span class="help-block"><?php echo L('进入栏目管理时默认展开顶级栏目的下级子栏目');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('栏目列表数量统计');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[total]" value="0"<?php if (empty($data['total'])) {?> checked<?php }?> /> <?php echo L('open');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[total]" value="1"<?php if ($data['total']) {?> checked<?php }?> /> <?php echo L('close');?> <span></span></label>
                            </div>

                            <span class="help-block"><?php echo L('进入栏目管理时可以看到栏目的文章数量，当栏目过多时建议关闭此选项，会影响加载速度');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('栏目列表名称长度');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="data[name_size]" value="<?php echo intval((string)$data['name_size']);?>"></label>
                            <span class="help-block"><?php echo L('在后台栏目列表处显示的名称长度控制值，0表示不限制');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('栏目数量阈值');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" name="data[max_category]" value="<?php echo intval((string)$data['max_category']);?>"></label>
                            <span class="help-block"><?php echo L('当栏目总数量在阈值范围内时，变动栏目时系统会自动更新缓存，可能会造成延迟情况');?></span>
                            <span class="help-block"><?php echo L('当栏目数量大于阈值时，系统将不会自动更新缓存，需要手动更新缓存才会生效');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('后台列表显示字段');?></label>
                        <div class="col-md-9">

                            <div class="table-list">
                                <table>
                                    <thead>
                                    <tr class="heading">
                                        <th class="myselect">
                                            <?php echo L('显示');?>
                                        </th>
                                        <th width="180"> <?php echo L('字段');?> </th>
                                        <th> <?php echo L('说明');?> </th>
                                    </tr>
                                    </thead>
                                    <tbody class="field-sort-items2">
                                    <?php 
                                    if(is_array($sysfield)){
                                    foreach($sysfield as $n => $t){
                                    ?>
                                    <tr class="odd gradeX">
                                        <td class="myselect">
                                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="checkboxes" name="data[sys_field][]" value="<?php echo $n;?>"<?php if (dr_in_array($n, $data['sys_field'])){?> checked<?php }?> />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td><?php echo $t[0];?></td>
                                        <td><?php echo $t[1];?></td>
                                    </tr>
                                    <?php }}?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="table-list" style="margin-top: 30px">
                                <table>
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
                                    foreach($field as $n => $t){
                                    if ($t['field']) {
                                    ?>
                                    <tr class="odd gradeX">
                                        <td class="myselect">
                                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                                <input type="checkbox" class="checkboxes" name="data[list_field][<?php echo $t['field'];?>][use]" value="1" <?php if ($data['list_field'][$t['field']]['use']){?> checked<?php }?> />
                                                <span></span>
                                            </label>
                                        </td>
                                        <td><?php echo L($t['name']);?> (<?php echo $t['field'];?>)</td>
                                        <td><input class="form-control" type="text" name="data[list_field][<?php echo $t['field'];?>][name]" value="<?php echo $data['list_field'][$t['field']]['name'] ? htmlspecialchars($data['list_field'][$t['field']]['name']) : $t['name'];?>" /></td>
                                        <td> <input class="form-control" type="text" name="data[list_field][<?php echo $t['field'];?>][width]" value="<?php echo htmlspecialchars((string)$data['list_field'][$t['field']]['width']);?>" /></td>
                                        <td><input type="checkbox" name="data[list_field][<?php echo $t['field'];?>][center]" <?php if ($data['list_field'][$t['field']]['center']){?> checked<?php }?> value="1"  data-on-text="<?php echo L('居中');?>" data-off-text="<?php echo L('默认');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                        </td>
                                        <td> <div class="input-group" style="width:250px">
                                                <span class="input-group-btn">
                                                    <a class="btn btn-success" href="javascript:help('?m=content&c=sitemodel&a=public_help&pc_hash='+pc_hash);"><?php echo L('回调');?></a>
                                                </span>
                                            <input class="form-control" type="text" name="data[list_field][<?php echo $t['field'];?>][func]" value="<?php echo htmlspecialchars((string)$data['list_field'][$t['field']]['func']);?>" />
                                        </div></td>
                                    </tr>
                                    <?php }}}?>
                                    </tbody>
                                </table>
                            </div>
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
<script type="text/javascript">
$(function() {
    $(".field-sort-items").sortable();
});
</script>
</div>
</div>
</body>
</html>