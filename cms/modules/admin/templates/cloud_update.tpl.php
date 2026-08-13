<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p>升级程序之前，请务必备份全站数据</p>
</div>
<div class="right-card-box">
<form class="form-horizontal" role="form" id="myform">
    <div class="table-list">
        <table class="table table-striped table-bordered table-hover table-checkable dataTable">
            <thead>
            <tr class="heading">
                <th width="80"> 类型</th>
                <th width="250"> 程序名称</th>
                <th width="110"> 更新时间 </th>
                <th width="100"> 版本 </th>
                <th width="110" style="text-align: center"> 备份 </th>
                <th> </th>
            </tr>
            </thead>
            <tbody>
            <tr class="odd gradeX">
                <td>系统</td>
                <td>CMS</td>
                <td> <?php echo CMS_UPDATETIME;?> </td>
                <td><a href="javascript:dr_show_log('<?php echo CMS_ID;?>', '<?php echo CMS_VERSION;?>');"><?php echo CMS_VERSION;?></a></td>
                <td align="center">
                    <?php if ($backup) {?>
                    <a href="javascript:dr_tips(1, '备份目录：<?php echo $backup;?>', -1);" class="label label-success"> 已备份 </a>
                    <?php } else {?>
                    <span class="label label-danger"> 未备份 </span>
                    <?php }?>
                </td>
                <td>
                    <label style="display: none" id="dr_update_cms">
                    <button type="button" onclick="dr_update_cms('?m=admin&c=cloud&a=todo_update&id=<?php echo CMS_ID;?>&dir=cms', '升级前请做好系统备份，你确定要升级吗？', 1)" class="btn red btn-xs"> <i class="fa fa-cloud-upload"></i> 在线升级</button>
                    </label>
                    <label class="dr_check_version" id="dr_row_cms"></label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</form>
</div>
</div>
<script type="text/javascript">

    $(function() {
        $("#dr_row_cms").html("<img style='height:17px' src='<?php echo JS_PATH;?>layer/theme/default/loading-0.gif'>");
        $("#dr_update_cms").hide();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "?m=admin&c=cloud&a=check_version&id=<?php echo CMS_ID;?>&version=<?php echo CMS_VERSION;?>&pc_hash="+pc_hash,
            success: function(json) {
                if (json.code) {
                    $("#dr_row_cms").html(json.msg);
                    $("#dr_update_cms").show();
                } else {
                    $("#dr_row_cms").html("<font color='red'>"+json.msg+"</font>");
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                $("#dr_row_cms").html("<font color='red'>网络异常，请稍后再试</font>");
            }
        });
    });

    // ajax 批量操作确认
    function dr_update_cms(url, msg, remove) {
        layer.confirm(
        msg,
        {
            icon: 3,
            shade: 0,
            title: '提示',
            btn: ['直接升级','备份再升级', '取消']
        }, function(index, layero){
            layer.close(index);
            dr_todo_cms(url+'&is_bf=1');
        }, function(index){
            layer.close(index);
            dr_todo_cms(url+'&is_bf=0');
        });
    }

    function dr_todo_cms(url) {
        var width = '500px';
        var height = '280px';
        if (is_mobile()) {
            width = '100%';
            height = '100%';
        }
        var login_url = '?m=admin&c=cloud&a=login&pc_hash='+pc_hash;
        layer.open({
            type: 2,
            title: '登录官方云账号',
            fix:true,
            scrollbar: false,
            shadeClose: true,
            shade: 0,
            area: [width, height],
            btn: ['确定', '取消'],
            yes: function(index, layero){
                var body = layer.getChildFrame('body', index);
                $(body).find('.form-group').removeClass('has-error');
                // 延迟加载
                var loading = layer.load(2, {
                    shade: [0.3,'#fff'], //0.1透明度的白色背景
                    time: 100000000
                });
                $.ajax({type: "POST",dataType:"json", url: login_url, data: $(body).find('#myform').serialize(),
                    success: function(json) {
                        layer.close(loading);
                        // token 更新
                        if (json.token) {
                            var token = json.token;
                            $(body).find("#myform input[name='"+token.name+"']").val(token.value);
                        }
                        if (json.code == 1) {
                            layer.close(index);
                            var yz_url = url+'&'+$('#myform').serialize()+'&ls='+json.data;
                            // 验证成功
                            layer.open({
                                type: 2,
                                title: '升级程序',
                                scrollbar: false,
                                resize: true,
                                maxmin: true, //开启最大化最小化按钮
                                shade: 0,
                                area: ['80%', '80%'],
                                success: function(layero, index){
                                    // 主要用于后台权限验证
                                    var body = layer.getChildFrame('body', index);
                                    var json = $(body).html();
                                    if (json.indexOf('"code":0') > 0 && json.length < 150){
                                        var obj = JSON.parse(json);
                                        layer.closeAll(index);
                                        dr_tips(0, obj.msg);
                                    }
                                },
                                content: yz_url
                            });
                        } else {
                            $(body).find('#dr_row_'+json.data.field).addClass('has-error');
                            dr_tips(0, json.msg);
                        }
                        return false;
                    },
                    error: function(HttpRequest, ajaxOptions, thrownError) {
                        dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
                    }
                });
                return false;
            },
            content: login_url+'&is_iframe=1'
        });
    }

    function dr_beifen_cms(url, msg, remove) {
        layer.confirm(
                msg,
                {
                    icon: 3,
                    shade: 0,
                    title: '提示',
                    btn: ['确定', '取消']
                }, function(index){
                    layer.close(index);
                    layer.open({
                        type: 2,
                        title: '备份程序',
                        scrollbar: false,
                        resize: true,
                        maxmin: true, //开启最大化最小化按钮
                        shade: 0,
                        area: ['80%', '80%'],
                        success: function(layero, index){
                            // 主要用于后台权限验证
                            var body = layer.getChildFrame('body', index);
                            var json = $(body).html();
                            if (json.indexOf('"code":0') > 0 && json.length < 150){
                                var obj = JSON.parse(json);
                                layer.closeAll(index);
                                dr_tips(0, obj.msg);
                            }
                        },
                        content: url
                    });
                });
    }
    
    function dr_show_log(id, v) {
        layer.open({
            type: 2,
            title: '版本日志',
            scrollbar: false,
            resize: true,
            maxmin: true, //开启最大化最小化按钮
            shade: 0,
            area: ['80%', '80%'],
            content: '?m=admin&c=cloud&a=log_show&id='+id+'&version='+v+'&pc_hash='+pc_hash,
        });
    }
</script>
</div>
</div>
</body>
</html>