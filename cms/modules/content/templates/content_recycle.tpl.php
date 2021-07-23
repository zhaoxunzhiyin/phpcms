<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="<?php echo CSS_PATH?>bootstrap/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>layui/css/layui.css" media="all" />
<link rel="stylesheet" href="<?php echo CSS_PATH;?>admin/css/global.css" media="all" />
<style type="text/css">
* {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
.list_order {text-align: left;}
.btn-group {margin-left: 10px;}
.measure-input, input.date, input.endDate, .input-focus, #keyword, #search {height: 32px;line-height: 32px;}
.layui-input, .layui-laypage-btn {color: #000000;}
</style>
<script type="text/javascript" src="<?php echo JS_PATH;?>layui/layui.js"></script>
<div class="admin-main layui-anim layui-anim-upbit">
    <!--<fieldset class="layui-elem-field layui-field-title">
        <legend><?php echo L('list');?></legend>
    </fieldset>-->
    <blockquote class="layui-elem-quote">
        <a href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" class="layui-btn layui-btn-sm<?php if($steps==0 && !$this->input->get('reject')) echo ' on';?>">
            <i class="fa fa-check  "></i> <?php echo L('check_passed');?>
        </a>
        <a href="javascript:;" onclick="javascript:$('#searchid').toggle();" class="layui-btn layui-btn-sm layui-btn-normal">
            <i class="fa fa-search"></i> <?php echo L('search');?>
        </a>
    </blockquote>
    <div class="demoTable" id="searchid" style="display:none;">
        <?php echo L('addtime');?>：
        <?php echo form::date('start_time',$this->input->get('start_time'),0,0,'false');?>- &nbsp;<?php echo form::date('end_time',$this->input->get('end_time'),0,0,'false');?>
                <select id="posids" name="posids"><option value='' <?php if($this->input->get('posids')=='') echo 'selected';?>><?php echo L('all');?></option>
                <option value="1" <?php if($this->input->get('posids')==1) echo 'selected';?>><?php echo L('elite');?></option>
                <option value="2" <?php if($this->input->get('posids')==2) echo 'selected';?>><?php echo L('no_elite');?></option>
                </select>                
                <select id="searchtype" name="searchtype">
                    <option value='0' <?php if($this->input->get('searchtype')==0) echo 'selected';?>><?php echo L('title');?></option>
                    <option value='1' <?php if($this->input->get('searchtype')==1) echo 'selected';?>><?php echo L('intro');?></option>
                    <option value='2' <?php if($this->input->get('searchtype')==2) echo 'selected';?>><?php echo L('username');?></option>
                    <option value='3' <?php if($this->input->get('searchtype')==3) echo 'selected';?>>ID</option>
                </select>
        <div class="layui-inline">
            <input class="layui-input" name="keyword" id="keyword" <?php if(isset($keyword)) echo $keyword;?> placeholder="请输入关键字">
        </div>
        <button class="layui-btn" id="search" data-type="reload"><i class="fa fa-search"></i> <?php echo L('search');?></button>
        <div style="clear: both;"></div>
    </div>
    <table class="layui-table" id="list" lay-filter="list"></table>
</div>
<script type="text/html" id="attribute">
    {{# if(d.thumb){ }}
    <img src="<?php echo IMG_PATH;?>icon/small_img.gif" onmouseover="layer.tips('<img src={{d.thumb}}>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();">
    {{# } }}
    {{# if(d.posids==1){ }}
    <img src="<?php echo IMG_PATH;?>icon/small_elite.png" onmouseover="layer.tips('<?php echo L('elite');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } }}
    {{# if(d.islink==1){ }}
    <img src="<?php echo IMG_PATH;?>icon/link.png" onmouseover="layer.tips('<?php echo L('islink_url');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } }}
</script>
<script type="text/html" id="hits">
    <span style="display: block;" onmouseover="layer.tips('<?php echo L('today_hits');?>：{{d.dayviews}}<br><?php echo L('yestoday_hits');?>：{{d.yesterdayviews}}<br><?php echo L('week_hits');?>：{{d.weekviews}}<br><?php echo L('month_hits');?>：{{d.monthviews}}',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">{{d.views}}</span>
</script>
<script type="text/html" id="username">
    {{# if(d.sysadd==0){ }}
    <a href='javascript:;' onclick="omnipotent('member','?m=member&c=member&a=memberinfo&username={{d.deusername}}&pc_hash=<?php echo $this->input->get('pc_hash');?>','<?php echo L('view_memberlinfo');?>',1,700,500);">{{d.username}}</a><img src="<?php echo IMG_PATH;?>icon/contribute.png" onmouseover="layer.tips('<?php echo L('member_contribute');?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
    {{# } else { }}
    {{d.username}}
    {{# } }}
</script>
<script type="text/html" id="listorder">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input" value="{{d.listorder}}" size="10"/>
</script>
<script type="text/html" id="action">
    <a href="{{d.url}}" target="_blank" class="layui-btn layui-btn-xs layui-btn-normal"><i class="fa fa-eye"></i> <?php echo L('preview');?></a>
    <a class="layui-btn layui-btn-xs" lay-event="restore"><i class="fa fa-window-restore"></i> <?php echo L('restore');?></a>
</script>
<script type="text/html" id="topBtn">
    <div class="btn-group">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="dropdown" data-toggle="dropdown"><i class="fa fa-files-o"></i> <?php echo L('批量操作')?></button>
        <div class="dropdown dropdown-bottom-left">
            <?php if($status==100) {?>
            <a href="javascript:;" class="dropdown-item" id="recycle"><i class="fa fa-window-restore"></i> <?php echo L('还原');?></a>
            <div class="dropdown-line"></div>
            <?php }?>
            <a href="javascript:;" class="dropdown-item" id="delAll"><i class="fa fa-trash-o"></i> <?php echo L('thorough');?><?php echo L('delete');?></a>
        </div>
    </div>
</script>
<script>
layui.use(['table'], function(){
    var table = layui.table, $ = layui.jquery;
    var tableIn = table.render({
        id: 'content',
        elem: '#list',
        url:'?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
        method: 'post',
        toolbar: '#topBtn',
        cellMinWidth: 80,
        page: true,
        cols: [[
            {type: "checkbox", fixed: 'left'},
            {field: 'id', title: '<?php echo L('number');?>', width: 80, sort: true},
            {field: 'title', title: '<?php echo L('title');?>', minWidth:340, sort: true, edit: 'text'},
            {field: 'attribute', title: '<?php echo L('attribute');?>', templet: '#attribute', width:100},
            {field: 'hits', title: '<?php echo L('hits');?>', width:100, templet: '#hits', sort: true},
            {field: 'publish_user', title: '<?php echo L('publish_user');?>', width:100, templet: '#username', sort: true},
            {field: 'updatetime', title: '<?php echo L('updatetime');?>', width:180, sort: true},
            {field: 'listorder', title: '<?php echo L('listorder');?>', width:80, templet: '#listorder', sort: true},
            {width: 180, align: 'center', toolbar: '#action',title:'<?php echo L('operations_manage');?>'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
        ]],
        limit: 10
    });
    //搜索
    $('#search').on('click', function () {
        var keyword = $('#keyword').val();
        var start_time = $('#start_time').val();
        var end_time = $('#end_time').val();
        var posids = $('#posids').val();
        var searchtype = $('#searchtype').val();
        if ($.trim(keyword) === '') {
            layer.msg('请输入关键字！', {icon: 0});
            return;
        }
        tableIn.reload({ page: {page: 1}, where: {keyword: keyword,start_time: start_time,end_time: end_time,posids: posids,searchtype: searchtype} });
    });
    table.on('tool(list)', function(obj) {
        var data = obj.data;
        if(obj.event === 'restore'){
            Dialog.confirm('确定要还原此内容吗？', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=0&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
                    data: {id:data.id},
                    dataType: 'json',
                    success: function(res) {
                        layer.close(loading);
                        if (res.code==1) {
	                        layer.msg(res.msg, {time: 1000, icon: 1}, function () {
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
    //监听单元格编辑
    table.on('edit(list)',function(obj) {
        var value = obj.value, data = obj.data, field = obj.field;
        if (field=='title' && value=='') {
            layer.tips('标题不能为空',this,{tips: [1, '#000']});
            return false;
        }else{
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=update&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
    $('body').on('blur','.list_order',function() {
        var id = $(this).attr('name');
        var listorder = $(this).val();
        var loading = layer.load(1, {shade: [0.1, '#fff']});
        $.ajax({
            type: 'post',
            url: '?m=content&c=content&a=listorder&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
            data: {id:id,listorder:listorder},
            dataType: 'json',
            success: function(res) {
                layer.close(loading);
                if (res.code == 1) {
                    layer.msg(res.msg, {time: 1000, icon: 1}, function () {
                        tableIn.reload();
                    });
                }else{
                    layer.msg(res.msg,{time:1000,icon:2});
                }
            }
        });
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
            Dialog.confirm('确认要删除选中的内容吗？', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=delete&dosubmit=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
    $('body').on('click','#recycle',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            Dialog.confirm('确认要还原选中的内容吗？', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=0&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash=<?php echo $pc_hash;?>',
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