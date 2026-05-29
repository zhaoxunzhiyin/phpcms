<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form class="form-horizontal" role="form" id="myform" name="myform" action="?m=admin&c=linkage&a=public_listk_add" method="post">
<input type="hidden" name="key" value="<?php echo $key?>">
    <div class="form-body">
        <div class="form-group">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('模式');?></label>
            <div class="col-xs-8">
                <div class="mt-radio-inline">
                    <label class="mt-radio">
                        <input type="radio" name="all" value="0" onclick="$('.dr_more').hide();$('.dr_one').show();" checked > <?php echo L('单个');?>
                        <span></span>
                    </label>
                    <label class="mt-radio">
                        <input type="radio" name="all" value="1" onclick="$('.dr_more').show();$('.dr_one').hide();"> <?php echo L('批量');?>
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group" id="dr_row_pid">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('分类');?></label>
            <div class="col-xs-7">
                <?php echo $select;?>
            </div>
        </div>
        <div class="form-group dr_one" id="dr_row_name">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('名称');?></label>
            <div class="col-xs-7">
                <input type="text" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','cname',12);" class="form-control" id="name" name="data[name]" value="">
                <span class="help-block"> <?php echo L('它的描述名称');?> </span>
            </div>
        </div>
        <div class="form-group dr_one" id="dr_row_cname">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('别名');?></label>
            <div class="col-xs-7">
                <input type="text" class="form-control" id="cname" name="data[cname]" value="">
                <span class="help-block"> <?php echo L('别名只能由字母或者字母+数字组成');?> </span>
            </div>
        </div>

        <div class="form-group dr_more" id="dr_row_all" style="display:none">
            <label class="col-xs-3 control-label ajax_name"><?php echo L('批量');?></label>
            <div class="col-xs-7">
                <textarea name="data[all]" id="all" class="form-control" style="height:220px" rows="3"></textarea>

                <span class="help-block"> <?php echo L('换行分隔多条数据');?> </span>
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