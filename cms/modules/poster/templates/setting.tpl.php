<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" action="?m=poster&c=space&a=setting" method="post" name="myform" id="myform">
    <div class="form-body">
                <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('ads_show_time')?></label>
            <div class="col-xs-8">
                <div class="mt-radio-inline">
                    <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablehits]" value="1"<?php echo ($enablehits) ? ' checked' : ''?>> <?php echo L('yes');?> <span></span></label>
                    <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablehits]" value="0"<?php echo (!$enablehits) ? ' checked' : ''?>> <?php echo L('no');?> <span></span></label>
                </div>
            </div>
        </div>
                <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('upload_file_ext')?></label>
            <div class="col-xs-8">
                <input type="text" id="ext" class="form-control" name="setting[ext]" value="<?php echo $ext?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('file_size')?></label>
            <div class="col-xs-8">
                <input type="text" id="maxsize" class="form-control" name="setting[maxsize]" value="<?php echo $maxsize?>">
                <span class="help-block"> M </span>
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
