<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="file" type="hidden" value="" id="file">
<div class="portlet light bordered">
    <div class="table-list">
        <table width="100%" cellspacing="0">
            <thead>
            <tr class="heading">
                <th width="10"></th>
                <th width="300"> <?php echo L('backup_file_name');?></th>
                <th width="100"> <?php echo L('卷数');?></th>
                <th width="80"> <?php echo L('压缩');?></th>
                <th width="120"> <?php echo L('backup_file_size');?></th>
                <th width="180"> <?php echo L('backup_file_time');?></th>
                <th width="150"> <?php echo L('状态');?></th>
                <th> <?php echo L('database_op');?> </th>
            </tr>
            </thead>
            <tbody>
            <?php 
            if(is_array($list)){
            foreach($list as $i => $info){
            ?>   
            <tr class="odd gradeX">

                <td></td>
                <td><?php echo $info['file'];?></td>
                <td><?php echo $info['part'];?></td>
                <td><?php echo $info['compress'];?></td>
                <td><?php echo $info['size'];?></td>
                <td><?php echo $info['date'];?></td>
                <td>-</td>
                <td>
                    <label><a href="javascript:;" class="btn btn-xs dark btn-restore" data-file="<?php echo $info['file'];?>"><i class="fa fa-reply"></i> <?php echo L('还原');?></a></label>
                    <label><a href="javascript:;" class="btn btn-xs red btn-delete" data-file="<?php echo $info['file'];?>"><i class="fa fa-trash"></i> <?php echo L('删除');?></a></label>
                </td>
            </tr>
            <?php 
            }
            }
            ?>
            <?php 
            if(is_array($infos)){
            foreach($infos as $i => $info){
            ?>   
            <tr class="odd gradeX">

                <td></td>
                <td><?php echo dr_date($info['time'], 'Ymd-His');?></td>
                <td><?php echo $info['part'];?></td>
                <td><?php echo $info['compress'];?></td>
                <td><?php echo format_file_size($info['size']);?></td>
                <td><?php echo dr_date($info['time'], "Y-m-d H:i:s", 'red');?></td>
                <td>-</td>
                <td>
                    <label><a href="?m=content&c=database&a=recovery&time=<?php echo $info['time'];?>" class="btn btn-xs dark btn-restore"><i class="fa fa-reply"></i> <?php echo L('还原');?></a></label>
                    <label><a href="javascript:;" class="btn btn-xs red btn-delete" data-file="<?php echo $info['time'];?>"><i class="fa fa-trash"></i> <?php echo L('删除');?></a></label>
                </td>
            </tr>
            <?php 
            }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</form>
</div>
<script type="text/javascript">
$(function() {
    $(document).on("click", ".btn-restore", function () {
        var that = this, code = ".";
        layer.confirm("确定恢复备份？<br><font color='red'>建议先备份当前数据后再进行恢复操作！！！</font><br><font color='red'>当前数据库将被清空覆盖，请谨慎操作！！！</font>", {
            type: 5,
            shade: 0,
            title: '提示',
            btn: ['确定', '取消'],
            skin: 'layui-layer-dialog layui-layer-fast'
        }, function (index) {
            if (that.href=='javascript:;') {
                $('#file').val($(that).attr('data-file'));
                dr_db_submit('<?php echo L('还原')?>', 'myform', '?m=content&c=database&a=recovery');
            } else {
                layer.closeAll();
                $.get(that.href, success, "json");
            }
        });
        return false;

        function success(data) {
            if (data.code) {
                if (data.gz) {
                    data.msg += code;
                    if (code.length === 5) {
                        code = ".";
                    } else {
                        code += ".";
                    }
                }
                $(that).parent().parent().prev().text(data.msg);
                if (data.data.part) {
                    $.get(that.href,
                            {"part": data.data.part, "start": data.data.start},
                            success,
                            "json"
                    );
                } else {
                    layer.confirm('确定要刷新整个后台吗？', {
                        icon: 3,
                        shade: 0,
                        title: '提示',
                        btn: ['确定', '取消']
                    }, function (index) {
                        layer.closeAll();
                        parent.location.href = '<?php echo SELF;?>';
                    }, function(index) {
                        layer.close(index);
                        window.location.reload(true);
                    });
                }
            } else {
                dr_tips(0, data.msg);
            }
        }
    });
    $(document).on("click", ".btn-delete", function () {
        var that = this;
        layer.confirm('确定删除备份？',{
            icon: 3,
            shade: 0,
            title: '提示',
            btn: ['确定', '取消']
        }, function(index){
            layer.close(index);
            var loading = layer.load(2, {
                shade: [0.3,'#fff'], //0.1透明度的白色背景
                time: 5000
            });
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '?m=admin&c=database&a=delete&pc_hash='+pc_hash,
                data: $('#myform').serialize() + "&time=" + $(that).data('file'),
                success: function(json) {
                    layer.close(loading);
                    // token 更新
                    if (json.token) {
                        var token = json.token;
                        $("#myform input[name='"+token.name+"']").val(token.value);
                    }
                    if (json.code == 1) {
                        setTimeout("window.location.reload(true)", 2000);
                    }
                    dr_tips(json.code, json.msg);
                    return false;
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
                }
            });
        });
    });
});
function dr_db_submit(title, myform, url) {
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 1000
    });
    $.ajax({type: "POST",dataType:"json", url: url, data: $('#'+myform).serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#"+myform+" input[name='"+token.name+"']").val(token.value);
            }
            if (json.code == 1) {
                layer.open({
                    type: 2,
                    title: title,
                    scrollbar: false,
                    resize: true,
                    maxmin: true,
                    shade: 0,
                    area: ['80%', '80%'],
                    success: function(layero, index){
                        // 主要用于后台权限验证
                        var body = layer.getChildFrame('body', index);
                        var json = $(body).html();
                        json = json.replace(/<.*?>/g,"");
                        if (json.indexOf('"code":0') > 0 && json.length < 150){
                            var obj = JSON.parse(json);
                            layer.close(index);
                            dr_tips(0, obj.msg);
                        }
                    },
                    content: json.data.url,
                    cancel: function(index, layero){
                        var body = layer.getChildFrame('body', index);
                        if ($(body).find('#dr_check_status').val() == "1") {
                            layer.confirm('关闭后将中断操作，是否确认关闭呢？', {
                                icon: 3,
                                shade: 0,
                                title: '提示',
                                btn: ['确定', '取消']
                            }, function (index) {
                                layer.closeAll();
                            });
                            return false;
                        }
                        if ($(body).find('#dr_check_status').val() == "0") {
                            layer.confirm('确定要刷新整个后台吗？', {
                                icon: 3,
                                shade: 0,
                                title: '提示',
                                btn: ['确定', '取消']
                            }, function (index) {
                                layer.closeAll();
                                parent.location.href = '<?php echo SELF;?>';
                            }, function(index) {
                                layer.close(index);
                                window.location.reload(true);
                            });
                            return false;
                        }
                    }
                });

            } else {
                dr_tips(0, json.msg, 90000);
            }
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, this, thrownError);
        }
    });
}
</script>
</div>
</div>
</body>
</html>