
    <div class="form-group">
        <label class="col-md-2 control-label">控件宽度</label>
        <div class="col-md-9">
            <label><input type="text" class="form-control" name="setting[width]" value=""></label>
            <span class="help-block">[整数]表示固定宽度；[整数%]表示百分比</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">图标样式</label>
        <div class="col-md-9">
            <label><input type="text" class="form-control" name="setting[icon]" value=""></label>
            <span class="help-block">例如: fa fa-user</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">图标名称</label>
        <div class="col-md-9">
            <label><input type="text" class="form-control" name="setting[icon_name]" value=""></label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">按钮颜色</label>
        <div class="col-md-9">
            <?php echo color_select('setting[color]', '');?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label">点击JS函数</label>
        <div class="col-md-9">
            <input type="text" class="form-control Large" name="setting[func]" value="">
            <span class="help-block">单击按钮时执行的js函数</span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="" class="form-control"></label>
      </div>
    </div>
