<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" method="post" role="form" id="myform">
    <div class="form-body">

        <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('账号');?></label>
            <div class="col-xs-7">
                <p class="form-control-static"> <?php echo $username;?> </p>
            </div>
        </div>
        <div class="form-group" id="dr_row_name">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('新账号');?></label>
            <div class="col-xs-7">
                <input type="text" id="dr_name" class="form-control" value="<?php echo html2code($username);?>" name="name">
            </div>
        </div>

    </div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>