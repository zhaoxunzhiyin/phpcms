<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" method="post" role="form" id="myform">
    <div class="form-body">

        <div class="form-group" id="dr_row_name">
            <label class="control-label col-xs-2"><?php echo L('name');?></label>
            <div class="col-xs-10">
                <input type="text" class="form-control" id="dr_name" name="name" value="<?php echo htmlspecialchars((string)$name); ?>">
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