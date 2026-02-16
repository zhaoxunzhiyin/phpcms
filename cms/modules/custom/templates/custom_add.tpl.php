<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form name="myform" id="myform" action="?m=custom&c=custom&a=add" class="form-horizontal" method="post">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <div class="form-body clear">

<div class="form-group">
    <label class="control-label col-md-2"><?php echo L('custom_title')?></label>
    <div class="col-md-10">
        <input type="text" name="custom[title]" id="title" value="" style="width:100%;" class="measure-input">
        <span class="help-block" id="dr_title_tips">（<?php echo L('custom_title_tips')?>）</span>
    </div>
</div>
<div class="form-group" id="dr_row_content">
    <label class="control-label col-md-2"><?php echo L('content');?></label>
    <div class="col-md-10">
        <textarea class="dr_ueditor dr_ueditor_content" name="custom[content]" id="content"></textarea>
        <?php echo form::editor('content',"full");?>
    </div>
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
</body>
</html> 