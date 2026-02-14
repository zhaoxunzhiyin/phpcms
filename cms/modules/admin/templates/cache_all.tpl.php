<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="padding-top:0px;margin-bottom:30px;">
<div class="note note-danger">
    <p><a href="javascript:dr_update_cache_all();"><?php echo L('更改系统配置之后需要重新生成一次缓存文件');?></a></p>
</div>

<div class="right-card-box">
    <div class="table-scrollable">

        <table class="table table-striped table-bordered table-hover table-checkable dataTable">
            <thead>
            <tr class="heading">
                <th width="55"> </th>
                <th width="500"> <?php echo L('更新项目');?> </th>
                <th> </th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if(is_array($list)){
            foreach($list as $id => $t){
            $id=$id+1;
            ?>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id;?> </span>
                </td>
                <td>
                    <?php echo L($t['name']);?>
                </td>
                <td style="overflow:auto">
                    <label>
                        <a href="javascript:;" onclick="my_update_cache('<?php echo $id;?>', '<?php echo $t['function'];?>', '<?php echo $t['mod'];?>', '<?php echo $t['file'];?>', '<?php echo $t['param'];?>');" class="btn red btn-xs<?php if ($t['function'] != 'update_thumb') {?> update_cache<?php }?>"><i class="fa fa-refresh"></i> <?php echo L('立即更新');?> </a>
                    </label>
                    <label id="dr_<?php echo $id;?>_result" >

                    </label>
                </td>
            </tr>
            <?php }}
            $id=$id+1;?>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id++;?> </span>
                </td>
                <td>
                    <?php echo L('新增或变更栏目后，需要更新栏目缓存数据');?>
                </td>
                <td style="overflow:auto">
                    <label>
                        <a href="javascript:iframe_show('<?php echo L('更新栏目');?>', '?m=admin&c=category&a=public_repair&pc_hash='+pc_hash, '500px', '300px');" class="btn blue btn-xs"><i class="fa fa-reorder"></i> <?php echo L('更新栏目');?> </a>
                    </label>
                </td>
            </tr>
            <?php
            $id=$id+1;
            if ($module) {?>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id++;?> </span>
                </td>
                <td>
                    <?php echo L('内容地址与设置地址不同步时，更新内容URL地址');?>
                </td>
                <td style="overflow:auto">
                    <?php foreach($module as $c){?>
                    <label>
                        <a href="javascript:iframe_show('<?php echo L($c['name']);?>', '?m=content&c=create_html&a=public_show_url&modelid=<?php echo $c['modelid'];?>', '500px', '300px');" class="btn blue btn-xs"><i class="<?php echo dr_icon($c['icon']);?>"></i> <?php echo L($c['name']);?> </a>
                    </label>
                    <?php }?>
                    <?php if ($module_more) {?>
                    <div class="btn-group dropdown-btn-group" style="margin-top:0; margin-left: 10px">
                        <button type="button" class="btn btn-xs btn-default "><?php echo L('更多');?></button>
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach($module_more as $c){?>
                            <li>
                                <a href="javascript:iframe_show('<?php echo L($c['name']);?>', '?m=content&c=create_html&a=public_show_url&modelid=<?php echo $c['modelid'];?>', '500px', '300px');"><i class="<?php echo dr_icon($c['icon']);?>"></i> <?php echo L($c['name']);?> </a>
                            </li>
                            <?php }?>
                        </ul>
                    </div>
                    <?php }?>
                </td>
            </tr>
            <?php }
            if ($linkage) {?>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id++;?> </span>
                </td>
                <td>
                     <?php echo L('变更联动菜单数据后，更新缓存数据');?>
                </td>
                <td style="overflow:auto">
                    <?php foreach($linkage as $c){?>
                    <label>
                        <a href="javascript:iframe_show('<?php echo L($c['name']);?>', '?m=admin&c=linkage&a=public_cache&key=<?php echo $c['id'];?>', '500px', '300px');" class="btn blue btn-xs"> <?php echo L($c['name']);?> </a>
                    </label>
                    <?php }?>
                    <?php if ($linkage_more) {?>
                    <div class="btn-group dropdown-btn-group" style="margin-top:0; margin-left: 10px">
                        <button type="button" class="btn btn-xs btn-default "><?php echo L('更多');?></button>
                        <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach($linkage_more as $c){?>
                            <li>
                                <a href="javascript:iframe_show('<?php echo L($c['name']);?>', '?m=admin&c=linkage&a=public_cache&key=<?php echo $c['id'];?>', '500px', '300px');"> <?php echo L($c['name']);?> </a>
                            </li>
                            <?php }?>
                        </ul>
                    </div>
                    <?php }?>
                </td>
            </tr>
            <?php }?>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id++;?> </span>
                </td>
                <td>
                    <?php echo L('当编辑器已经存在动态地图时，需要更新AK值');?>
                </td>
                <td style="overflow:auto">
                    <label>
                        <a href="javascript:dr_alldb_edit();" class="btn red btn-xs"><i class="fa fa-refresh"></i> <?php echo L('立即更新');?> </a>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="badge badge-success"> <?php echo $id++;?> </span>
                </td>
                <td style="color:blue">
                    <?php echo L('更新CMS版本升级程序（当版本升级时必须操作）');?>
                </td>
                <td style="overflow:auto">
                    <label>
                        <a href="javascript:dr_iframe_show('<?php echo L('升级程序脚本');?>', '?m=admin&c=check&a=init&pc_hash='+pc_hash);" class="btn red btn-xs"><i class="fa fa-refresh"></i> <?php echo L('立即更新');?> </a>
                    </label>
                    <label>
                        <a href="javascript:dr_load_ajax('<?php echo L('确定初始化后台菜单吗？');?>', '?m=admin&c=menu&a=public_init', 0);" class="btn blue btn-xs"><i class="fa fa-list"></i> <?php echo L('初始化后台菜单');?> </a>
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
function dr_update_cache_all() {
    $('.update_cache').trigger('click');
}
function my_update_cache(id, m, mod, file, param) {
    var obj = $('#dr_'+id+'_result');
    obj.html("<img style='height:17px' src='<?php echo JS_PATH;?>layer/theme/default/loading-0.gif' />");

    if (m == 'attachment') {
        my_update_attachment(id, 0);
    } else {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "?m=admin&c=cache_all&a=public_cache&id="+m+"&mod="+mod+"&file="+file+"&param="+param,
            success: function (json) {
                if (json.code == 0) {
                    obj.html('<font color="red">'+json.msg+'</font>');
                } else {
                    obj.html('<font color="green">'+json.msg+'</font>');
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                obj.html('<a href="javascript:dr_show_file_code(\'<?php echo L('查看日志');?>\', \'?m=admin&c=index&a=public_error_log_show\');" style="color:red"><?php echo L("系统崩溃，请将错误日志发送给官方处理");?></a>');
            }
        });
    }


}
function my_update_attachment(id, page) {
    var obj = $('#dr_'+id+'_result');
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "?m=admin&c=cache_all&a=public_cache&id=attachment&page="+page,
        success: function (json) {
            if (json.code == 0) {
                obj.html('<font color="red">'+json.msg+'</font>');

            } else {
                if (json.data == 0) {
                    obj.html('<font color="green">'+json.msg+'</font>');
                } else {
                    my_update_attachment(id, json.data);
                    obj.html('<font color="blue">'+json.msg+'</font>');
                }
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            obj.html('<a href="javascript:dr_show_file_code(\'<?php echo L('查看日志');?>\', \'?m=admin&c=index&a=public_error_log_show\');" style="color:red"><?php echo L("系统崩溃，请将错误日志发送给官方处理");?></a>');
        }
    });
}
function dr_alldb_edit() {
    <?php if (!SYS_BDMAP_API) {?>
    dr_tips(0, '需要前往后台设置界面，去设置百度地图AK值', -1);
    <?php } else {?>
    var url = '?m=content&c=create_html&a=public_dball_edit&t1='+encodeURIComponent('/statics/js/ueditor/dialogs/map/show.html#')+'&t2='+encodeURIComponent('/statics/js/ueditor/dialogs/map/show.html#ak=<?php echo SYS_BDMAP_API;?>&');
    iframe_show('<?php echo L('批量操作');?>', url);
    <?php }?>
}
</script>
</div>
</div>
</div>
</div>
</body>
</html>