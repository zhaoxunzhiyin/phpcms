<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="?m=admin&c=category&a=init&menuid=<?php echo $this->input->get('menuid');?>"><?php echo L('category_manage');?></a></p>
</div>
<form action="?m=admin&c=category&a=remove" class="form-horizontal" method="post" name="myform" id="myform">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid');?>">
<div class="myfbody">
<div class="portlet bordered light">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li class="active">
                <a data-toggle="tab_0"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('remove','','content').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-arrows"></i> <?php if (is_pc()) {echo L('remove','','content');}?> </a>
            </li>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane active" id="tab_0">
                <div class="form-body">

                    <div class="form-group row">
                        <label class="col-md-2 control-label"> <?php echo L('from_where','','content');?> </label>
                        <div class="col-md-9">
                            <label><select name="fromid[]" id="fromid" class="form-control bs-select" data-title="<?php echo L('from_category','','content');?>" multiple="multiple" data-actions-box="true">
                                <option value='0' style="background:#F1F3F5;color:blue;"><?php echo L('from_category','','content');?></option>
                                <?php echo $source_string;?>
                            </select></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 control-label"><?php echo L('move_to_categorys','','content');?></label>
                        <div class="col-md-9">
                            <label><select name="tocatid" id="tocatid" class="form-control bs-select" data-title="<?php echo L('move_to_categorys','','content');?>">
                                <option value='0' style="background:#F1F3F5;color:blue;"><?php echo L('move_to_categorys');?></option>
                                <?php echo $string;?>
                            </select></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="portlet-body form myfooter">
            <div class="form-actions text-center">
                <button type="button" onclick="dr_ajax_submit('?m=admin&c=category&a=remove', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
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