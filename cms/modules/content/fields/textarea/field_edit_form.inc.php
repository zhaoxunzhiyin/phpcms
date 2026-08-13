<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">文本域宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">文本域高度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[height]" value="<?php echo $setting['height'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <textarea name="setting[defaultvalue]" style="height:100px;" id="defaultvalue" class="form-control"><?php echo $setting['defaultvalue'];?></textarea>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">是否允许Html</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablehtml]" value="1" <?php if($setting['enablehtml']==1) {?>checked<?php }?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablehtml]" value="0" <?php if($setting['enablehtml']==0) {?>checked<?php }?>> 否 <span></span></label>
        </div></label>
      </div>
    </div>
