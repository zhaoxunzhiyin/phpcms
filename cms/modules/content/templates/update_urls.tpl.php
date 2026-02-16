<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:iframe_show('<?php echo L('一键更新栏目');?>','?m=admin&c=category&a=public_repair&pc_hash='+pc_hash,'500px','300px');"><?php echo L('变更栏目属性之后，需要一键更新栏目配置信息');?></a></p>
</div>
<div class="portlet light bordered">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('according_model').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-th-large"></i> <?php if (is_pc()) {echo L('according_model');}?> </a>
            </li>
            <?php if(cleck_admin(param::get_session('roleid')) && ADMIN_FOUNDERS && dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {?>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('按字段批量替换').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-database"></i> <?php if (is_pc()) {echo L('按字段批量替换');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('按字段批量设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-edit"></i> <?php if (is_pc()) {echo L('按字段批量设置');}?> </a>
            </li>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('全模型替换').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-table"></i> <?php if (is_pc()) {echo L('全模型替换');}?> </a>
            </li>
            <?php }?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">
                <div class="table-list">
                    <table style="margin-top: 30px;" class="table table-striped table-bordered table-hover table-checkable dataTable">
                        <thead>
                        <tr class="heading">
                            <th width="50"> </th>
                            <th width="180"> <?php echo L('model_name');?> </th>
                            <th><?php echo L('operations_manage');?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $models = getcache('model','commons');
                        $i=1;
                        foreach($models as $_k=>$_v) {
                            if($_v['siteid']!=$this->siteid) continue;
                        ?>
                        <tr class="heading">
                            <td><?php echo $i;?></td>
                            <td><?php echo $_v['name'];?></td>
                            <td>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>', '?m=content&c=create_html&a=public_url_index&modelid=<?php echo $_v['modelid'];?>')" class="btn blue btn-xs"> <i class="fa fa-refresh"></i> <?php echo L('批量更新内容URL地址')?> </button></label>
                                <?php if (ADMIN_FOUNDERS && dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {?>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>','?m=content&c=create_html&a=public_desc_index&modelid=<?php echo $_v['modelid'];?>')" class="btn drak btn-xs"> <i class="fa fa-th-large"></i> <?php echo L('批量提取描述字段')?> </button></label>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>','?m=content&c=create_html&a=public_thumb_index&modelid=<?php echo $_v['modelid'];?>')" class="btn green btn-xs"> <i class="fa fa-photo"></i> <?php echo L('批量提取缩略图')?> </button></label>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>', '?m=content&c=create_html&a=public_xthumb_index&modelid=<?php echo $_v['modelid'];?>')" class="btn blue btn-xs"> <i class="fa fa-cloud-download"></i> <?php echo L('缩略图本地化')?> </button></label>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>','?m=content&c=create_html&a=public_tag_index&modelid=<?php echo $_v['modelid'];?>')" class="btn yellow btn-xs"> <i class="fa fa-tag"></i> <?php echo L('批量提取关键词')?> </button></label>
                                <?php }?>
                                <?php if(cleck_admin(param::get_session('roleid')) && ADMIN_FOUNDERS && dr_in_array(param::get_session('userid'), ADMIN_FOUNDERS)) {?>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>','?m=content&c=create_html&a=public_del_index&modelid=<?php echo $_v['modelid'];?>')" class="btn red btn-xs"> <i class="fa fa-trash"></i> <?php echo L('批量彻底删除内容')?> </button></label>
                                <label><button type="button" onclick="iframe_show('<?php echo L('批量操作')?>','?m=content&c=create_html&a=public_cat_index&modelid=<?php echo $_v['modelid'];?>')" class="btn green btn-xs"> <i class="fa fa-reorder"></i> <?php echo L('批量变更栏目')?> </button></label>
                                <?php }?>
                            </td>
                        </tr>
                        <?php $i++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
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
                                <label id="dr_fd"><select class="form-control">
                                    <option value="0"><?php echo L('没有选择表')?></option>
                                </select></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('被替换内容')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" name="t1"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('设置被替换的字符内容')?> </p>

                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('替换后的内容')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" name="t2"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('将上面设置的被替换的字符替换成新的字符')?> </p>
                            </div>
                        </div>
                        <div class="form-group row dr_sql_row" style="display: none">
                            <label class="col-md-2 control-label"><?php echo L('本次SQL语句')?></label>
                            <div class="col-md-9">
                                <textarea readonly class="form-control dr_sql" style="height:100px"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('这是本次替换的sql语句，用于开发者分析问题')?> </p>
                            </div>
                        </div>

                        <div class="portlet-body form myfooter">
                            <div class="form-actions text-center">
                                <button type="button" onclick="dr_submit_sql_todo2('replaceform', '?m=content&c=create_html&a=public_replace_index')" class="btn blue"> <i class="fa fa-database"></i> <?php echo L('立即执行')?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <form action="" class="form-horizontal" method="post" id="editform">
                    <div class="form-body">

                        <div class="form-group row">
                            <label class="col-md-2 control-label"> <?php echo L('表名称')?> </label>
                            <div class="col-md-9">
                                <label><select name="bm" class="form-control" onchange="dr_sz(this.value)">
                                    <option value="0"><?php echo L('选择表')?></option>
                                    <?php foreach($tables as $t) {?>
                                    <option value="<?php echo $t['Name'];?>"><?php echo $t['Name'];?><?php if ($t['Comment']) {?>（<?php echo $t['Comment'];?>）<?php }?></option>
                                    <?php }?>
                                </select></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('待设置字段')?></label>
                            <div class="col-md-9">
                                <label id="dr_sz"><select class="form-control">
                                    <option value="0"><?php echo L('没有选择表')?></option>
                                </select></label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('修改方式')?></label>
                            <div class="col-md-9">
                                <div class="mt-radio-inline">
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="ms" value="0" checked /> <?php echo L('完全替换指定值')?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="ms" value="1"  /> <?php echo L('将新值插入在原值之前')?> <span></span></label>
                                    <label class="mt-radio mt-radio-outline"><input type="radio" name="ms" value="2"  /> <?php echo L('将新值插入在原值之后')?> <span></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('执行条件')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" name="t1"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('设置批量替换的条件SQL语句，留空表示全部替换')?> </p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('设置新的值')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" name="t2"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('设置修改后的字符内容')?> </p>
                            </div>
                        </div>
                        <div class="form-group row dr_sql_row" style="display: none">
                            <label class="col-md-2 control-label"><?php echo L('本次SQL语句')?></label>
                            <div class="col-md-9">
                                <textarea readonly class="form-control dr_sql" style="height:100px"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('这是本次替换的sql语句，用于开发者分析问题')?> </p>
                            </div>
                        </div>

                        <div class="portlet-body form myfooter">
                            <div class="form-actions text-center">
                                <button type="button" onclick="dr_submit_sql_todo2('editform', '?m=content&c=create_html&a=public_all_edit')" class="btn blue"> <i class="fa fa-database"></i> <?php echo L('立即执行')?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <form action="" class="form-horizontal" method="post" id="allform">
                    <div class="form-body">

                        <div class="form-group row">
                            <label class="col-md-2 control-label"> </label>
                            <div class="col-md-9">
                                <div class="well well2">
                                    <?php echo L('当网站域名变更时可以在这里进行全模块替换')?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('被替换内容')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" id="alldb_t1"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('设置被替换的字符内容')?> </p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 control-label"><?php echo L('替换后的内容')?></label>
                            <div class="col-md-9">
                                <textarea class="form-control" style="height:100px" id="alldb_t2"></textarea>
                                <p style="padding-top:9px;" class="help-block"> <?php echo L('将上面设置的被替换的字符替换成新的字符')?> </p>

                            </div>
                        </div>

                        <script>
                        function dr_alldb_edit() {
                            var url = '?m=content&c=create_html&a=public_dball_edit&key='+Date.parse(new Date())+'&t1='+encodeURIComponent($('#alldb_t1').val())+'&t2='+encodeURIComponent($('#alldb_t2').val());
                            iframe_show('<?php echo L('批量操作')?>', url);
                        }
                        </script>

                        <div class="portlet-body form myfooter">
                            <div class="form-actions text-center">
                                <button type="button" onclick="dr_alldb_edit();" class="btn blue"> <i class="fa fa-database"></i> <?php echo L('立即执行')?></button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
</div>
<script>
function dr_fd(v) {
    $.ajax({type: "get",dataType:"json", url: "?m=content&c=create_html&a=public_field_index&table="+v,
        success: function(json) {
            if (json.code == 1) {
                $('#dr_fd').html(json.msg);
            } else {
                dr_tips(0, json.msg);
            }
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
function dr_sz(v) {
    $.ajax({type: "get",dataType:"json", url: "?m=content&c=create_html&a=public_field_index&table="+v,
        success: function(json) {
            if (json.code == 1) {
                $('#dr_sz').html(json.msg);
            } else {
                dr_tips(0, json.msg);
            }
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
</script>
</div>
</div>
</body>
</html>