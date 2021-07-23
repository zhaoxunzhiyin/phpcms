<?php 
    defined('IN_ADMIN') or exit('No permission resources.');
    include $this->admin_tpl('header', 'admin');
?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<style type="text/css">
.list_order {text-align: left;}
.btn-group {margin-left: 10px;}
.measure-input, input.date, input.endDate, .input-focus {height: 32px;}
.layui-input, .layui-laypage-btn {color: #000000;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<div class="admin-main layui-anim layui-anim-upbit">
    <!--<fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo L('存储策略');?></legend>
    </fieldset>-->
    <blockquote class="layui-elem-quote">
        <a href="?m=attachment&c=attachment&a=remote_add&menuid=<?php echo $this->input->get('menuid');?>" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus"></i> <?php echo L('add');?>
        </a>
    </blockquote>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<script type="text/html" id="action">
    <a href="?m=attachment&c=attachment&a=remote_edit&id={{d.id}}&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> <?php echo L('edit');?></a>
    <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="delete"><i class="fa fa-trash-o"></i> <?php echo L('delete')?></a>
</script>
<script type="text/html" id="topBtn">
    <button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="delAll"><i class="fa fa-trash-o"></i> <?php echo L('thorough');?><?php echo L('delete');?></button>
</script>
<script>
layui.use(['table'], function(){
    var table = layui.table, $ = layui.jquery;
    var tableIn = table.render({
        id: 'content',
        elem: '#list',
        url:'?m=attachment&c=attachment&a=remote&pc_hash='+pc_hash,
        method: 'post',
        toolbar: '#topBtn',
        cellMinWidth: 80,
        page: true,
        cols: [[
            {type: "checkbox", fixed: 'left'},
            {field: 'id', title: '<?php echo L('number');?>', width: 80, sort: true},
            {field: 'type', title: '<?php echo L('存储类型');?>', width:120, sort: true},
            {field: 'name', title: '<?php echo L('名称');?>', minWidth:340, sort: true, edit: 'text'},
            {width: 160, align: 'center', toolbar: '#action',title:'<?php echo L('operations_manage');?>'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
        ]],
        limit: 10
    });
    //监听单元格编辑
    table.on('edit(list)',function(obj) {
        var value = obj.value, data = obj.data, field = obj.field;
        if (field=='name' && value=='') {
            layer.tips('<?php echo L('attachment_name_not')?>',this,{tips: [1, '#000']});
            return false;
        }else{
            $.ajax({
                type: 'post',
                url: '?m=attachment&c=attachment&a=update&dosubmit=1&pc_hash='+pc_hash,
                data: {id:data.id,field:field,value:value},
                dataType: 'json',
                success: function(res) {
                    if (res.code == 1) {
                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                            tableIn.reload();
                        });
                    }else{
                        layer.msg(res.msg,{time:1000,icon:2});
                    }
                }
            });
        }
    });
    table.on('tool(list)', function(obj) {
        var data = obj.data;
        if(obj.event === 'delete'){
            Dialog.confirm('<?php echo L('del_confirm')?>', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=attachment&c=attachment&a=delete&pc_hash='+pc_hash,
                    data: {id:data.id},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
                            layer.msg(res.msg,{icon: 1, time: 1000},function(){
                                tableIn.reload();
                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    }
                });
            });
        }
    });
    $('body').on('click','#delAll',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            Dialog.confirm('<?php echo L('del_confirm')?>', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=attachment&c=attachment&a=public_delete_all',
                    data: {ids: ids},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
                            layer.msg(res.msg,{icon: 1, time: 1000},function(){
                                tableIn.reload();
                            });
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    }
                });
            });
        }
    })
});
</script>
</body>
</html>