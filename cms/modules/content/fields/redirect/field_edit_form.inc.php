<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">控件宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
