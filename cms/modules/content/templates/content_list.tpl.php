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
        <a href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=add&menuid=&catid=<?php echo $catid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>','<?php echo L('add_content');?>');" class="layui-btn layui-btn-sm">
            <i class="fa fa-plus"></i> <?php echo L('add_content');?>
        </a>
        <a href="?m=content&c=content&a=init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" class="layui-btn layui-btn-sm<?php if($steps==0 && !$this->input->get('reject')) echo ' on';?>">
            <i class="fa fa-check"></i> <?php echo L('check_passed');?>
        </a>
        <a href="?m=content&c=content&a=recycle_init&catid=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>" class="layui-btn layui-btn-sm layui-btn-danger">
            <i class="fa fa-trash-o"></i> <?php echo L('recycle');?>
        </a>
        <?php echo $workflow_menu;?>
        <a href="javascript:;" onclick="javascript:$('#searchid').toggle();" class="layui-btn layui-btn-sm layui-btn-normal">
            <i class="fa fa-search"></i> <?php echo L('search');?>
        </a>
        <?php if($category['ishtml']) {?>
        <a href="javascript:;" onclick="dr_bfb('<?php echo L('update_htmls',array('catname'=>$category['catname']));?>', 'myform', '?m=content&c=create_html&a=category&pagesize=30&dosubmit=1&modelid=0&catids[0]=<?php echo $catid;?>&pc_hash=<?php echo $pc_hash;?>&referer=<?php echo urlencode($_SERVER['QUERY_STRING']);?>')" class="layui-btn layui-btn-sm layui-btn-normal">
            <i class="fa fa-html5"></i> <?php echo L('update_htmls',array('catname'=>$category['catname']));?>
        </a>
        <?php }?>
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
    <a href="javascript:;" onclick="javascript:contentopen('?m=content&c=content&a=edit&catid={{d.catid}}&id={{d.id}}','<?php echo L('edit').L('content');?>')" class="layui-btn layui-btn-xs"><i class="fa fa-edit"></i> <?php echo L('edit');?></a>
    <a href="javascript:view_comment('{{d.idencode}}','{{d.safetitle}}')" class="layui-btn layui-btn-xs layui-btn-danger"><i class="fa fa-comment"></i> <?php echo L('comment');?></a>
</script>
<script type="text/html" id="topBtn">
    <div class="btn-group">
        <button type="button" class="layui-btn layui-btn-sm layui-btn-normal" id="dropdown" data-toggle="dropdown"><i class="fa fa-files-o"></i> <?php echo L('批量操作')?></button>
        <div class="dropdown dropdown-bottom-left">
            <a href="javascript:;" class="dropdown-item" id="remove"><i class="fa fa-arrows"></i> <?php echo L('remove');?></a>
            <?php if($category['content_ishtml']) {?>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="createhtml"><i class="fa fa-check"></i> <?php echo L('createhtml');?></a>
            <?php }
            if($status!=99) {?>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="passed"><i class="fa fa-check"></i> <?php echo L('passed_checked');?></a>
            <?php }?>
            <?php if(!$this->input->get('reject')) { ?>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="push"><i class="fa fa-window-restore"></i> <?php echo L('push');?></a>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="copy"><i class="fa fa-files-o"></i> <?php echo L('copy');?></a>
            <?php }?>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="recycle"><i class="fa fa-trash-o"></i> <?php echo L('in_recycle');?></a>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="delAll"><i class="fa fa-trash-o"></i> <?php echo L('thorough');?><?php echo L('delete');?></a>
            <?php if (module_exists('bdts')) {?>
            <div class="dropdown-line"></div>
            <a href="javascript:;" class="dropdown-item" id="bdts"><i class="fa fa-paw"></i> <?php echo L('批量百度主动推送');?></a>
            <?php }?>
        </div>
    </div>
    <?php if(!$this->input->get('reject')) { ?>
    <?php if($workflow_menu) { ?><button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="reject_check"><?php echo L('reject');?></button>
    <div id='reject_content' style='background-color: #fff;border:#006699 solid 1px;position:absolute;z-index:10;padding:1px;display:none;'>
    <table cellpadding='0' cellspacing='1' border='0'><tr><tr><td colspan='2'><textarea name='reject_c' id='reject_c' style='width:300px;height:46px;' onfocus="if(this.value == this.defaultValue) this.value = ''" onblur="if(this.value.replace(' ','') == '') this.value = this.defaultValue;"><?php echo L('reject_msg');?></textarea></td><td><button type="button" class="layui-btn layui-btn-danger layui-btn-sm" id="reject_check1"><?php echo L('submit');?></button></td></tr>
    </table>
    </div>
    <?php }}?>
</script>
<script>
layui.use(['table'], function(){
    var table = layui.table, $ = layui.jquery;
    var tableIn = table.render({
        id: 'content',
        elem: '#list',
        url:'?m=content&c=content&a=init&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
        method: 'post',
        toolbar: '#topBtn',
        cellMinWidth: 80,
        page: true,
        cols: [[
            {type: "checkbox", fixed: 'left'},
            {field: 'id', title: '<?php echo L('number');?>', width: 80, sort: true},
            {field: 'title', title: '<?php echo L('title');?>', minWidth:320, sort: true, edit: 'text'},
            {field: 'attribute', title: '<?php echo L('attribute');?>', templet: '#attribute', width:100},
            {field: 'hits', title: '<?php echo L('hits');?>', width:100, templet: '#hits', sort: true},
            {field: 'publish_user', title: '<?php echo L('publish_user');?>', width:100, templet: '#username', sort: true},
            {field: 'updatetime', title: '<?php echo L('updatetime');?>', width:180, sort: true},
            {field: 'listorder', title: '<?php echo L('listorder');?>', width:80, templet: '#listorder', sort: true},
            {width: 240, align: 'center', toolbar: '#action',title:'<?php echo L('operations_manage');?>'<?php if(!is_mobile(0)) {?>, fixed: 'right'<?php }?>}
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
            Dialog.confirm('确认要删除选中的内容吗？您可以在回收站恢复！', function() {
                var loading = layer.load(1, {shade: [0.1, '#fff']});
                $.ajax({
                    type: 'post',
                    url: '?m=content&c=content&a=recycle&dosubmit=1&recycle=1&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
    $('body').on('click','#push',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentpush','?m=content&c=push&action=position_list&catid=<?php echo $catid?>&modelid=<?php echo $modelid?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('push');?>：',800,500);
        }
    })
    $('body').on('click','#copy',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentcopy','?m=content&c=copy&a=init&module=content&classname=push_api&action=category_list_copy&tpl=copy_to_category&modelid=<?php echo $modelid?>&catid=<?php echo $catid?>&id='+ids.toString().replace(new RegExp(",","g"),'|'),'<?php echo L('copy');?>：',800,500);
        }
    })
    <?php if (module_exists('bdts')) {?>
    $('body').on('click','#bdts',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=bdts&c=bdts&a=add&modelid=<?php echo $modelid;?>&pc_hash='+pc_hash,
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
        }
    })
    <?php }?>
    $('body').on('click','#remove',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            artdialog('contentremove','?m=content&c=content&a=remove&catid=<?php echo $catid?>&ids='+ids,'<?php echo L('remove');?>：',800,500);
        }
    })
    <?php if($category['content_ishtml']) {?>
    $('body').on('click','#createhtml',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=create_html&a=batch_show&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
                data: {ids: ids, dosubmit: 1},
                dataType: 'json',
                success:function(json) {
                    layer.close(loading);
                    if (json.code == 1) {
                        layer.open({
                            type:2,
                            title:'生成内容页面',
                            scrollbar:false,
                            resize:true,
                            maxmin:true,
                            shade:0,
                            area:[ "80%", "80%" ],
                            success:function(layero, index) {
                                var body = layer.getChildFrame("body", index);
                                var json = $(body).html();
                                if (json.indexOf('"code":0') > 0 && json.length < 150) {
                                    var obj = JSON.parse(json);
                                    layer.close(loading);
                                    dr_tips(0, obj.msg);
                                }
                            },
                            content:json.data.url,
                            cancel: function(e, t) {
                                return layer.confirm("关闭后将中断操作，是否确认关闭呢？", {
                                    icon: 3,
                                    shade: 0,
                                    title: "提示",
                                    btn: ["确定", "取消"]
                                }, function(e) {
                                    layer.closeAll()
                                }), !1
                            }
                        });
                    } else {
                        dr_tips(0, json.msg, 90000);
                    }
                    return false;
                },
                error:function(HttpRequest, ajaxOptions, thrownError) {
                    dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
                }
            });
        }
    })
    <?php }?>
    <?php if($workflow_menu) {?>
    $('body').on('click','#reject_check',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            $('#reject_content').toggle();
        }
    })
    $('body').on('click','#reject_check1',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&reject=1&pc_hash='+pc_hash,
                data: {ids: ids, reject_c: $('#reject_c').val()},
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
        }
    })
    <?php }?>
    <?php if($status!=99) {?>
    $('body').on('click','#passed',function() {
        var checkStatus = table.checkStatus('content'); //content即为参数id设定的值
        var ids = [];
        $(checkStatus.data).each(function (i, o) {
            ids.push(o.id);
        });
        if (ids.toString()=='') {
            layer.msg('\u81f3\u5c11\u9009\u62e9\u4e00\u6761\u4fe1\u606f',{time:1000,icon:2});
        } else {
            var loading = layer.load(1, {shade: [0.1, '#fff']});
            $.ajax({
                type: 'post',
                url: '?m=content&c=content&a=pass&catid=<?php echo $catid;?>&steps=<?php echo $steps;?>&pc_hash='+pc_hash,
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
        }
    })
    <?php }?>
});
</script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>cookie.js"></script>
<script type="text/javascript"> 
<!--
function view_comment(id, name) {
    var diag = new Dialog({
        id:'view_comment',
        title:'<?php echo L('view_comment');?>：'+name,
        url:'<?php echo SELF;?>?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id+'&pc_hash='+pc_hash,
        width:800,
        height:500,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();
}
setcookie('refersh_time', 0);
function refersh_window() {
    var refersh_time = getcookie('refersh_time');
    if(refersh_time==1) {
        location.reload(true);
    }
}
setInterval("refersh_window()", 3000);
//-->
</script>
</body>
</html>