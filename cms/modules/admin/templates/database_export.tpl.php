<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="dosubmit" type="hidden" value="1">
<div class="portlet light bordered">
<?php if ($database['default']['type']=='sqlite3') {?>
<div class="note note-danger">
    <p>
        <a href="javascript:;" class="btn green btn-backup"><i class="fa fa-compress"></i> 立即备份</a>
        <span class="text-danger">注意：Sqlite数据库只支持直接备份。</span>
    </p>
</div>
<?php } else {?>
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li class="active">
                <a data-toggle="tab_0"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('database_export').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-th-large"></i> <?php if (is_pc()) {echo L('database_export');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="form-body">
            <div class="form-group" id="dr_row_sys_admin_pagesize">
                <label class="col-md-2 control-label"><?php echo L('分卷大小')?></label>
                <div class="col-md-9">
                    <div class="input-inline input-medium">
                        <div class="input-group">
                            <input type="text" name="sizelimit" id="sizelimit" value="2" class="form-control">
                            <span class="input-group-addon">
                                <?php echo L('MB')?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label"><?php echo L('启用压缩')?></label>
                <div class="col-md-9">
                    <input type="checkbox" id="compress" name="compress" value="1" checked data-on-text="<?php echo L('open')?>" data-off-text="<?php echo L('close')?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                </div>
            </div>
            <div class="form-group" id="levels">
                <label class="col-md-2 control-label"><?php echo L('压缩级别')?></label>
                <div class="col-md-9">
                    <?php
                    $levels = array();
                    for($i=1;$i<10;$i++){
                        $levels[$i] = $i;
                    }
                    echo form::select($levels, 9, 'name="level" id="level"')?>
                    <span class="help-block"><?php echo L('压缩级别，1：普通；4：一般；9：最高')?></span>
                </div>
            </div>
        </div>
        <div class="table-list">
<table width="100%" cellspacing="0" class="table-checkable">
 <?php if(is_array($infos)){?>
    <thead>
       <tr>
           <th class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
           <th width="280"><?php echo L('database_tblname')?></th>
           <th width="100"><?php echo L('database_records')?></th>
           <th width="150"><?php echo L('database_size')?></th>
           <th width="180"><?php echo L('updatetime')?></th>
           <th width="150">备份状态</th>
           <th><?php echo L('database_op')?></th>
       </tr>
    </thead>
    <tbody>
    <?php foreach($infos['cmstables'] as $v){?>
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="tables[]" value="<?php echo $v['name']?>" />
                        <span></span>
                    </label></td>
    <td align="center"><a href="javascript:void(0);" onclick="show('<?php echo $v['name']?>')"><?php echo $v['name']?></a></td>
    <td align="center"><?php echo $v['rows']?></td>
    <td align="center"><?php echo format_file_size($v['size'])?></td>
    <td align="center"><?php echo dr_date($v['updatetime'], null, 'red')?></td>
    <td class="info">未备份</td>
    <td align="center"><label><a href="?m=admin&c=database&a=public_repair&operation=optimize&tables=<?php echo $v['name']?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs green"> <i class="fa fa-refresh"></i> <?php echo L('database_optimize')?></a></label>
        <label><a href="?m=admin&c=database&a=public_repair&operation=repair&tables=<?php echo $v['name']?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs blue"> <i class="fa fa-wrench"></i> <?php echo L('database_repair')?></a></label>
        <label><a href="?m=admin&c=database&a=public_repair&operation=flush&tables=<?php echo $v['name']?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs red"> <i class="fa fa-retweet"></i> <?php echo L('database_flush')?></a></label>
        <label><a href="?m=admin&c=database&a=public_repair&operation=jc&tables=<?php echo $v['name']?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs yellow"> <i class="fa fa-cogs"></i> <?php echo L('database_check')?></a></label>
        <label><a href="javascript:void(0);" onclick="showcreat('<?php echo $v['name']?>')" class="btn btn-xs dark"> <i class="fa fa-database"></i> <?php echo L('database_showcreat')?></a></label></td>
    </tr>
    <?php }?>
    </tbody>
<?php }?>
</table>
</div>
<?php if(is_array($infos)){?>
<div class="row list-footer table-checkable">
    <div class="col-md-12 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>
        <label><button name="dosubmit" type="button" class="btn green btn-sm btn-backup"> <i class="fa fa-database"></i> <?php echo L('backup_starting');?></button></label>
        <label><button name="dosubmit" type="button" onclick="dr_bfb_submit('<?php echo L('batch_optimize')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=y')" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('batch_optimize');?></button></label>
        <label><button name="dosubmit" type="button" onclick="dr_bfb_submit('<?php echo L('batch_repair')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=x')" class="btn blue btn-sm"> <i class="fa fa-wrench"></i> <?php echo L('batch_repair');?></button></label>
        <label><button name="dosubmit" type="button" onclick="dr_bfb_submit('<?php echo L('batch_check')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=jc')" class="btn yellow btn-sm"> <i class="fa fa-cogs"></i> <?php echo L('batch_check');?></button></label>
        <label><button name="dosubmit" type="button" onclick="dr_bfb_submit('<?php echo L('batch_flush')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=s')" class="btn red btn-sm"> <i class="fa fa-retweet"></i> <?php echo L('batch_flush');?></button></label>
        <label><button name="dosubmit" type="button" onclick="dr_bfb_submit('<?php echo L('batch_utf8mb4')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=ut')" class="btn dark btn-sm"> <i class="fa fa-database"></i> <?php echo L('batch_utf8mb4');?></button></label>
    </div>
</div>
<?php }?>
    </div>
<?php }?>
</div>
</form>
</div>
</div>
</div>
</body>
<script type="text/javascript">
$(function() {
    <?php if ($database['default']['type']=='sqlite3') {?>
    $(document).on("click", ".btn-backup", function () {
        // 延迟加载
        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 5000
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '?m=admin&c=database&a=backup&pc_hash='+pc_hash,
            data: $('#myform').serialize(),
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
    <?php } else {?>
    $('#compress').on('switchChange.bootstrapSwitch',function(event,state){
        if(state){ 
            $('#levels').removeClass('hide');
        }else{
            $('#levels').addClass('hide');
        }
    });
    $(document).on("click", ".btn-backup", function () {
        // 延迟加载
        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 5000
        });
        $(this).attr('disabled', true);
        <?php if (is_pc()) {?>$(this).html('正在发送备份请求...');<?php }?>
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '?m=admin&c=database&a=backup&menuid=<?php echo $this->input->get('menuid');?>&pc_hash='+pc_hash,
            data: $("#myform").serialize(),
            success: function(json) {
                layer.close(loading);
                // token 更新
                if (json.token) {
                    var token = json.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                if (json.code == 1) {
                    tables = json.data.tables;
                    <?php if (is_pc()) {?>$('.btn-backup').html(json.msg + '开始备份，请不要关闭本页面！');<?php }?>
                    backup(json.data.tab);
                } else {
                    $('.btn-backup').attr('disabled', false);
                    <?php if (is_pc()) {?>$('.btn-backup').html('<?php echo L('backup_starting');?>');<?php }?>
                    dr_tips(json.code, json.msg);
                }
                return false;
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
            }
        });
    });
    function backup(tab, code) {
        code && showmsg(tab.id, "开始备份...(0%)");
        $.get('?m=admin&c=database&a=backup&menuid=<?php echo $this->input->get('menuid');?>&pc_hash='+pc_hash, tab, function (json) {
            if (json.code) {
                showmsg(tab.id, json.msg);
                if (!json.data.tab) {
                    $('.btn-backup').attr('disabled', false);
                    <?php if (is_pc()) {?>$('.btn-backup').html("备份完成，点击重新备份");<?php }?>
                    if (json.data.url) {
                        setTimeout("window.location.href = '"+json.data.url+"'", 2000);
                    }
                    return;
                }
                backup(json.data.tab, tab.id != json.data.tab.id);
            } else {
                dr_tips(json.code, json.msg);
                $('.btn-backup').attr('disabled', false);
                <?php if (is_pc()) {?>$('.btn-backup').html("立即备份");<?php }?>
            }
        }, "json");
    }
    function showmsg(id, msg) {
        $("#myform").find("input[value=" + tables[id] + "]").closest("tr").find(".info").html(msg);
    }
    <?php }?>
});
function showcreat(tblname) {
    omnipotent('show','?m=admin&c=database&a=public_repair&operation=showcreat&menuid=<?php echo $this->input->get('menuid');?>&tables='+tblname,tblname,1,'60%','70%')
}
function show(tblname) {
    omnipotent('show','?m=admin&c=database&a=public_repair&operation=show&menuid=<?php echo $this->input->get('menuid');?>&tables='+tblname,tblname,1,'60%','70%')
}
</script>
</html>
