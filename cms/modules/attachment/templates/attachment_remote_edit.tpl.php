<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body" style="margin-top:20px;margin-bottom:30px;">
<form class="form-horizontal" method="post" role="form" id="myform">
    <div class="form-body">
        <div class="form-group">
            <label class="col-xs-12 control-label "><?php echo L('原来的储存策略');?></label>
            <div class="col-xs-12">
                <label style="width: 200px">
                    <div class="input-group">
                        <input type="text" name="data[o]" id="dr_old" class="form-control">
                        <div class="input-group-btn">
                            <button type="button" class="btn default dropdown-toggle" data-toggle="dropdown" tabindex="-1">
                                <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li>
                                    <a href="javascript:$('#dr_old').val('0');"> <?php echo L('默认');?> </a>
                                </li>
                                <?php 
                                if (is_array($remote)) {
                                foreach ($remote as $t) {
                                ?>
                                <li>
                                    <a href="javascript:$('#dr_old').val('<?php echo $t['id'];?>');"> <?php echo $t['name'];?> </a>
                                </li>
                                <?php }} ?>
                            </ul>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-12 control-label "><?php echo L('变更后的储存策略');?></label>
            <div class="col-xs-12">
                <label>
                    <select name="data[n]" class="form-control">
                        <option value="0"> <?php echo L('默认');?> </option>
                        <?php 
                        if (is_array($remote)) {
                        foreach ($remote as $t) {
                        ?>
                        <option value="<?php echo $t['id'];?>"><?php echo $t['name'];?></option>
                        <?php }} ?>
                    </select>
                </label>
                <p class="help-block"><?php echo L('需要手动将原来的储存附件复制到新的储存策略的目录中');?></p>
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