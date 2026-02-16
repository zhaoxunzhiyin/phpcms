
    <?php echo linkage(array());?>
    <div class="form-group">
      <label class="col-md-2 control-label">路径分隔符</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[space]" value=" - " class="form-control"></label>
            <span class="help-block">显示完整路径时生效</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">强制选择最终项</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ck_child]" value="1"> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[ck_child]" value="0" checked> 关闭 <span></span></label>
          </div>
          <span class="help-block">开启后会强制要求用户选择最终一个选项，需要启用必须验证才会生效</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="" class="form-control"></label>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">是否作为筛选字段</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[filtertype]" value="1"> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[filtertype]" value="0" checked> 否 <span></span></label>
          </div>
      </div>
    </div>
