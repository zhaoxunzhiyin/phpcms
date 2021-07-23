<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap/css/bootstrap.min.css" media="all" />
<style type="text/css">
.page-content {margin-left: 0px;margin-top: 0;padding: 25px 20px 10px;}
.main-content {background: #f5f6f8;}
.note.note-danger {background-color: #fef7f8;border-color: #f0868e;color: #210406;}
.note.note-danger {border-radius: 4px;border-left: 4px solid #f0868e;background-color: #ffffff;color: #888;}
.my-content-top-tool {margin-top: -25px;margin-bottom: 10px;}
.note {margin: 0 0 20px;padding: 15px 30px 15px 15px;border-left: 5px solid #eee;border-radius: 0 4px 4px 0;}
.note, .tabs-right.nav-tabs>li>a:focus, .tabs-right.nav-tabs>li>a:hover {-webkit-border-radius: 0 4px 4px 0;-moz-border-radius: 0 4px 4px 0;-ms-border-radius: 0 4px 4px 0;-o-border-radius: 0 4px 4px 0;}
.note p:last-child {margin-bottom: 0;}
.note p {margin: 0;}
.note p, .page-loading, .panel .panel-body {font-size: 13px;}
.note.note-danger a {color: #666;}
.portlet.light>.portlet-title {padding: 0;color: #181C32;font-weight: 500;}
.portlet.bordered>.portlet-title {border-bottom: 0;}
.portlet>.portlet-title {padding: 0;margin-bottom: 2px;-webkit-border-radius: 4px 4px 0 0;-moz-border-radius: 4px 4px 0 0;-ms-border-radius: 4px 4px 0 0;-o-border-radius: 4px 4px 0 0;border-radius: 4px 4px 0 0;}
.portlet>.portlet-title>.caption {float: left;display: inline-block;font-size: 18px;line-height: 18px;padding: 10px 0;}
.portlet.light>.portlet-title>.caption.caption-md>.caption-subject, .portlet.light>.portlet-title>.caption>.caption-subject {font-size: 15px;}
.font-dark {color: #2f353b!important;}
.btn:not(.btn-sm):not(.btn-lg) {line-height: 1.44;}
.btn {outline: 0!important;}
.btn, .form-control {box-shadow: none!important;}
.btn {display: inline-block;margin-bottom: 0;font-weight: 400;text-align: center;touch-action: manipulation;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
.btn, .btn-danger.active, .btn-danger:active, .btn-default.active, .btn-default:active, .btn-info.active, .btn-info:active, .btn-primary.active, .btn-primary:active, .btn-success.active, .btn-success:active, .btn-warning.active, .btn-warning:active, .btn.active, .btn:active, .dropdown-menu>.disabled>a:focus, .dropdown-menu>.disabled>a:hover, .form-control, .navbar-toggle, .open>.btn-danger.dropdown-toggle, .open>.btn-default.dropdown-toggle, .open>.btn-info.dropdown-toggle, .open>.btn-primary.dropdown-toggle, .open>.btn-success.dropdown-toggle, .open>.btn-warning.dropdown-toggle {background-image: none;}
.btn, .btn-group, .btn-group-vertical, .caret, .checkbox-inline, .radio-inline, img {vertical-align: middle;}
.btn.blue:not(.btn-outline) {color: #FFF;background-color: #3598dc;border-color: #3598dc;}
.btn.blue:not(.btn-outline).active, .btn.blue:not(.btn-outline):active, .btn.blue:not(.btn-outline):hover, .open>.btn.blue:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #217ebd;border-color: #1f78b5;}
.btn.green:not(.btn-outline) {color: #FFF;background-color: #32c5d2;border-color: #32c5d2;}
.btn.green:not(.btn-outline).active, .btn.green:not(.btn-outline):active, .btn.green:not(.btn-outline):hover, .open>.btn.green:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #26a1ab;border-color: #2499a3;}
.btn.yellow:not(.btn-outline) {color: #fff;background-color: #c49f47;border-color: #c49f47;}
.btn.yellow:not(.btn-outline).active, .btn.yellow:not(.btn-outline):active, .btn.yellow:not(.btn-outline):hover, .open>.btn.yellow:not(.btn-outline).dropdown-toggle {color: #fff;background-color: #a48334;border-color: #9c7c32;}
.btn.red:not(.btn-outline).active, .btn.red:not(.btn-outline):active, .btn.red:not(.btn-outline):hover, .open>.btn.red:not(.btn-outline).dropdown-toggle {color: #fff;background-color: #e12330;border-color: #dc1e2b;}
.btn.red:not(.btn-outline) {color: #fff;background-color: #e7505a;border-color: #e7505a;}
.btn.dark:not(.btn-outline) {color: #FFF;background-color: #2f353b;border-color: #2f353b;}
.btn.dark:not(.btn-outline).active, .btn.dark:not(.btn-outline):active, .btn.dark:not(.btn-outline):hover, .open>.btn.dark:not(.btn-outline).dropdown-toggle {color: #FFF;background-color: #181c1f;border-color: #141619;}
</style>
<div class="page-content main-content">
<div class="note note-danger my-content-top-tool">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=public_cache_all',1);"><?php echo L('操作之前请更新下全站缓存');?></a></p>
</div>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-sticky-note font-dark"></i>
            <span class="caption-subject font-dark"> <?php echo L('according_model');?></span>
        </div>
    </div>
    <div class="portlet-body ">
        <?php
        $models = getcache('model','commons');
        $model_datas = array();
        foreach($models as $_k=>$_v) {
            if($_v['siteid']!=$this->siteid) continue;
            $model_datas[$_v['modelid']] = $_v['name'];
        }
        echo form::select($model_datas,$modelid,'name="modelid" onchange="change_model(this.value)"');
        ?>
        <button type="button" onclick="dr_submit_todo('thumbform', '?m=content&c=create_html&a=public_show_url&modelid=<?php echo $modelid;?>')" class="btn blue"> <i class="fa fa-refresh"></i> <?php echo L('批量更新内容URL地址')?> </button>
        <button type="button" onclick="dr_iframe_show_html('desc')" class="btn drak"> <i class="fa fa-th-large"></i> <?php echo L('批量提取描述字段')?> </button>
        <button type="button" onclick="dr_iframe_show_html('thumb')" class="btn green"> <i class="fa fa-photo"></i> <?php echo L('批量提取缩略图')?> </button>
        <button type="button" onclick="dr_iframe_show_html('tag')" class="btn yellow"> <i class="fa fa-tag"></i> <?php echo L('批量提取关键词')?> </button>
        <button type="button" onclick="dr_iframe_show_html('del')" class="btn red"> <i class="fa fa-trash"></i> <?php echo L('批量彻底删除内容')?> </button>
        <button type="button" onclick="dr_iframe_show_html('cat')" class="btn green"> <i class="fa fa-reorder"></i> <?php echo L('批量变更栏目')?> </button>
    </div>
</div>
<div class="hide">
    <div id="tagform_html">
        <form id="tagform">
            <div class="form-body">

                <div class="form-group">
                    <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
                    <option value='0' selected><?php echo L('no_limit_category');?></option>
                    <?php echo $string;?>
                    </select>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-6" style="text-align:left">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('提取范围')?> </label>
                            </div>
                            <div class="form-group">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio">
                                        <input type="radio" name="keyword"  value="1" checked=""> <?php echo L('只提取空词的内容')?>
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="keyword" value="0"> <?php echo L('重新提取全部内容')?>
                                        <span></span>
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6" style="text-align:right;padding-top:25px">
                            <button type="button" onclick="dr_submit_todo('tagform_post', '?m=content&c=create_html&a=public_tag_index&modelid=<?php echo $modelid;?>')" class="btn blue"> <i class="fa fa-tag"></i> <?php echo L('立即执行')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="thumbform_html">
        <form id="thumbform">
            <input type="hidden" name="dosubmit" value="1">
            <div class="form-body">
                <div class="form-group">
                    <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
                    <option value='0' selected><?php echo L('no_limit_category');?></option>
                    <?php echo $string;?>
                    </select>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-6" style="text-align:left">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('替换范围')?> </label>
                            </div>
                            <div class="form-group">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio">
                                        <input type="radio" name="thumb"  value="1" checked=""> <?php echo L('只替换空图')?>
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="thumb" value="0"> <?php echo L('替换全部')?>
                                        <span></span>
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6" style="text-align:right;padding-top:25px">
                            <button type="button" onclick="dr_submit_todo('thumbform_post', '?m=content&c=create_html&a=public_thumb_index&modelid=<?php echo $modelid;?>')" class="btn blue"> <i class="fa fa-photo"></i> <?php echo L('立即执行')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="descform_html">
        <form id="descform">
            <div class="form-body">

                <div class="form-group">
                    <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
                    <option value='0' selected><?php echo L('no_limit_category');?></option>
                    <?php echo $string;?>
                    </select>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-3" style="text-align:left">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('提取字数')?> </label>
                            </div>
                            <div class="form-group">
                                <label><input type="text" name="nums" value="100" class="form-control"></label>
                            </div>
                        </div>
                        <div class="col-md-6" style="text-align:left">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('提取范围')?> </label>
                            </div>
                            <div class="form-group">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio">
                                        <input type="radio" name="keyword"  value="1" checked=""> <?php echo L('只提取空描述的内容')?>
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="keyword" value="0"> <?php echo L('重新提取全部内容')?>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align:center;padding-top:20px">
                            <button type="button" onclick="dr_submit_todo('descform_post', '?m=content&c=create_html&a=public_desc_index&modelid=<?php echo $modelid;?>')" class="btn blue"> <i class="fa fa-tag"></i> <?php echo L('立即执行')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="delform_html">
        <form id="delform">
            <div class="form-body">
                <div class="form-group">
                    <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
                    <option value='0' selected><?php echo L('no_limit_category');?></option>
                    <?php echo $string;?>
                    </select>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('按管理员账号或按管理员uid')?> </label>
                            </div>
                            <div class="form-group">
                                <label><input type="text" name="author" class="form-control"></label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('按id范围')?> </label>
                            </div>
                            <div class="form-group">
                                <label><input type="text" name="id1" class="form-control"></label>
                                <label><?php echo L('到')?></label>
                                <label><input type="text" name="id2" class="form-control"></label>
                            </div>
                        </div>
                        <div class="col-md-12" style="text-align:center;padding-top:20px">
                            <button type="button" onclick="dr_submit_todo('delform_post', '?m=content&c=create_html&a=public_del_index&modelid=<?php echo $modelid;?>')" class="btn red"> <i class="fa fa-trash"></i> <?php echo L('立即执行')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="catform_html">
        <form id="catform">
            <div class="form-body">
                <div class="form-group">
                    <select class="bs-select form-control" name='catids[]' id='catids' multiple="multiple" style="height:200px;" title="<?php echo L('push_ctrl_to_select');?>">
                    <option value='0' selected><?php echo L('no_limit_category');?></option>
                    <?php echo $string;?>
                    </select>
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-12" style="text-align:left">
                            <div class="form-group" style="margin-bottom:5px">
                                <label> <?php echo L('变更为')?> </label>
                            </div>
                            <div class="form-group">
                                <select class="bs-select form-control" name='toid' id='toid'>
                                <option value='0' selected><?php echo L('选择栏目');?></option>
                                <?php echo $select_post;?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12" style="text-align:center;padding-top:20px">
                            <button type="button" onclick="dr_submit_todo('catform_post', '?m=content&c=create_html&a=public_cat_index&modelid=<?php echo $modelid;?>')" class="btn blue"> <i class="fa fa-tag"></i> <?php echo L('立即执行')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-database font-dark"></i>
            <span class="caption-subject font-dark  "> <?php echo L('内容替换')?></span>
        </div>
    </div>
    <div class="portlet-body form">
        <form action="" class="form-horizontal" method="post" id="replaceform">
            <div class="form-body">

                <div class="form-group row">
                    <label class="col-md-2 control-label"> <?php echo L('表名称')?> </label>
                    <div class="col-md-9">
                        <label><select name="bm" class="form-control" onchange="dr_fd(this.value)">
                            <option value="0"><?php echo L('选择表')?></option>
                            <?php foreach($tables as $t) {?>
                            <option value="<?php echo $t['Name'];?>"><?php echo $t['Name'];?><?php if ($t['Comment']) {?>（<?php echo $t['Comment'];?>）<?php }?></option>
                            <?php }?>
                        </select></label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 control-label"><?php echo L('待替换字段')?></label>
                    <div class="col-md-9">
                        <label id="dr_fd"></label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 control-label"><?php echo L('被替换内容')?></label>
                    <div class="col-md-9">
                        <textarea class="form-control" style="height:100px" name="t1"></textarea>

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 control-label"><?php echo L('替换后的内容')?></label>
                    <div class="col-md-9">
                        <textarea class="form-control" style="height:100px" name="t2"></textarea>

                    </div>
                </div>


                <div class="form-actions row">
                    <label class="col-md-2 control-label">&nbsp;</label>
                    <div class="col-md-9" style="padding-left: 5px;">
                        <button type="button" onclick="dr_submit_post_todo('replaceform', '?m=content&c=create_html&a=public_replace_index')" class="btn blue"> <i class="fa fa-database"></i> <?php echo L('立即执行')?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
<script>
function dr_iframe_show_html(id) {
    var html = $("#"+id+"form_html").html();
    html = html.replace('<form id="'+id+'form">', '<form id="'+id+'form_post">');
    var w = '50%';
    var h = '60%';
    if (is_mobile()) {
        w = '95%';
        h = '90%';
    }
    layer.open({
        type: 1,
        title: '<?php echo L('批量操作')?>',
        shadeClose: true,
        shade: 0,
        area: [w, h],
        content: "<div  style=\"padding: 20px;\">"+html+"</div>"
    });
}
function dr_fd(v) {
    $.ajax({type: "get",dataType:"json", url: "?m=content&c=create_html&a=public_field_index&table="+v,
        success: function(json) {
            if (json.code == 1) {
                $('#dr_fd').html(json.msg);
            } else {
                dr_cmf_tips(0, json.msg);
            }
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
</script>
<script language="JavaScript">
<!--
window.top.$('#display_center_id').css('display','none');
function change_model(modelid) {
    window.location.href='?m=content&c=create_html&a=update_urls&modelid='+modelid+'&pc_hash='+pc_hash;
}
//-->
</script>
</body>
</html>