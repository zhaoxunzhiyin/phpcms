<?php defined('IN_CMS') or exit('No permission resources.');?>

    <?php echo linkage($setting);?>
    <div class="form-group">
      <label class="col-md-2 control-label">路径分隔符</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[space]" value="<?php echo $setting['space'];?>" class="form-control"></label>
            <span class="help-block">显示完整路径时生效</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">强制选择最终项</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ck_child]" value="1" <?php if($setting['ck_child']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ck_child]" value="0" <?php if(!$setting['ck_child']) echo 'checked';?>> 关闭 <span></span></label>
          </div>
          <span class="help-block">开启后会强制要求用户选择最终一个选项，需要启用必须验证才会生效</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
      </div>
    </div>