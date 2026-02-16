<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
$(function(){
    $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
    $("#subject").formValidator({onshow:"<?php echo L('input','','admin').L('subject')?>",onfocus:"<?php echo L('subject').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('subject').L('no_empty')?>"});
    $("#con").formValidator({onshow:"<?php echo L('content').L('no_empty')?>",onfocus:"<?php echo L('content').L('no_empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('content').L('no_empty')?>"});
      $("#tousername").formValidator({onshow:"<?php echo L('input','','admin').L('touserid')?>",onfocus:"<?php echo L('touserid').L('no_empty')?>"}).inputValidator({min:1,onerror:"<?php echo L('input','','admin').L('touserid')?>"}).ajaxValidator({type : "get",url : "",data :"m=message&c=message&a=public_name",datatype : "html",async:'true',success : function(data){if( data == 1 ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('not_myself')?>! ",onwait : "<?php echo L('connecting')?>"});
})
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<form action="?m=message&c=message&a=send_one" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<input type="hidden" name="dosubmit" value="1">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('send_one').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-send"></i> <?php if (is_pc()) {echo L('send_one');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('subject')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="subject" name="info[subject]" value="" ></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('touserid')?></label>
                        <div class="col-md-9">
                            <label><input class="form-control input-large" type="text" id="tousername" name="info[send_to_id]" value="" ></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('content')?></label>
                        <div class="col-md-9">
                            <textarea name="info[content]" id="con" style="height:100px"></textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
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