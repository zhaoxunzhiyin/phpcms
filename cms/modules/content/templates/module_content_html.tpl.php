<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script>
    function dr_save_urlrule(share, catid, value) {
        var index = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 10000
        });
        $.ajax({
            type: "GET",
            cache: false,
            url: '?m=content&c=create_html&a=public_rule_edit&share='+share+'&value='+value+'&catid='+catid+'&pc_hash='+pc_hash,
            dataType: "json",
            success: function (json) {
                layer.close(index);
                if (json.code == 1) {
                    dr_tips(1, json.msg);
                } else {
                    dr_tips(0, json.msg);
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError);
            }
        });
    }
    // ajax关闭或启用
    function dr_cat_ajax_open_close(e, url, fan) {
        var index = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 10000
        });
        $.ajax({
            type: "GET",
            cache: false,
            url: url,
            dataType: "json",
            success: function (json) {
                layer.close(index);
                if (json.code == 1) {
                    if (json.data.value == fan) {
                        $(e).attr('class', 'badge badge-no');
                        $(e).html('<i class="fa fa-times"></i>');
                    } else {
                        $(e).attr('class', 'badge badge-yes');
                        $(e).html('<i class="fa fa-check"></i>');
                    }
                    setTimeout("window.location.reload(true)", 2000);
                }
                dr_tips(json.code, json.msg);
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError);
            }
        });
    }
    function dr_tree_data(catid) {
        var index = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 100000
        });
        var value = $(".select-cat-"+catid).html();
        if (value == '[+]') {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "?m=content&c=create_html&a=public_list_index&pid="+catid,
                success: function(json) {
                    layer.close(index);
                    if (json.code == 1) {
                        $(".dr_catid_"+catid).after(json.msg);
                        $(".select-cat-"+catid).html('[-]');
                        $('.tooltips').tooltip();
                    } else {
                        dr_cmf_tips(json.code, json.msg);
                    }
                },
                error: function(HttpRequest, ajaxOptions, thrownError) {
                    dr_ajax_alert_error(HttpRequest, this, thrownError);
                }
            });
        } else {
            layer.close(index);
            $(".dr_pid_"+catid).remove();
            $(".select-cat-"+catid).html('[+]');
        }
    }
$(function() {
    <?php if (defined('SYS_CAT_POPEN') && SYS_CAT_POPEN) {
    if(is_array($pcats)){
    foreach($pcats as $ii){
    ?>
    dr_tree_data(<?php echo $ii;?>);
    <?php }}}?>
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<div class="note note-danger">
    <p><label><a class="btn btn-sm blue" href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');"> <?php echo L('一键更新栏目');?> </a></label> <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_sync_index&pc_hash='+pc_hash, '500px', '300px')" class="btn btn-sm blue"> <i class="fa fa-cog"></i> <?php echo L('一键开启栏目静态')?> </a></label> <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_sync2_index&pc_hash='+pc_hash, '500px', '300px')" class="btn btn-sm red"> <i class="fa fa-cog"></i> <?php echo L('一键关闭栏目静态')?> </a></label> <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_csync_index&pc_hash='+pc_hash, '500px', '300px')" class="btn btn-sm blue"> <i class="fa fa-cog"></i> <?php echo L('一键开启内容静态')?> </a></label> <label><a href="javascript:iframe_show('<?php echo L('一键更新')?>', '?m=content&c=create_html&a=public_csync2_index&pc_hash='+pc_hash, '500px', '300px')" class="btn btn-sm red"> <i class="fa fa-cog"></i> <?php echo L('一键关闭内容静态')?> </a></label></p>
</div>
<div class="form-horizontal">
    <div class="portlet-body">
        <div class="tab-content">

            <div class="tab-pane active">
                <div class="table-list">

                    <table class="table table-striped table-bordered table-hover table-checkable dataTable">
                        <thead>
                        <tr class="heading">
                            <th width="70" style="text-align:center"> Id </th>
                            <th> <?php echo L('栏目')?> </th>
                            <th width="100" style="text-align:center"> <?php echo L('栏目静态')?> </th>
                            <th width="100" style="text-align:center"> <?php echo L('内容静态')?> </th>
                            <th width="180"> <?php echo L('栏目URL规则')?> </th>
                            <th> <?php echo L('内容URL规则')?> </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php echo $list;?>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>