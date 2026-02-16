<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link href="<?php echo JS_PATH?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form action="?m=bdts&c=bdts&a=url_add&menuid=<?php echo $this->input->get('menuid');?>" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">

    <div class="form-body">

        <div class="form-group" id="dr_row_url">
            <label class="col-xs-12 control-label"><?php echo L('输入URL');?>：</label>
            <div class="col-xs-12">
                <input class="form-control" type="text" class="form-control" id="url" name="url">
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