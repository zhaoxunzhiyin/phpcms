<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">每个位置宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认选中项</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('多个之间用半角逗号隔开')?></span>
      </div>
    </div>
