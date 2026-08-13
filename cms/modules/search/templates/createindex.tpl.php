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
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
    <p><?php echo L('re_index_note');?></p>
</div>
<form action="" class="form-horizontal" method="get" name="myform" id="myform">
<input type="hidden" name="m" value="search">
<input type="hidden" name="c" value="search_admin">
<input type="hidden" name="a" value="createindex">
<input type="hidden" name="menuid" value="<?php echo $this->input->get('menuid');?>">
<input type="hidden" name="dosubmit" value="1">
    <div class="portlet bordered light">
        <div class="portlet-body">
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('re_index_updates');?></label>
                    <div class="col-md-10">
                        <label><input type="text" name="pagesize" id="pagesize" value="100" class="form-control"></label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('re_index_number');?></label>
                    <div class="col-md-10">
                        <label><input type="text" name="num" id="num" value="10" class="form-control"></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo L('confirm_reindex')?></button>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
</body>
</html>