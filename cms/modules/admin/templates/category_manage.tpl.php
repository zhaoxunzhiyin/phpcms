<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script>
function dr_tree_data(catid) {
    var index = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 100000
    });
    var value = $('.select-cat-'+catid).html();
    if (value == '[+]') {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '?m=admin&c=category&a=public_list_index&pid='+catid,
            success: function(json) {
                layer.close(index);
                if (json.code == 1) {
                    <?php if (defined('SYS_TOTAL_POPEN') && SYS_TOTAL_POPEN) {?>
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "?m=admin&c=category&a=public_ctotal",
                        data: {
                            'catid': json.data,
                            '<?php echo SYS_TOKEN_NAME;?>': "<?php echo csrf_hash();?>",
                        },
                        success: function(json2) {
                            if (json2.code == 1) {
                                eval(json2.msg);
                            }
                        },
                        error: function(HttpRequest, ajaxOptions, thrownError) {
                            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
                        }
                    });
                    <?php }?>
                    $('.dr_catid_'+catid).after(json.msg);
                    $('.select-cat-'+catid).html('[-]');
                    $('.tooltips').tooltip();
                } else {
                    dr_tips(json.code, json.msg);
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, this, thrownError);
            }
        });
    } else {
        layer.close(index);
        $('.dr_pid_'+catid).remove();
        $('.select-cat-'+catid).html('[+]');
    }
}
function dr_scjt() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '?m=admin&c=category&a=public_scjt_edit&pc_hash='+pc_hash,
        data: $('#myform').serialize(),
        success: function(json) {
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            if (json.code == 1) {
                dr_bfb('<?php echo L('生成栏目页面');?>', '', json.msg);
            } else {
                dr_tips(json.code, json.msg);
            }

        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, this, thrownError);
        }
    });
}
$(function() {
    <?php if (defined('SYS_CAT_POPEN') && SYS_CAT_POPEN) {
    if(is_array($pcats)){
    foreach($pcats as $ii){
    ?>
    dr_tree_data(<?php echo $ii;?>);
    <?php }}}?>
    <?php if (defined('SYS_TOTAL_POPEN') && SYS_TOTAL_POPEN) {?>
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "?m=admin&c=category&a=public_ctotal",
        data: <?php echo json_encode(['catid'=>$tcats, SYS_TOKEN_NAME => csrf_hash()]);?>,
        success: function(json) {
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            if (json.code == 1) {
                eval(json.msg);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
    <?php }?>
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                <div class="page-body">
<div class="note note-danger">
    <p><a href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');"><?php echo L('变更栏目属性之后，需要一键更新栏目配置信息');?></a></p>
</div>
<div class="right-card-box">
    <form class="form-horizontal" role="form" id="myform">
        <div class="table-list">
            <table class="table-checkable">
                <thead>
                <?php echo $cat_head;?>
                </thead>
                <tbody>
                <?php echo $cat_list;?>
                </tbody>
            </table>
        </div>
        <div class="row list-footer table-checkable ">
            <div class="col-md-12 list-select">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                    <span></span>
                </label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=category&a=delete&pc_hash='+pc_hash, '<?php echo L('将同步删除其下级所有栏目和内容，且无法恢复，你确定要删除它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
                <label><?php echo $move_select;?></label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=category&a=public_move_edit&pc_hash='+pc_hash, '<?php echo L('你确定要移动它们吗？');?>', 1)" class="btn blue btn-sm"> <i class="fa fa-edit"></i> <?php echo L('move');?></button></label>
                <label><button type="button" onclick="dr_scjt()" class="btn green btn-sm"> <i class="fa fa-html5"></i> <?php echo L('生成栏目静态');?> </button></label>
                <label>
                    <div class="btn-group dropup">
                        <a class="btn blue btn-sm dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false" href="javascript:;"><i class="fa fa-cogs"></i> <?php echo L('批量静态');?> <i class="fa fa-angle-up"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:;" onclick="ajax_option('?m=admin&c=category&a=public_htmlall_edit&pc_hash='+pc_hash, '<?php echo L('你确定要批量设置栏目为静态模式吗？');?>', 1)" > <?php echo L('设置栏目静态');?></a></li>
                            <li><a href="javascript:;" onclick="ajax_option('?m=admin&c=category&a=public_phpall_edit&pc_hash='+pc_hash, '<?php echo L('你确定要批量设置栏目为动态模式吗？');?>', 1)" > <?php echo L('设置栏目动态');?></a></li>
                            <li class="divider"> </li>
                            <li><a href="javascript:;" onclick="ajax_option('?m=admin&c=category&a=public_htmlall_edit&type=1&pc_hash='+pc_hash, '<?php echo L('你确定要批量设置内容为静态模式吗？');?>', 1)" > <?php echo L('设置内容静态');?></a></li>
                            <li><a href="javascript:;" onclick="ajax_option('?m=admin&c=category&a=public_phpall_edit&type=1&pc_hash='+pc_hash, '<?php echo L('你确定要批量设置内容为动态模式吗？');?>', 1)" > <?php echo L('设置内容动态');?></a></li>
                        </ul>
                    </div>
                </label>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>