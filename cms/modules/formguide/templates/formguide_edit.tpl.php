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
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="formid" id="formid" type="hidden" value="<?php echo $this->input->get('formid')?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid')?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('基本设置').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('基本设置');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('后台列表显示字段').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-table"></i> <?php if (is_pc()) {echo L('后台列表显示字段');}?> </a>
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
                            <label><input class="form-control input-large" type="text" id="name" name="info[name]" value="<?php echo new_html_special_chars($data['name']);?>" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','tablename',12);"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('tablename');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="tablename" name="info[tablename]" value="<?php echo $data['tablename'];?>" readonly></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('introduction');?></label>
                        <div class="col-md-9">
                            <textarea class="form-control " style="height:90px" name="info[description]"><?php echo $data['description'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('time_limit');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enabletime]" value="1"<?php echo ($data['setting']['enabletime']) ? ' checked' : ''?>> <?php echo L('enable');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enabletime]" value="0"<?php echo (!$data['setting']['enabletime']) ? ' checked' : ''?>> <?php echo L('unenable');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="time"<?php echo (!$data['setting']['enabletime']) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('时间范围');?></label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <div class="input-group date-picker input-daterange " data-date="" data-date-format="yyyy-mm-dd">
                                    <input type="text" placeholder="<?php echo L('start_time');?>" class="form-control" value="<?php echo ($data['setting']['starttime'] ? dr_date($data['setting']['starttime'], 'Y-m-d') : '');?>" name="setting[starttime]">
                                    <span class="input-group-addon"> <?php echo L('到');?> </span>
                                    <input type="text" placeholder="<?php echo L('end_time');?>" class="form-control" value="<?php echo ($data['setting']['endtime'] ? dr_date($data['setting']['endtime'], 'Y-m-d') : '');?>" name="setting[endtime]">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_mail');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="1"<?php echo ($data['setting']['sendmail']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="0"<?php echo (!$data['setting']['sendmail']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="mailaddress"<?php echo (!$data['setting']['sendmail']) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('e-mail_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mails" name="setting[mails]" value="<?php echo $data['setting']['mails'];?>" >
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="mailcontent"<?php echo (!$data['setting']['sendmail']) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('mailmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="mailmessage" name="setting[mailmessage]" style="height:100px"><?php echo $data['setting']['mailmessage'];?></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_sms');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="1"<?php echo ($data['setting']['sendsms']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="0"<?php echo (!$data['setting']['sendsms']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="smsaddress"<?php echo (!$data['setting']['sendsms']) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('sms_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mobiles" name="setting[mobiles]" value="<?php echo $data['setting']['mobiles'];?>" >
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="smscontent"<?php echo (!$data['setting']['sendsms']) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('smsmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="smsmessage" name="setting[smsmessage]" style="height:100px"><?php echo $data['setting']['smsmessage'];?></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allows_more_ip');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowmultisubmit]" value="1"<?php echo ($data['setting']['allowmultisubmit']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowmultisubmit]" value="0"<?php echo (!$data['setting']['allowmultisubmit']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowunreg');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowunreg]" value="1"<?php echo ($data['setting']['allowunreg']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowunreg]" value="0"<?php echo (!$data['setting']['allowunreg']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('code');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[code]" value="1"<?php echo ($data['setting']['code']) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[code]" value="0"<?php echo (!$data['setting']['code']) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('提交成功提示文字');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="rt_text" name="setting[rt_text]" value="<?php echo $data['setting']['rt_text'];?>"></label>
                            <span class="help-block"><?php echo L('当用户提交表单成功之后显示的文字，默认为：感谢您的参与！')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('提交成功跳转URL');?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="rt_url" name="setting[rt_url]" value="<?php echo $data['setting']['rt_url'];?>"></label>
                            <span class="help-block"><?php echo L('当用户提交表单成功之后跳转的链接，{APP_PATH}表示当前站点URL，{formid}表示当前表单的id号，{siteid}表示当前站点的id号')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('optional_style');?></label>
                        <div class="col-md-9">
                            <label><?php echo form::select($template_list, $data['default_style'], 'name="info[default_style]" id="style" onchange="load_file_list(this.value)"', L('please_select'))?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('template_selection')?></label>
                        <div class="col-md-9">
                            <label id="show_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $data['default_style']?>&id=<?php echo $data['show_template']?>&module=formguide&templates=show&name=info&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);});</script></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('js调用使用的模板');?></label>
                        <div class="col-md-9">
                            <label id="show_js_template"><script type="text/javascript">$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style=<?php echo $data['default_style']?>&id=<?php echo $data['js_template']?>&module=formguide&templates=show_js&name=info&pc_hash='+pc_hash, function(data){$('#show_js_template').html(data.show_js_template);});</script></label>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="table-list">
                        <table class="table table-striped table-bordered table-hover table-checkable dataTable">
                            <thead>
                            <tr class="heading">
                                <th class="myselect">
                                    <?php echo L('显示');?>
                                </th>
                                <th width="180"> <?php echo L('字段');?> </th>
                                <th width="150"> <?php echo L('名称');?> </th>
                                <th width="100"> <?php echo L('宽度');?> </th>
                                <th width="140"> <?php echo L('对其方式');?> </th>
                                <th> <?php echo L('回调方法');?> </th>
                            </tr>
                            </thead>
                            <tbody class="field-sort-items">
                            <?php 
                            if(is_array($field)){
                            foreach($field as $n=>$t){
                            if ($t['field']) {
                            ?>
                            <tr class="odd gradeX">
                                <td class="myselect">
                                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                        <input type="checkbox" class="checkboxes" name="setting[list_field][<?php echo $t['field'];?>][use]" value="1" <?php if ($data['setting']['list_field'][$t['field']]['use']){?> checked<?php }?> />
                                        <span></span>
                                    </label>
                                </td>
                                <td><?php echo L($t['name']);?> (<?php echo $t['field'];?>)</td>
                                <td><input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][name]" value="<?php echo $data['setting']['list_field'][$t['field']]['name'] ? htmlspecialchars($data['setting']['list_field'][$t['field']]['name']) : $t['name'];?>" /></td>
                                <td> <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][width]" value="<?php echo htmlspecialchars((string)$data['setting']['list_field'][$t['field']]['width']);?>" /></td>
                                <td><input type="checkbox" name="setting[list_field][<?php echo $t['field'];?>][center]" <?php if ($data['setting']['list_field'][$t['field']]['center']){?> checked<?php }?> value="1"  data-on-text="<?php echo L('居中');?>" data-off-text="<?php echo L('默认');?>" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                                </td>
                                <td> <div class="input-group" style="width:250px">
                                        <span class="input-group-btn">
                                            <a class="btn btn-success" href="javascript:help('?m=content&c=sitemodel&a=public_help&pc_hash='+pc_hash);"><?php echo L('回调');?></a>
                                        </span>
                                    <input class="form-control" type="text" name="setting[list_field][<?php echo $t['field'];?>][func]" value="<?php echo htmlspecialchars((string)$data['setting']['list_field'][$t['field']]['func']);?>" />
                                </div></td>
                            </tr>
                            <?php }}}?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
<script type="text/javascript">
$(function() {
    $(".field-sort-items").sortable();
    handleBootstrapSwitch();
});
function load_file_list(id) {
    if (id=='') return false;
    $.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&module=formguide&templates=show|show_js&name=info&pc_hash='+pc_hash, function(data){$('#show_template').html(data.show_template);$('#show_js_template').html(data.show_js_template);});
}
$(document).ready(function(){
    $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
    $('#name').formValidator({onshow:"<?php echo L('input_form_title')?>",onfocus:"<?php echo L('title_min_3_chars')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('title_cannot_empty')?>"}).defaultPassed();
    $('#tablename').formValidator({onshow:"<?php echo L('please_input_tallename')?>", onfocus:"<?php echo L('standard')?>", oncorrect:"<?php echo L('right')?>"}).regexValidator({regexp:"^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){0,19}$",onerror:"<?php echo L('tablename_was_wrong');?>"}).inputValidator({min:1,onerror:"<?php echo L('tablename_no_empty')?>"}).ajaxValidator({
        type : "get",
        url : "",
        data : "m=formguide&c=formguide&a=public_checktable&formid=<?php echo $this->input->get('formid')?>",
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
    }).defaultPassed();
    $('#starttime').formValidator({onshow:"<?php echo L('select_stardate')?>",onfocus:"<?php echo L('select_stardate')?>",oncorrect:"<?php echo L('right_all')?>"}).defaultPassed();
    $('#endtime').formValidator({onshow:"<?php echo L('select_downdate')?>",onfocus:"<?php echo L('select_downdate')?>",oncorrect:"<?php echo L('right_all')?>"}).defaultPassed();
    $('#style').formValidator({onshow:"<?php echo L('select_style')?>",onfocus:"<?php echo L('select_style')?>",oncorrect:"<?php echo L('right')?>"}).inputValidator({min:1,onerror:"<?php echo L('select_style')?>"}).defaultPassed();
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