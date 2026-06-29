<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<?php echo load_js(JS_PATH.'content_addtop.js');?>
<?php echo load_css(JS_PATH.'jquery-minicolors/jquery.minicolors.css');?>
<?php echo load_js(JS_PATH.'jquery-minicolors/jquery.minicolors.min.js');?>
<?php echo load_js(JS_PATH.'cookie.js');?>
<script type="text/javascript">var catid=<?php echo intval($catid);?></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                <div class="page-body">
<form name="myform" id="myform" action="?m=content&c=content&a=add" class="form-horizontal" method="post">
<input value="1" type="hidden" name="dosubmit">
    <div class="">
        <div class="row ">
            <div class="<?php if (is_mobile()){?>col-md-12<?php }else{?>col-md-9<?php }?>">

                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-blue sbold"></span>
                        </div>

                    </div>
                    <div class="portlet-body">
                        <div class="form-body">
                            <?php
if(is_array($forminfos['base'])) {
 foreach($forminfos['base'] as $field=>$info) {
     if($info['isomnipotent']) continue;
     if($info['formtype']=='omnipotent') {
        foreach($forminfos['base'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
        foreach($forminfos['senior'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
    }
 ?>
<div class="form-group" id="dr_row_<?php echo $field?>">
    <label class="control-label col-md-2"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
    <div class="col-md-10">
        <?php echo $info['form']?>
        <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
    </div>
</div>
<?php
} }
?>
                   </div>
                    </div>
                </div>

                
            </div>
            <div class="<?php if (is_mobile()){?>col-md-12<?php }else{?>col-md-3<?php }?> my-sysfield" >
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="form-body">
                          <?php
if(is_array($forminfos['senior'])) {
 foreach($forminfos['senior'] as $field=>$info) {
    if($info['isomnipotent']) continue;
    if($info['formtype']=='omnipotent') {
        foreach($forminfos['base'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
        foreach($forminfos['senior'] as $_fm=>$_fm_value) {
            if($_fm_value['isomnipotent']) {
                $info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'],$info['form']);
            }
        }
    }
 ?>
<div class="form-group" id="dr_row_<?php echo $field?>">
    <label class="control-label col-md-2"><?php if($info['star']){ ?><span class="required" aria-required="true"> * </span><?php } ?><?php echo $info['name']?></label>
    <div class="col-md-10">
        <?php echo $info['form']?>
        <span class="help-block" id="dr_<?php echo $field?>_tips"><?php echo $info['tips']?></span>
    </div>
</div>
<?php
} }
?>
<?php if(cleck_admin(param::get_session('roleid')) || $priv_status) {?>
<div class="form-group">
    <label class="control-label col-md-2"><?php echo L('c_status');?></label>
    <div class="col-md-10">
        <div class="mt-radio-inline"><label class="mt-radio mt-radio-outline"><input type="radio" name="status" value="99" checked/> <?php echo L('c_publish');?> <span></span></label>
<?php if($workflowid) { ?><label class="mt-radio mt-radio-outline"><input type="radio" name="status" value="1" > <?php echo L('c_check');?> <span></span></label><?php }?>
</div>
    </div>
</div>
<?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        
    </div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript"> 
<!--
//只能放到最下面
$(function(){
/*
 * 加载禁用外边链接
 */

    $('#linkurl').attr('disabled',true);
    $('#islink').attr('checked',false);
    $('.edit_content').hide();
})
//-->
</script>
</body>
</html>