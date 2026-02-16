<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('附件设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('附件设置');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('头像存储').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-user"></i> <?php if (is_pc()) {echo L('头像存储');}?> </a>
            </li>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="$('#dr_page').val('2')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('缩略图').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-photo"></i> <?php if (is_pc()) {echo L('缩略图');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('附件归档');?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[attachment_stat]" value="1" <?php echo ($attachment_stat) ? ' checked' : ''?> data-on-text="<?php echo L('开启');?>" data-off-text="<?php echo L('关闭');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('附件将分为已使用的附件和未使用的附件，归档存储');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('是否同步删除附件');?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[attachment_del]" value="1" <?php echo ($attachment_del) ? ' checked' : ''?> data-on-text="<?php echo L('开启');?>" data-off-text="<?php echo L('关闭');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('删除文章将同步删除附件');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('防止重复上传');?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[sys_attachment_cf]" value="1" <?php echo ($sys_attachment_cf) ? ' checked' : ''?> data-on-text="<?php echo L('开启');?>" data-off-text="<?php echo L('关闭');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('当存在重复上传同一文件时，只存储一个文件');?></span>
                        </div>
                    </div>

                    <div class="form-group" id="dr_row_sys_attachment_pagesize">
                        <label class="col-md-2 control-label"><?php echo L('浏览附件分页');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control" type="text" id="sys_attachment_pagesize" name="data[sys_attachment_pagesize]" value="<?php echo $sys_attachment_pagesize;?>" ></label>
                            <span class="help-block"><?php echo L('在浏览附件界面中每页显示的数量控制')?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('上传安全策略');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[sys_attachment_safe]" value="0"<?php echo ($sys_attachment_safe=='0') ? ' checked' : ''?>> <?php echo L('严格模式');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="data[sys_attachment_safe]" value="1"<?php echo ($sys_attachment_safe=='1') ? ' checked' : ''?>> <?php echo L('宽松模式');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('严格模式将对文件进行全面检测是否存在非法特征');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('全局存储策略');?></label>
                        <div class="col-md-9">
                            <label><select class="form-control" name="data[sys_attachment_save_id]">
                                <option value="0"<?php echo ($sys_attachment_save_id=='0') ? ' selected' : ''?>><?php echo L('本地存储（按字段分别设置）');?></option>
                                <?php foreach ($remote as $i=>$t) {?>
                                <option value="<?php echo $i;?>"<?php echo ($i == $sys_attachment_save_id ? ' selected' : '');?>> <?php echo L($t['name']);?> </option>
                                <?php }?>
                            </select></label>
                            <span class="help-block"><?php echo L('远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('开启附件分站状态');?></label>
                        <div class="col-md-9">
                            <input type="checkbox" name="data[attachment_file]" value="1" <?php echo ($attachment_file) ? ' checked' : ''?> data-on-text="<?php echo L('开启');?>" data-off-text="<?php echo L('关闭');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                            <span class="help-block"><?php echo L('默认为关闭，开启附件上传为分站上传');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('存储目录方式');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_attachment_type').hide()" name="data[sys_attachment_save_type]" value="0"<?php echo ($sys_attachment_save_type=='0') ? ' checked' : ''?> /> <?php echo L('默认');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_attachment_type').show()" name="data[sys_attachment_save_type]" value="1"<?php echo ($sys_attachment_save_type=='1') ? ' checked' : ''?> /> <?php echo L('自定义');?> <span></span></label>
                            </div>
                            <span class="help-block"><?php echo L('默认存储目录为：/年/月日/文件名');?></span>
                        </div>
                    </div>

                    <div class="form-group dr_attachment_type"<?php echo ($sys_attachment_save_type=='0') ? ' style="display: none"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('存储目录格式');?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" name="data[sys_attachment_save_dir]" value="<?php echo $sys_attachment_save_dir;?>" >
                            <span class="help-block"><?php echo L('留空表示不要目录存储，可填参数格式：{y}表示年，{m}表示月，{d}表示日，/表示目录，不要填写其他特殊符号');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('附件上传目录');?></label>
                        <div class="col-md-9">
                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" id="dr_attachment_dir" name="data[sys_attachment_path]" value="<?php echo $sys_attachment_path;?>">
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_domain_dir('dr_attachment_dir')" type="button"><i class="fa fa-code"></i> <?php echo L('检测');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('此目录必须有读写权限，绝对路径请以“/”开头');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('附件URL地址');?></label>
                        <div class="col-md-9">
                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" name="data[sys_attachment_url]" value="<?php echo $sys_attachment_url;?>" >
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_domain()" type="button"><i class="fa fa-wrench"></i> <?php echo L('检测');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('当设置了附件上传目录后，必须为该目录指定域名，用于分离附件，留空表示默认本站地址（站外保存时必须指定域名）');?></span>
                        </div>
                    </div>
                    <div class="form-group" style="display: none" id="dr_test_domain">
                        <label class="col-md-2 control-label"><?php echo L('目录检测结果');?></label>
                        <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_domain_result">

                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('头像存储目录');?></label>
                        <div class="col-md-9">

                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" id="dr_avatar_dir" name="data[sys_avatar_path]" value="<?php echo $sys_avatar_path;?>" >
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_domain_dir('dr_avatar_dir')" type="button"><i class="fa fa-code"></i> <?php echo L('测试');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('绝对路径请以“/”开头，默认：上传路径/avatar/');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('头像访问URL地址');?></label>
                        <div class="col-md-9">
                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" id="dr_avatar_url" name="data[sys_avatar_url]" value="<?php echo $sys_avatar_url;?>" >
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_avatar_domain()" type="button"><i class="fa fa-wrench"></i> <?php echo L('检测');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('头像文件访问地址，可单独指定域名，默认：/上传路径/avatar/');?></span>
                        </div>
                    </div>

                    <div class="form-group" style="display: none" id="dr_test_avatar_domain">
                        <label class="col-md-2 control-label"><?php echo L('目录检测结果');?></label>
                        <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_avatar_domain_result">

                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('缩略图存储目录');?></label>
                        <div class="col-md-9">

                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" id="dr_thumb_dir" name="data[sys_thumb_path]" value="<?php echo $sys_thumb_path;?>" >
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_domain_dir('dr_thumb_dir')" type="button"><i class="fa fa-code"></i> <?php echo L('检测');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('绝对路径请以“/”开头，默认：上传路径/thumb/');?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('缩略图访问URL地址');?></label>
                        <div class="col-md-9">
                            <div class="input-group input-xlarge">
                                <input class="form-control " type="text" id="dr_thumb_url" name="data[sys_thumb_url]" value="<?php echo $sys_thumb_url;?>" >
                                <span class="input-group-btn">
                                        <button class="btn blue" onclick="dr_test_thumb_domain()" type="button"><i class="fa fa-wrench"></i> <?php echo L('检测');?></button>
                                    </span>
                            </div>
                            <span class="help-block"><?php echo L('缩略图文件访问地址，可单独指定域名，默：/上传路径/thumb/');?></span>
                        </div>
                    </div>

                    <div class="form-group" style="display: none" id="dr_test_thumb_domain">
                        <label class="col-md-2 control-label"><?php echo L('目录检测结果');?></label>
                        <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_thumb_domain_result">

                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('保存');?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script>
function dr_test_domain() {
    // 延迟加载
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 5000
    });
    $('#dr_test_domain').hide();
    $.ajax({type: "POST",dataType:"json", url: "?m=attachment&c=attachment&a=public_test_attach_domain", data: $('#myform').serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            $('#dr_test_domain').show();
            $('#dr_test_domain_result').html(json.msg);
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
        }
    });
}
function dr_test_thumb_domain() {
    // 延迟加载
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 5000
    });
    $('#dr_test_domain').hide();
    $.ajax({type: "POST",dataType:"json", url: "?m=attachment&c=attachment&a=public_test_thumb_domain", data: $('#myform').serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            $('#dr_test_thumb_domain').show();
            $('#dr_test_thumb_domain_result').html(json.msg);
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
        }
    });
}
function dr_test_avatar_domain() {
    // 延迟加载
    var loading = layer.load(2, {
        shade: [0.3,'#fff'], //0.1透明度的白色背景
        time: 5000
    });
    $('#dr_test_domain').hide();
    $.ajax({type: "POST",dataType:"json", url: "?m=attachment&c=attachment&a=public_test_avatar_domain", data: $('#myform').serialize(),
        success: function(json) {
            layer.close(loading);
            // token 更新
            if (json.token) {
                var token = json.token;
                $("#myform input[name='"+token.name+"']").val(token.value);
            }
            $('#dr_test_avatar_domain').show();
            $('#dr_test_avatar_domain_result').html(json.msg);
            return false;
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError);
        }
    });
}
function dr_test_domain_dir(id) {
    $.ajax({type: "GET",dataType:"json", url: "?m=attachment&c=attachment&a=public_test_attach_dir&v="+encodeURIComponent($("#"+id).val()),
        success: function(json) {
            dr_tips(json.code, json.msg, -1);
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
</script>
</div>
</div>
</body>
</html>