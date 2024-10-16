<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">显示级层</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[level]" value="<?php echo $setting['level'];?>" size="30" class="form-control"></label>
            <span class="help-block"><?php echo L('值越大地图显示越详细')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" size="10" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">高度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[height]" value="<?php echo $setting['height'];?>" size="10" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
      </div>
    </div>
