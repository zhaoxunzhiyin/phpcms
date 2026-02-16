<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('attachment_address_replace_msg');?></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('附件地址替换').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('附件地址替换');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('old_attachment_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" name="old_attachment_path" id="old_attachment_path" value="<?php echo SYS_UPLOAD_URL;?>" >
                            <span class="help-block"><?php echo L('old_attachment_address_msg');?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('new_attachment_address');?></label>
                        <div class="col-md-9">
                            <input class="form-control" type="text" name="new_attachment_path" id="new_attachment_path" value="" >
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="Dialog.confirm('<?php echo L('form_submit_confirm')?>',function(){dr_ajax_submit('<?php echo dr_now_url();?>&page='+$('#dr_page').val(), 'myform', '2000');});" class="btn green"> <i class="fa fa-save"></i> <?php echo L('保存');?></button>
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