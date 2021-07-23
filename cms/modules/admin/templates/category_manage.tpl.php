<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<style type="text/css">
.list_order {text-align: left;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<div class="admin-main layui-anim layui-anim-upbit">
    <fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo L('category_manage');?></legend>
    </fieldset>
    <blockquote class="layui-elem-quote">
        <a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo $menuid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=0', '<?php echo L('add_category')?>')" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus"></i> <?php echo L('add_category');?>
        </a>
        <a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=1', '<?php echo L('add_page')?>')" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus-square"></i> <?php echo L('add_page');?>
        </a>
        <a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=2', '<?php echo L('add_cat_link')?>')" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus-square-o"></i> <?php echo L('add_cat_link');?>
        </a>
        <a href="?m=admin&c=category&a=public_cache&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&module=admin" class="layui-btn layui-btn-sm">
            <i class="fa fa-refresh"></i> <?php echo L('category_cache');?>
        </a>
        <a href="?m=admin&c=category&a=count_items&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-sort-amount-asc"></i> <?php echo L('count_items');?>
        </a>
        <a href="?m=admin&c=category&a=batch_edit&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-edit"></i> <?php echo L('category_batch_edit');?>
        </a>
        <a href="?m=content&c=sitemodel_field&a=init&menuid=<?php echo$this->input->get('menuid');?>&modelid=-1&pc_hash=<?php echo $_SESSION['pc_hash'];?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-bars"></i> <?php echo L('category_field_manage');?>
        </a>
        <a href="?m=content&c=sitemodel_field&a=init&menuid=<?php echo$this->input->get('menuid');?>&modelid=-2&pc_hash=<?php echo $_SESSION['pc_hash'];?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-list"></i> <?php echo L('page_field_manage');?>
        </a>
        <a class="layui-btn layui-btn-normal layui-btn-sm"  onclick="openAll();">
            <i class="fa fa-folder-open-o"></i> <?php echo L('open_close');?>
        </a>
    </blockquote>
    <table class="layui-table" id="treeTable" lay-filter="treeTable"></table>
</div>
<script type="text/html" id="catname">
    {{d.title}}{{d.display_icon}}
</script>
<script type="text/html" id="url">
    {{# if(d.url){ }}
    <a href="{{d.url}}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal"><?php echo L('vistor');?></a>
    {{# } else { }}
    <a href="?m=admin&c=category&a=public_cache&menuid=<?php echo $this->input->get('menuid');?>&module=admin" class="layui-btn layui-btn-xs layui-btn-danger"><?php echo L('update_backup');?></a>
    {{# } }}
</script>
<script type="text/html" id="ismenu">
    <span onmouseover="layer.tips('前端循环调用不会显示，但可以正常访问',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><input type="checkbox" name="ismenu" value="{{d.id}}" lay-skin="switch" lay-text="<?php echo L('display');?>|<?php echo L('hidden');?>" lay-filter="ismenu" {{ d.ismenu == 1 ? 'checked' : '' }}></span>
</script>
<script type="text/html" id="disabled">
    <span onmouseover="layer.tips('禁用状态下此栏目不能正常访问',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><input type="checkbox" name="disabled" value="{{d.id}}" lay-skin="switch" lay-text="<?php echo L('可用');?>|<?php echo L('禁用');?>" lay-filter="disabled" {{ d.disabled == 0 ? 'checked' : '' }}></span>
</script>
<script type="text/html" id="iscatpos">
    <span onmouseover="layer.tips('前端栏目面包屑导航调用不会显示，但可以正常访问，您现在的位置不显示',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><input type="checkbox" name="iscatpos" value="{{d.id}}" lay-skin="switch" lay-text="<?php echo L('display');?>|<?php echo L('hidden');?>" lay-filter="iscatpos" {{ d.iscatpos == 1 ? 'checked' : '' }}></span>
</script>
<script type="text/html" id="isleft">
    <span onmouseover="layer.tips('前端栏目调用左侧不会显示，但可以正常访问',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();"><input type="checkbox" name="isleft" value="{{d.id}}" lay-skin="switch" lay-text="<?php echo L('display');?>|<?php echo L('hidden');?>" lay-filter="isleft" {{ d.isleft == 1 ? 'checked' : '' }}></span>
</script>
<script type="text/html" id="listorder">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input" value="{{d.listorder}}" size="10"/>
</script>
<script type="text/html" id="action">
    <a href="javascript:addedit('?m=admin&c=category&a=add&parentid={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&s={{d.type}}&pc_hash=<?php echo $this->input->get('pc_hash');?>', '<?php echo L('add_sub_category');?>')" class="layui-btn layui-btn-xs"><i class="fa fa-plus"></i> <?php echo L('add_sub_category');?></a>
    <a href="javascript:addedit('?m=admin&c=category&a=edit&catid={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&type={{d.type}}&pc_hash=<?php echo $this->input->get('pc_hash');?>', '<?php echo L('edit');?>')" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> <?php echo L('edit');?></a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="fa fa-trash-o"></i> <?php echo L('delete');?></a>
    <a href="?m=admin&c=category&a=remove&catid={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="layui-btn layui-btn-danger layui-btn-xs"><i class="fa fa-arrows"></i> <?php echo L('remove','','content');?></a>
</script>
<script type="text/html" id="topBtn">
   <a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo $menuid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=0', '<?php echo L('add_category')?>')" class="layui-btn layui-btn-sm"><?php echo L('add_category');?></a>
</script>
<script>
    var pc_file = '<?php echo JS_PATH;?>';
    var editObj=null,ptable=null,treeGrid=null,tableId='treeTable',layer=null;
    layui.config({
        base: pc_file + 'layui/extend/'
    }).extend({
        treeGrid:'treeGrid'
    }).use(['jquery','treeGrid','layer','form'], function(){
        var $=layui.jquery;
        treeGrid = layui.treeGrid;
        layer=layui.layer;
		form = layui.form;
        ptable=treeGrid.render({
            id:tableId
            ,elem: '#'+tableId
            ,idField:'id'
            ,url:'?m=admin&c=category&a=init&pc_hash='+pc_hash
            ,method: 'post'
            ,cellMinWidth: 100
            ,treeId:'id'//树形id字段名称
            ,treeManage:'manage'//树形manage字段名称
            ,treeUpId:'pid'//树形父id字段名称
            ,treeShowName:'title'//以树形式显示的字段
            ,height:'full-140'
            ,isFilter:false
            ,iconOpen:true//是否显示图标【默认显示】
            ,isOpenDefault:true//节点默认是展开还是折叠【默认展开】
            ,cols: [[
                {field: 'id', title: '<?php echo L('number')?>', width: 80, fixed: true},
                {field: 'title', title: '<?php echo L('catname')?>', templet: '#catname'},
                {field: 'typename',align: 'center', title: '<?php echo L('category_type')?>', width: 100},
                {field: 'modelname',align: 'center', title: '<?php echo L('modelname')?>', width: 100},
                {field: 'items',align: 'center', title: '<?php echo L('items')?>', width: 80},
                {field: 'url',align: 'center', title: '<?php echo L('vistor')?>', width: 80, templet: '#url'},
                {field: 'help',align: 'center', title: '<?php echo L('domain_help')?>', width: 90},
                {field: 'ismenu',align: 'center', title: '<?php echo L('menu_nav')?>', width: 90,toolbar: '#ismenu'},
                {field: 'disabled',align: 'center', title: '<?php echo L('可用')?>', width: 85,toolbar: '#disabled'},
                {field: 'iscatpos',align: 'center', title: '<?php echo L('display')?>', width: 85,toolbar: '#iscatpos'},
                {field: 'isleft',align: 'center', title: '<?php echo L('左侧')?>', width: 85,toolbar: '#isleft'},
                {field: 'listorder',align: 'center', title: '<?php echo L('listorder');?>', width: 80, templet: '#listorder'},
                {field: 'manage',title: '<?php echo L('operations_manage');?>',width: 325,align: 'center', toolbar: '#action'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
            ]]
            ,page:false
        });
        treeGrid.on('tool('+tableId+')',function (obj) {
			var data = obj.data;
            if(obj.event === 'del'){
                Dialog.confirm('您确定要删除该记录吗？', function() {
                    $.ajax({
                        type: 'post',
                        url: '?m=admin&c=category&a=delete&dosubmit=1&pc_hash='+pc_hash,
                        data: {catid:data.id},
                        dataType: 'json',
                        success: function(res) {
                            if (res.code == 1) {
                                layer.msg(res.msg,{time:1000,icon:1});
                                obj.del();
                            }else{
                                layer.msg(res.msg,{time:1000,icon:2});
                            }
                        }
                    });
                });
            }
        });
        form.on('switch(ismenu)', function(obj){
            loading = layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var ismenu = obj.elem.checked===true?1:0;
            $.ajax({
                type: 'post',
                url: '?m=admin&c=category&a=ismenu&dosubmit=1&pc_hash='+pc_hash,
                data: {catid:id,ismenu:ismenu},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                        return false;
                    }
                }
            });
        });
        form.on('switch(disabled)', function(obj){
            loading = layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var disabled = obj.elem.checked===true?0:1;
            $.ajax({
                type: 'post',
                url: '?m=admin&c=category&a=disabled&dosubmit=1&pc_hash='+pc_hash,
                data: {catid:id,disabled:disabled},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                        return false;
                    }
                }
            });
        });
        form.on('switch(iscatpos)', function(obj){
            loading = layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var iscatpos = obj.elem.checked===true?1:0;
            $.ajax({
                type: 'post',
                url: '?m=admin&c=category&a=iscatpos&dosubmit=1&pc_hash='+pc_hash,
                data: {catid:id,iscatpos:iscatpos},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                        return false;
                    }
                }
            });
        });
        form.on('switch(isleft)', function(obj){
            loading = layer.load(1, {shade: [0.1,'#fff']});
            var id = this.value;
            var isleft = obj.elem.checked===true?1:0;
            $.ajax({
                type: 'post',
                url: '?m=admin&c=category&a=isleft&dosubmit=1&pc_hash='+pc_hash,
                data: {catid:id,isleft:isleft},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code == 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                        return false;
                    }
                }
            });
        });
        $('body').on('blur','.list_order',function() {
            var id = $(this).attr('data-id');
            var listorder = $(this).val();
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=admin&c=category&a=listorder&dosubmit=1&pc_hash='+pc_hash,
                data: {catid:id,listorder:listorder},
                dataType: 'json',
                success: function(res) {
                    layer.close(loading);
                    if(res.code === 1){
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            location.reload(true);
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                        treeGrid.render;
                    }
                }
            });
        });
    });
    function openAll() {
        var treedata=treeGrid.getDataTreeList(tableId);
        treeGrid.treeOpenAll(tableId,!treedata[0][treeGrid.config.cols.isOpen]);
    }
</script>
<script language="JavaScript">
<!--
function addedit(url, name) {
	artdialog('content_id',url,name,'80%','80%');
}
window.top.$('#display_center_id').css('display','none');
//-->
</script>
</body>
</html>
