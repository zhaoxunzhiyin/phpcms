
	<div class="form-group">
      <label class="col-md-2 control-label">宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="" class="form-control"> </label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">最大值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[maxnumber]" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">最小值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[minnumber]" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">步长值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[step]" id="step" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
	  <label class="col-md-2 control-label">显示模式</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show]" value="1"/> 按钮 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show]" value="0" checked />箭头<span></span></label>
        </div>
	  </div>
	</div>
    <div class="form-group">
      <label class="col-md-2 control-label">加按钮颜色</label>
      <div class="col-md-9">
            <?php echo color_select('setting[up]', '');?>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">减按钮颜色</label>
      <div class="col-md-9">
            <?php echo color_select('setting[down]', '');?>
      </div>
    </div>
