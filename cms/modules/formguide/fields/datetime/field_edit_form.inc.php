<?php defined('IN_CMS') or exit('No permission resources.');?>
    <div class="form-group">
      <label class="col-md-2 control-label">宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">类型</label>
      <div class="col-md-9">
        <div class="mt-radio-inline">
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="int" onclick="$('#date').show();$('#time').hide();" <?php if($setting['fieldtype']=='int') echo 'checked';?>>日期<span></span></label>
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="varchar" onclick="$('#date').hide();$('#time').show();" <?php if($setting['fieldtype']=='varchar') echo 'checked';?>>时间<span></span></label>
        </div>
      </div>
    </div>
    <div class="form-group" id="date"<?php if($setting['fieldtype']=='varchar') echo ' style="display:none;"';?>> 
      <label class="col-md-2 control-label">日期格式</label>
      <div class="col-md-9">
        <div class="mt-radio-inline">
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[format]" value="1" <?php if($setting['format']) echo 'checked';?>>日期时间格式<span></span></label>
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[format]" value="0" <?php if(!$setting['format']) echo 'checked';?>>日期格式<span></span></label>
        </div>
      </div>
    </div>
    <div class="form-group" id="time"<?php if($setting['fieldtype']=='int') echo ' style="display:none;"';?>> 
      <label class="col-md-2 control-label">时间格式</label>
      <div class="col-md-9">
        <div class="mt-radio-inline">
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[format2]" value="0" <?php if(!$setting['format2']) echo 'checked';?>>时分格式<span></span></label>
            <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[format2]" value="1" <?php if($setting['format2']) echo 'checked';?>>时分秒格式<span></span></label>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">图标显示</label>
      <div class="col-md-9">
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[is_left]" value="0"<?php if(!$setting['is_left']) echo ' checked';?>/>左侧图标<span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[is_left]" value="1"<?php if($setting['is_left']) echo ' checked';?>/>右侧图标<span></span></label>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">图标颜色</label>
      <div class="col-md-9">
            <?php echo color_select('setting[color]', $setting['color']);?>
      </div>
    </div>
