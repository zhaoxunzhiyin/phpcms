<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" role="form" id="myform" name="myform" action="?m=admin&c=linkage&a=public_list_edit" method="post">
<input type="hidden" name="id" value="<?php echo $id?>">
<input type="hidden" name="key" value="<?php echo $key?>">
    <div class="form-body">
        <div class="form-group" id="dr_row_pid">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('分类');?></label>
            <div class="col-xs-7">
                <?php echo $select;?>
            </div>
        </div>
        <div class="form-group dr_one" id="dr_row_name">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('名称');?></label>
            <div class="col-xs-7">
                <input type="text" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','cname',12);" class="form-control" id="name" name="data[name]" value="<?php echo htmlspecialchars($data['name']);?>">
                <span class="help-block"> <?php echo L('它的描述名称');?> </span>
            </div>
        </div>
        <div class="form-group dr_one" id="dr_row_cname">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('别名');?></label>
            <div class="col-xs-7">
                <input type="text" class="form-control" id="cname" name="data[cname]" value="<?php echo htmlspecialchars($data['cname']);?>">
                <span class="help-block"> <?php echo L('别名只能由字母或者字母+数字组成');?> </span>
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