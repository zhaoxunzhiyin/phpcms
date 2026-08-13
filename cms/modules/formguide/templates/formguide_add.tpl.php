<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<link rel="stylesheet" href="<?php echo JS_PATH;?>bootstrap-switch/css/bootstrap-switch.min.css" media="all" />
<script type="text/javascript" src="<?php echo JS_PATH;?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<link rel="stylesheet" href="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.css">
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery-ui/jquery-ui.min.js"></script>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo JS_PATH;?>bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script src="<?php echo JS_PATH;?>bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            format: "yyyy-mm-dd",
            orientation: "left",
            autoclose: true
        });
    }
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="?m=formguide&c=formguide&a=add" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="portlet light bordered">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('基本设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('基本设置');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('name');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="name" name="info[name]" value="" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','tablename',12);"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('tablename');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="tablename" name="info[tablename]" value=""></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('introduction');?></label>
                        <div class="col-md-9">
                            <textarea class="form-control " style="height:90px" name="info[description]"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('time_limit');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enabletime]" value="1"> <?php echo L('enable');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enabletime]" value="0" checked> <?php echo L('unenable');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="time" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('时间范围');?></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <div class="input-group date-picker input-daterange " data-date="" data-date-format="yyyy-mm-dd">
                                    <input type="text" placeholder="<?php echo L('start_time');?>" class="form-control" value="" name="setting[starttime]">
                                    <span class="input-group-addon"> <?php echo L('到');?> </span>
                                    <input type="text" placeholder="<?php echo L('end_time');?>" class="form-control" value="" name="setting[endtime]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_mail');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="1"> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="0" checked> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="mailaddress" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('e-mail_address');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="mails" name="setting[mails]" value=""></label>
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="mailcontent" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('mailmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="mailmessage" name="setting[mailmessage]" style="height:100px"></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_sms');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="1"> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="0" checked> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="smsaddress" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('sms_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mobiles" name="setting[mobiles]" value="" >
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="smscontent" style="display:none;">
                        <label class="col-md-2 control-label"><?php echo L('smsmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="smsmessage" name="setting[smsmessage]" style="height:100px"></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allows_more_ip');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowmultisubmit]" value="1"<?php echo ($this->setting['allowmultisubmit']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowmultisubmit]" value="0"<?php echo (!$this->setting['allowmultisubmit']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowunreg');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowunreg]" value="1"<?php echo ($this->setting['allowunreg']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowunreg]" value="0"<?php echo (!$this->setting['allowunreg']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('code');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[code]" value="1"<?php echo ($this->setting['code']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[code]" value="0"<?php echo (!$this->setting['code']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('提交成功提示文字');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="rt_text" name="setting[rt_text]" value=""></label>
                            <span class="help-block"><?php echo L('当用户提交表单成功之后显示的文字，默认为：感谢您的参与！')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('提交成功跳转URL');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="rt_url" name="setting[rt_url]" value=""></label>
                            <span class="help-block"><?php echo L('当用户提交表单成功之后跳转的链接，{APP_PATH}表示当前站点URL，{formid}表示当前表单的id号，{siteid}表示当前站点的id号')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('optional_style');?></label>
                        <div class="col-md-9">
                            <label><?php echo form::select($template_list, $info['default_style'], 'name="info[default_style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('template_selection')?></label>
                        <div class="col-md-9">
                            <label id="show_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $info['default_style']?>&module=formguide&templates=show&name=info&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});</script></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('js调用使用的模板');?></label>
                        <div class="col-md-9">
                            <label id="show_js_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $info['default_style']?>&module=formguide&templates=show_js&name=info&pc_hash='+pc_hash, function(data){$('#show_js_template').html(data.show_js_template);});</script></label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</form>
</div>
<script type="text/javascript">
function load_file_list(id) {
    if (id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=formguide&templates=show|show_js&name=info&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);$('#show_js_template').html(data.show_js_template);});
}

$(document).ready(function(){
    $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
    $('#name').formValidator({onshow:"<?php echo L('input_form_title')?>",onfocus:"<?php echo L('title_min_3_chars')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('title_cannot_empty')?>"});
    $('#tablename').formValidator({onshow:"<?php echo L('please_input_tallename')?>", onfocus:"<?php echo L('standard')?>", oncorrect:"<?php echo L('right')?>"}).regexValidator({regexp:"^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){0,19}$",onerror:"<?php echo L('tablename_was_wrong');?>"}).inputValidator({min:1,onerror:"<?php echo L('tablename_no_empty')?>"}).ajaxValidator({
        type : "get",
        url : "",
        data : "m=formguide&c=formguide&a=public_checktable",
        datatype : "html",
        cached:false,
        getdata:{issystem:'issystem'},
        async:'false',
        success : function(data){    
            if( data == "1" ){
                return true;
            } else {
                return false;
            }
        },
        buttons: $("#dosubmit"),
        onerror : "<?php echo L('tablename_existed')?>",
        onwait : "<?php echo L('connecting_please_wait')?>"
    });
    $('#starttime').formValidator({onshow:"<?php echo L('select_stardate')?>",onfocus:"<?php echo L('select_stardate')?>",oncorrect:"<?php echo L('right_all')?>"});
    $('#endtime').formValidator({onshow:"<?php echo L('select_downdate')?>",onfocus:"<?php echo L('select_downdate')?>",oncorrect:"<?php echo L('right_all')?>"});
    $('#style').formValidator({onshow:"<?php echo L('select_style')?>",onfocus:"<?php echo L('select_style')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('select_style')?>"});
});
$("input:radio[name='setting[enabletime]']").click(function (){
    if($("input:radio[name='setting[enabletime]']:checked").val()==0) {
        $("#time").hide();
    } else if($("input:radio[name='setting[enabletime]']:checked").val()==1) {
        $("#time").show();
    }
});
$("input:radio[name='setting[sendmail]']").click(function (){
    if($("input:radio[name='setting[sendmail]']:checked").val()==0) {
        $("#mailaddress").hide();
        $("#mailcontent").hide();
    } else if($("input:radio[name='setting[sendmail]']:checked").val()==1) {
        $("#mailaddress").show();
        $("#mailcontent").show();
    }
});
$("input:radio[name='setting[sendsms]']").click(function (){
    if($("input:radio[name='setting[sendsms]']:checked").val()==0) {
        $("#smsaddress").hide();
        $("#smscontent").hide();
    } else if($("input:radio[name='setting[sendsms]']:checked").val()==1) {
        $("#smsaddress").show();
        $("#smscontent").show();
    }
});
</script>
</div>
</div>
</body>
</html>