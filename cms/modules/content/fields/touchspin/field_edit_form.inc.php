<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"> </label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">最大值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[maxnumber]" value="<?php echo $setting['maxnumber'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">最小值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[minnumber]" value="<?php echo $setting['minnumber'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">步长值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[step]" id="step" value="<?php echo $setting['step'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
	  <label class="col-md-2 control-label">显示模式</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show]" value="1" <?php if($setting['show']) echo 'checked';?> /> 按钮 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show]" value="0" <?php if(!$setting['show']) echo 'checked';?> /> 箭头 <span></span></label>
          <span></span></label>
        </div>
	  </div>
	</div>
    <div class="form-group">
      <label class="col-md-2 control-label">加按钮颜色</label>
      <div class="col-md-9">
            <?php echo color_select('setting[up]', $setting['up']);?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">减按钮颜色</label>
      <div class="col-md-9">
            <?php echo color_select('setting[down]', $setting['down']);?>
      </div>
    </div>
