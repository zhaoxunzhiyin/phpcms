<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" action="?m=guestbook&c=guestbook&a=add_type" method="post" name="myform" id="myform">
    <div class="form-body">
                <div class="form-group" id="dr_row_name">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('type_name')?></label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="name" name="type[name]" value="">
            </div>
        </div>
                <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('link_type_listorder')?></label>
            <div class="col-xs-8">
                <input type="text" id="listorder" class="form-control" name="type[listorder]" value="0">
                <span class="help-block"> 排序值由小到大排列，范围为0-255 </span>
            </div>
        </div>
        <div class="form-group" id="dr_row_description">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('type_description')?></label>
            <div class="col-xs-8">
                <textarea class="form-control" style="height:60px;" name="type[description]" id="dr_description"></textarea>
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
