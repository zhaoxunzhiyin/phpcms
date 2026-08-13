<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_validator = true;include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<input type="hidden" name="info[userid]" value="<?php echo $userid?>"></input>
<input type="hidden" name="info[username]" value="<?php echo $username?>"></input>
<input type="hidden" name="info[admin_manage_code]" value="<?php echo $admin_manage_code?>" id="admin_manage_code">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('editpwd').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('editpwd');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('username')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $username;?> (<?php echo L('realname')?> <?php echo $realname?>)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('email')?></label>
                        <div class="col-md-9">
                            <div class="form-control-static"><?php echo $email;?></div>
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_old_password">
                        <label class="col-md-2 control-label"><?php echo L('old_password')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="password" id="dr_old_password" name="old_password" value="">
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_new_password">
                        <label class="col-md-2 control-label"><?php echo L('new_password')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="password" id="dr_new_password" name="new_password" value="">
                        </div>
                    </div>
                    <div class="form-group" id="dr_row_new_pwdconfirm">
                        <label class="col-md-2 control-label"><?php echo L('new_pwdconfirm')?></label>
                        <div class="col-md-9">
                            <input class="form-control input-large" type="password" id="dr_new_pwdconfirm" name="new_pwdconfirm" value="">
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
</div>
</div>
</body>
</html>