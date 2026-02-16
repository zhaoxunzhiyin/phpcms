<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
    <div class="row table-search-tool">
<form action="" method="get">
<input type="hidden" name="m" value="search">
<input type="hidden" name="c" value="search_admin">
<input type="hidden" name="a" value="createindex">
<input type="hidden" name="menuid" value="<?php echo $this->input->get('menuid');?>">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo dr_get_csrf_token();?>" name="pc_hash">
<div class="col-md-12 col-sm-12">
    <label><?php echo L('re_index_note');?></label>
</div>
<div class="col-md-12 col-sm-12">
    <label><input type="text" class="form-control" name="pagesize" value="100" size="5"></label>
</div>
<div class="col-md-12 col-sm-12">
    <label><?php echo L('tiao');?></label>
</div>
<div class="col-md-12 col-sm-12">
    <label><button type="submit" class="btn green btn-sm onloading" name="submit"> <i class="fa fa-refresh"></i> <?php echo L('confirm_reindex')?></button></label>
</div>
</form>
    </div>
</div>
</div>
</div>
</div>
</body>
</html>