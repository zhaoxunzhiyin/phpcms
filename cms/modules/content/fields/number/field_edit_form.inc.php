<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">取值范围</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[minnumber]" value="<?php echo $setting['minnumber'];?>" class="form-control"></label> - <label><input type="text" name="setting[maxnumber]" value="<?php echo $setting['maxnumber'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">小数位数</label>
      <div class="col-md-9">
            <label>
	  <select name="setting[decimaldigits]">
	  <option value="-1" <?php if($setting['decimaldigits']==-1) echo 'selected';?>)>自动</option>
	  <option value="0" <?php if($setting['decimaldigits']==0) echo 'selected';?>>0</option>
	  <option value="1" <?php if($setting['decimaldigits']==1) echo 'selected';?>>1</option>
	  <option value="2" <?php if($setting['decimaldigits']==2) echo 'selected';?>>2</option>
	  <option value="3" <?php if($setting['decimaldigits']==3) echo 'selected';?>>3</option>
	  <option value="4" <?php if($setting['decimaldigits']==4) echo 'selected';?>>4</option>
	  <option value="5" <?php if($setting['decimaldigits']==5) echo 'selected';?>>5</option>
	  </select>
    </label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">控件宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
	  <label class="col-md-2 control-label">是否作为区间字段</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[rangetype]" value="1" <?php if($setting['rangetype']) echo 'checked';?> /> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[rangetype]" value="0" <?php if(!$setting['rangetype']) echo 'checked';?> /> 否 <span></span></label>
          <span></span></label>
        </div>
            <span class="help-block">注：区间字段可以通过filters('字段名称','模型id','自定义数组')调用</span>
	  </label>
      </div>
	</div>		
