<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<style type="text/css">
html,body{background:#f5f6f8!important;}
body{padding: 20px 20px 0px 20px;}
.input-text, .measure-input, textarea, input.date, input.endDate, .input-focus {height: 32px;}
.keywords {height: 100%!important;}
</style>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>bootstrap/css/bootstrap.min.css" media="all" />
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content  ">
<div class="page-body" style="padding-top:17px;margin-bottom:90px;">
<form action="?m=attachment&c=attachment&a=save" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="portlet light myfbody">
        <div class="col-tab">
            <ul class="tabBut cu-li">
                <li id="tab_setting_1" class="on" onclick="SwapTab('setting','on','',3,1);"><?php echo L('附件设置')?></li>
                <li id="tab_setting_2" onclick="SwapTab('setting','on','',3,2);"><?php echo L('头像存储')?></li>
                <li id="tab_setting_3" onclick="SwapTab('setting','on','',3,3);"><?php echo L('缩略图')?></li>
            </ul>
            <div class="portlet-body">
                <div class="tab-content">

                    <div class="tab-pane active" id="div_setting_1">
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-2 control-label">附件存储策略</label>
                                <div class="col-md-9">
                                    <label><select class="form-control" name="data[sys_attachment_save_id]">
                                        <option value="0"<?php echo ($sys_attachment_save_id=='0') ? ' selected' : ''?>>本地存储</option>
                                        <?php foreach ($remote as $i=>$t) {?>
                                        <option value="<?php echo $i;?>"<?php echo ($i == $sys_attachment_save_id ? ' selected' : '');?>> <?php echo L($t['name']);?> </option>
                                        <?php }?>
                                    </select></label>
                                    <span class="help-block">远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">上传安全策略</label>
                                <div class="col-md-9">
                                    <div class="mt-radio-inline">
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[sys_attachment_safe]" value="0"<?php echo ($sys_attachment_safe=='0') ? ' checked' : ''?>> <?php echo L('严格模式');?> <span></span></label>
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[sys_attachment_safe]" value="1"<?php echo ($sys_attachment_safe=='1') ? ' checked' : ''?>> <?php echo L('宽松模式');?> <span></span></label>
                                    </div>
                                    <span class="help-block">严格模式将对文件进行全面检测是否存在非法特征</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">附件归档</label>
                                <div class="col-md-9">
                                    <div class="mt-radio-inline">
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[attachment_stat]" value="1"<?php echo ($attachment_stat=='1') ? ' checked' : ''?>> <?php echo L('是');?> <span></span></label>
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[attachment_stat]" value="0"<?php echo ($attachment_stat=='0') ? ' checked' : ''?>> <?php echo L('否');?> <span></span></label>
                                    </div>
                                    <span class="help-block">附件将分为已使用的附件和未使用的附件，归档存储</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">开启附件分站状态</label>
                                <div class="col-md-9">
                                    <div class="mt-radio-inline">
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[attachment_file]" value="1"<?php echo ($attachment_file=='1') ? ' checked' : ''?>> <?php echo L('是');?> <span></span></label>
                                        <label class="mt-radio mt-radio-outline"><input type="radio" name="data[attachment_file]" value="0"<?php echo ($attachment_file=='0') ? ' checked' : ''?>> <?php echo L('否');?> <span></span></label>
                                    </div>
                                    <span class="help-block">默认为否,开启附件上传为分站上传</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">存储目录方式</label>
                                <div class="col-md-9">
                                    <div class="mt-radio-inline">
                                        <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_attachment_type').hide()" name="data[sys_attachment_save_type]" value="0"<?php echo ($sys_attachment_save_type=='0') ? ' checked' : ''?> /> 默认 <span></span></label>
                                        <label class="mt-radio mt-radio-outline"><input type="radio" onclick="$('.dr_attachment_type').show()" name="data[sys_attachment_save_type]" value="1"<?php echo ($sys_attachment_save_type=='1') ? ' checked' : ''?> /> 自定义 <span></span></label>
                                    </div>
                                    <span class="help-block">默认存储目录为：/年/月日/文件名</span>
                                </div>
                            </div>

                            <div class="form-group dr_attachment_type"<?php echo ($sys_attachment_save_type=='0') ? ' style="display: none"' : ''?>>
                                <label class="col-md-2 control-label">存储目录格式</label>
                                <div class="col-md-9">
                                    <input class="form-control" type="text" name="data[sys_attachment_save_dir]" value="<?php echo $sys_attachment_save_dir;?>" >
                                    <span class="help-block">留空表示不要目录存储，可填参数格式：{y}表示年，{m}表示月，{d}表示日，/表示目录，不要填写其他特殊符号</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">附件上传目录</label>
                                <div class="col-md-9">
                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" id="dr_attachment_dir" name="data[sys_attachment_path]" value="<?php echo $sys_attachment_path;?>">
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_domain_dir('dr_attachment_dir')" type="button"><i class="fa fa-code"></i> 测试</button>
                                            </span>
                                    </div>
                                    <span class="help-block">此目录必须有读写权限，绝对路径请以“/”开头</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">附件URL地址</label>
                                <div class="col-md-9">
                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" name="data[sys_attachment_url]" value="<?php echo $sys_attachment_url;?>" >
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_domain()" type="button"><i class="fa fa-wrench"></i> 检测</button>
                                            </span>
                                    </div>
                                    <span class="help-block">当设置了附件上传目录后，必须为该目录指定域名，用于分离附件，留空表示默认本站地址（站外保存时必须指定域名）</span>
                                </div>
                            </div>
                            <div class="form-group" style="display: none" id="dr_test_domain">
                                <label class="col-md-2 control-label">目录检测结果</label>
                                <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_domain_result">

                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="tab-pane " id="div_setting_2">
                        <div class="form-body">


                            <div class="form-group">
                                <label class="col-md-2 control-label">头像存储目录</label>
                                <div class="col-md-9">

                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" id="dr_avatar_dir" name="data[sys_avatar_path]" value="<?php echo $sys_avatar_path;?>" >
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_domain_dir('dr_avatar_dir')" type="button"><i class="fa fa-code"></i> 测试</button>
                                            </span>
                                    </div>
                                    <span class="help-block">绝对路径请以“/”开头，默认：上传路径/avatar/</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">头像访问URL地址</label>
                                <div class="col-md-9">
                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" id="dr_avatar_url" name="data[sys_avatar_url]" value="<?php echo $sys_avatar_url;?>" >
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_avatar_domain()" type="button"><i class="fa fa-wrench"></i> 检测</button>
                                            </span>
                                    </div>
                                    <span class="help-block">头像文件访问地址，可单独指定域名，默认：/上传路径/avatar/</span>
                                </div>
                            </div>

                            <div class="form-group" style="display: none" id="dr_test_avatar_domain">
                                <label class="col-md-2 control-label">目录检测结果</label>
                                <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_avatar_domain_result">

                                </div>
                            </div>

                        </div>
                    </div>


                    <div class="tab-pane " id="div_setting_3">
                        <div class="form-body">


                            <div class="form-group">
                                <label class="col-md-2 control-label">缩略图存储目录</label>
                                <div class="col-md-9">

                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" id="dr_thumb_dir" name="data[sys_thumb_path]" value="<?php echo $sys_thumb_path;?>" >
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_domain_dir('dr_thumb_dir')" type="button"><i class="fa fa-code"></i> 测试</button>
                                            </span>
                                    </div>
                                    <span class="help-block">绝对路径请以“/”开头，默认：上传路径/thumb/</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">缩略图访问URL地址</label>
                                <div class="col-md-9">
                                    <div class="input-group input-xlarge">
                                        <input class="form-control " type="text" id="dr_thumb_url" name="data[sys_thumb_url]" value="<?php echo $sys_thumb_url;?>" >
                                        <span class="input-group-btn">
                                                <button class="btn blue" onclick="dr_test_thumb_domain()" type="button"><i class="fa fa-wrench"></i> 检测</button>
                                            </span>
                                    </div>
                                    <span class="help-block">缩略图文件访问地址，可单独指定域名，默：/上传路径/thumb/</span>
                                </div>
                            </div>

                            <div class="form-group" style="display: none" id="dr_test_thumb_domain">
                                <label class="col-md-2 control-label">目录检测结果</label>
                                <div class="col-md-9" style="padding-top: 3px; line-height: 25px; color:green" id="dr_test_thumb_domain_result">

                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <button name="dosubmit" type="submit" class="btn green"> <i class="fa fa-save"></i> 保存</button>
        </div>
    </div>
</form>

<script>
function SwapTab(name,cls_show,cls_hide,cnt,cur){
    for(i=1;i<=cnt;i++){
        if(i==cur){
             $('#div_'+name+'_'+i).show();
             $('#tab_'+name+'_'+i).attr('class',cls_show);
        }else{
             $('#div_'+name+'_'+i).hide();
             $('#tab_'+name+'_'+i).attr('class',cls_hide);
        }
    }
}
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
</div>
</div>
</body>
</html>