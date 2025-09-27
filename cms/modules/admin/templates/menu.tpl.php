<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<?php if(ROUTE_A=='init') {?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                <div class="page-body">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form class="form-horizontal" role="form" id="myform">
<div class="table-list">
    <table class="table-checkable">
        <thead>
        <tr class="heading">
        <th class="myselect">
            <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                <span></span>
            </label>
        </th>
        <th width="70" style="text-align:center"><?php echo L('listorder')?></th>
        <th width="60" style="text-align:center"> <?php echo L('可用')?> </th>
        <th width="300"> <?php echo L('chinese_name')?> </th>
        <th width="80" style="text-align:center"> <?php echo L('类型')?> </th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($array)){
    foreach($array as $info){
?>
<tr class="odd gradeX" id="dr_row_<?php echo $info['id'];?>">
<td class="myselect">
<label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
<input type="checkbox" class="checkboxes<?php echo $info['tid'];?> group-checkable" data-set=".checkboxes<?php echo $info['id'];?>"  name="ids[]" value="<?php echo $info['id'];?>" />
<span></span>
</label>
</td>
<td style="text-align:center"> <input type="text" onblur="dr_ajax_save(this.value, '?m=admin&c=menu&a=listorder&id=<?php echo $info['id'];?>&pc_hash='+pc_hash, 'listorder')" value="<?php echo $info['listorder'];?>" class="displayorder form-control input-sm input-inline input-mini"> </td>
<td style="text-align:center"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=admin&c=menu&a=display_edit&id=<?php echo $info['id'];?>&pc_hash='+pc_hash, 0);" class="badge badge-<?php if ($info['display']) {?>yes<?php } else { ?>no<?php }?>"><i class="fa fa-<?php if ($info['display']) {?>check<?php } else { ?>times<?php }?>"></i></a></td>
<td><?php echo $info['spacer'].' '.$info['title'];?></td>
<td style="text-align:center"><?php echo $info['type'];?></td>
<td>
<a href="?m=admin&c=menu&a=add&parentid=<?php echo $info['id'];?>&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="btn btn-xs blue"> <i class="fa fa-plus"></i> <?php echo L('add')?> </a>
<a href="?m=admin&c=menu&a=edit&id=<?php echo $info['id'];?>&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a>
</td>
</tr>
<?php 
    }
}
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-12 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onclick="ajax_option('?m=admin&c=menu&a=delete&menuid=<?php echo $this->input->get('menuid');?>', '<?php echo L('你确定要删除它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
<?php } elseif(ROUTE_A=='add') {
echo load_css(JS_PATH.'bootstrap-touchspin/bootstrap.touchspin.css');
echo load_js(JS_PATH.'bootstrap-touchspin/bootstrap.touchspin.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('菜单管理').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('菜单管理');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group" id="dr_row_parentid">
                        <label class="col-md-2 control-label"><?php echo L('menu_parentid')?></label>
                        <div class="col-md-9">
                            <select name="info[parentid]" id="parentid">
                                <option value="0"><?php echo L('no_parent_menu')?></option>
                                <?php echo $select_categorys;?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_language">
                        <label class="col-md-2 control-label"><?php echo L('chinese_name')?></label>
                        <div class="col-md-9">
                            <label><input type="text" id="language" name="language" value="" class="form-control input-large"></label>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_name">
                        <label class="col-md-2 control-label"><?php echo L('menu_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="name" name="info[name]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_m">
                        <label class="col-md-2 control-label"><?php echo L('module_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_m" name="info[m]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_c">
                        <label class="col-md-2 control-label"><?php echo L('file_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_c" name="info[c]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_a">
                        <label class="col-md-2 control-label"><?php echo L('action_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_a" name="info[a]" value="" >
                            <span class="help-block" id="a_tip"><?php echo L('ajax_tip')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_data">
                        <label class="col-md-2 control-label"><?php echo L('att_data')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="data" name="info[data]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_icon">
                        <label class="col-md-2 control-label"><?php echo L('菜单图标')?></label>
                        <div class="col-md-9">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" id="menu_icon" name="info[icon]" value="" class="form-control input-large">
                                <span class="input-group-btn"><a class="btn green" href="javascript:menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%');"><i class="fa fa-edit"></i> 选择图标</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_display">
                        <label class="col-md-2 control-label"><?php echo L('menu_display')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" checked> <?php echo L('yes')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0"> <?php echo L('no')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label ajax_name"><?php echo L('排列顺序')?></label>
                        <div class="col-md-9">
                            <label style="width:200px;"><input type="text" id="listorder" class="form-control" name="info[listorder]" value="0"></label>
                            <span class="help-block"> <?php echo L('排序值由小到大排列')?> </span>
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
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
<?php } elseif(ROUTE_A=='edit') {
echo load_css(JS_PATH.'bootstrap-touchspin/bootstrap.touchspin.css');
echo load_js(JS_PATH.'bootstrap-touchspin/bootstrap.touchspin.js');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('菜单管理').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('菜单管理');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group" id="dr_row_parentid">
                        <label class="col-md-2 control-label"><?php echo L('menu_parentid')?></label>
                        <div class="col-md-9">
                            <select name="info[parentid]" id="parentid">
                                <option value="0"><?php echo L('no_parent_menu')?></option>
                                <?php echo $select_categorys;?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_language">
                        <label class="col-md-2 control-label"><?php echo L('chinese_name')?></label>
                        <div class="col-md-9">
                            <label><input type="text" id="language" name="language" value="<?php echo L($name,'','',1)?>" class="form-control input-large"></label>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_name">
                        <label class="col-md-2 control-label"><?php echo L('menu_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="name" name="info[name]" value="<?php echo $name?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_m">
                        <label class="col-md-2 control-label"><?php echo L('module_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_m" name="info[m]" value="<?php echo $m?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_c">
                        <label class="col-md-2 control-label"><?php echo L('file_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_c" name="info[c]" value="<?php echo $c?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_a">
                        <label class="col-md-2 control-label"><?php echo L('action_name')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_a" name="info[a]" value="<?php echo $a?>" >
                            <span class="help-block" id="a_tip"><?php echo L('ajax_tip')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_data">
                        <label class="col-md-2 control-label"><?php echo L('att_data')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="data" name="info[data]" value="<?php echo $data?>" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_icon">
                        <label class="col-md-2 control-label"><?php echo L('菜单图标')?></label>
                        <div class="col-md-9">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" id="menu_icon" name="info[icon]" value="<?php echo $icon?>" class="form-control input-large">
                                <span class="input-group-btn"><a class="btn green" href="javascript:menuicon('icons','?m=admin&c=menu&a=public_icon&value='+$('#menu_icon').val(),'选择图标','80%','80%');"><i class="fa fa-edit"></i> 选择图标</a></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_display">
                        <label class="col-md-2 control-label"><?php echo L('menu_display')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="1" <?php if($display) echo 'checked';?>> <?php echo L('yes')?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="info[display]" value="0" <?php if(!$display) echo 'checked';?>> <?php echo L('no')?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label ajax_name"><?php echo L('排列顺序')?></label>
                        <div class="col-md-9">
                            <label style="width:200px;"><input type="text" id="listorder" class="form-control" name="info[listorder]" value="<?php echo intval($listorder);?>"></label>
                            <span class="help-block"> <?php echo L('排序值由小到大排列')?> </span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn blue"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo $post_url;?>')" class="btn green"> <i class="fa fa-plus"></i> <?php echo L('保存再添加')?></button></label>
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo $reply_url;?>')" class="btn yellow"> <i class="fa fa-mail-reply-all"></i> <?php echo L('保存并返回')?></button></label>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
<?php }?>
<script type="text/javascript">
    $(function(){
        $("#listorder").TouchSpin({
            buttondown_class: "btn red",
            buttonup_class: "btn green",
            min: 0,
            max: 999999999999999
        });
    });
</script>
</body>
</html>