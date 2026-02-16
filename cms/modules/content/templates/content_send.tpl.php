<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="page-body" style="margin-top:20px;margin-bottom:30px;">
<form class="form-horizontal" method="post" role="form" id="myform">
<?php foreach($id as $_v) {?>
<input type="hidden" name="ids[]" value="<?php echo $_v;?>" />
<?php }?>
    <div class="portlet-body">
                <div class="form-group">
            <div class="col-md-12">
                <label class="">本次批量处理</label>
                <label class="label label-danger"><?php echo dr_count($id);?></label>
                <label class="">条数据</label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-2 control-label ajax_name">退稿理由</label>
            <div class="col-md-8">
                <textarea class="form-control" id="dr_reject_c" name="reject_c" rows="5"></textarea>
                <span class="help-block"><?php echo L('reject_msg');?></span>
            </div>
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