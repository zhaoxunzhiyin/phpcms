<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_validator = true;include $this->admin_tpl('header');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('管理员账号允许同时拥有多个角色组');?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input type="hidden" name="info[admin_manage_code]" value="<?php echo $admin_manage_code?>" id="admin_manage_code">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('管理员').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('管理员');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group" id="dr_row_username">
                        <label class="col-md-2 control-label"><?php echo L('username')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_username" name="info[username]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_password">
                        <label class="col-md-2 control-label"><?php echo L('password')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="password" id="dr_password" name="info[password]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_pwdconfirm">
                        <label class="col-md-2 control-label"><?php echo L('cofirmpwd')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="password" id="dr_pwdconfirm" name="info[pwdconfirm]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_email">
                        <label class="col-md-2 control-label"><?php echo L('email')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_email" name="info[email]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_phone">
                        <label class="col-md-2 control-label"><?php echo L('phone')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_phone" name="info[phone]" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('realname')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="text" id="dr_realname" name="info[realname]" value="" >
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_roleid">
                        <label class="col-md-2 control-label"><?php echo L('userinrole')?></label>
                        <div class="col-md-9">
                            <div class="mt-checkbox-list">
                                <?php foreach($this->role as $rid=>$role){?>
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="info[roleid][]"<?php echo (($info['role'][$rid]) ? ' checked' : '')?> value="<?php echo $rid?>"> <?php echo $role['rolename']?>
                                    <span></span>
                                </label>
                                <?php }?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label>
                <label><button type="button" onclick="dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000', '<?php echo $reply_url;?>')" class="btn yellow"> <i class="fa fa-mail-reply-all"></i> <?php echo L('保存并返回')?></button></label>
            </div>
        </div>
    </div>
</div>
</div>
</form>
</div>
</div>
</div>
</body>
</html>