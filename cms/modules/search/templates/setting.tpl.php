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
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('basic_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-cog"></i> <?php if (is_pc()) {echo L('basic_setting');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('sphinx_setting').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-search"></i> <?php if (is_pc()) {echo L('sphinx_setting');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('pagination_count')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="pagesize" name="setting[pagesize]" value="<?php echo $pagesize?>" ></label>
                            <span class="help-block"><?php echo L('pagination_count_desc')?></span>
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('sphinxenable')?></label>
                        <div class="col-md-9">
                            <div class="mt-radio-inline">
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sphinxenable]" value="1" <?php if($sphinxenable) {?>checked<?php }?>> <?php echo L('yes');?> <span></span></label>
                                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[sphinxenable]" value="0" <?php if(!$sphinxenable) {?>checked<?php }?>> <?php echo L('no');?> <span></span></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('host')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="sphinxhost" name="setting[sphinxhost]" value="<?php echo $sphinxhost?>" ></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('port')?></label>
                        <div class="col-md-9">
                            <div class="input-group input-large">
                                <input class="form-control" type="text" id="sphinxport" name="setting[sphinxport]" value="<?php echo $sphinxport?>" >
                                <span class="input-group-btn">
                                    <button class="btn blue" onclick="test_sphinx()" type="button"><i class="fa fa-wrench"></i> <?php echo L('test');?></button>
                                </span>
                            </div>
                            <span class="help-block" id='testing'></span>
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
<script type="text/javascript">
function test_sphinx() {
    $('#testing').html('<?php echo L('testing')?>');
    $.post('?m=search&c=search_admin&a=public_test_sphinx',{sphinxhost:$('#sphinxhost').val(),sphinxport:$('#sphinxport').val()}, function(data){
        message = '';
        if(data == 1) {
            message = '<?php echo L('testsuccess')?>';
        } else if(data == -1) {
            message = '<?php echo L('hostempty')?>';
        } else if(data == -2) {
            message = '<?php echo L('portempty')?>';
        } else {
            message = data;
        }
        $('#testing').html(message);
    });
}
</script>
</div>
</div>
</body>
</html>