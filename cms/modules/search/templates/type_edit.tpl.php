<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input type="hidden" name="typeid" value="<?php echo $typeid?>">
    <div class="portlet bordered light">
        <div class="portlet-body">
            <div class="form-body">
                <div class="form-group">
                    <label class="col-md-2 control-label"><?php echo L('select_module_name');?></label>
                    <div class="col-md-9">
                        <?php if($modelid && $typedir == 'yp') {
                            $module = 'yp';
                        } elseif($modelid && $typedir != 'yp') {
                            $module = 'content';
                        } else {
                            $module = $module;
                        }
                        echo form::select($module_data,$module,'name="module" disabled');?>
                        <input name="module" type="hidden" value="<?php echo $module;?>"><input name="typedir" type="hidden" value="<?php echo $typedir;?>">
                    </div>
                </div>
                <div class="form-group modelid" id="dr_row_modelid" style="display: none;">
                    <label class="col-md-2 control-label"><?php echo L('select_model_name');?></label>
                    <div class="col-md-9">
                       <?php echo form::select($model_data,$modelid,'name="info[modelid]"');?>
                    </div>
                </div>
                <div class="form-group yp_modelid" id="dr_row_yp_modelid" style="display: none;">
                    <label class="col-md-2 control-label"><?php echo L('select_model_name');?></label>
                    <div class="col-md-9">
                       <?php echo form::select($yp_model_data,$modelid,'name="info[yp_modelid]"');?>
                    </div>
                </div>
                <div class="form-group" id="dr_row_name">
                    <label class="col-md-2 control-label"><?php echo L('type_name');?></label>
                    <div class="col-md-10">
                        <input type="text" name="info[name]" id="name" value="<?php echo $name;?>" class="form-control">
                    </div>
                </div>
                <div class="form-group" id="dr_row_description">
                    <label class="col-md-2 control-label"><?php echo L('description');?></label>
                    <div class="col-md-10">
                        <textarea name="info[description]" maxlength="255" class="form-control"><?php echo $description;?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
</div>
</div>
<script type="text/javascript">
$(function() {
    change_module('<?php echo $module;?>');
});
function change_module(module) {
    if(module == 'yp') {
        $('.modelid').hide();
        $('.yp_modelid').show();
    } else if(module == 'special') {
        $('.modelid').hide();
        $('.yp_modelid').hide();
    } else {
        $('.yp_modelid').hide();
        $('.modelid').show();
    }
}
</script>
</body>
</html>