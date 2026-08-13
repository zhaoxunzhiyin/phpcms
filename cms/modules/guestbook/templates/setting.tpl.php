<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
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
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('guestbook_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('guestbook_setting');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('application_or_not')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[is_post]" value="1"<?php echo ($is_post) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[is_post]" value="0"<?php echo (!$is_post) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('code_or_not')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablecheckcode]" value="1"<?php echo ($enablecheckcode) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablecheckcode]" value="0"<?php echo (!$enablecheckcode) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_mail');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="1"<?php echo ($sendmail) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendmail]" value="0"<?php echo (!$sendmail) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="mailaddress"<?php echo (!$sendmail) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('e-mail_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mails" name="setting[mails]" value="<?php echo $mails;?>" >
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="mailcontent"<?php echo (!$sendmail) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('mailmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="mailmessage" name="setting[mailmessage]" style="height:100px"><?php echo $mailmessage;?></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('allowed_send_sms');?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="1"<?php echo ($sendsms) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sendsms]" value="0"<?php echo (!$sendsms) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" id="smsaddress"<?php echo (!$sendsms) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('sms_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="mobiles" name="setting[mobiles]" value="<?php echo $mobiles;?>" >
                            <span class="help-block"><?php echo L('multiple_with_commas')?></span>
                        </div>
                    </div>
                    <div class="form-group" id="smscontent"<?php echo (!$sendsms) ? ' style="display:none;"' : ''?>>
                        <label class="col-md-2 control-label"><?php echo L('smsmessage');?></label>
                        <div class="col-md-9">
                            <textarea id="smsmessage" name="setting[smsmessage]" style="height:100px"><?php echo $smsmessage;?></textarea>
                            <span class="help-block"><?php echo L('setting_message')?></span>
                        </div>
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