
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">表单附加属性</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[bformattribute]" value="get_wxurl('myform','title','keywords','content')" class="form-control"></label>
            <span class="help-block">javascript事件：get_wxurl('表单名称','标题字段名称','关键词字段名称','内容字段名称')，内容必须是编辑器。</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">是否保存远程图片</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="1" checked>是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="0"> 否 <span></span></label>
        </div></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">图片水印</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1">是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0" checked> 否 <span></span></label>
        </div></label>
        <span class="help-block">上传的图片会加上水印图</span>
      </div>
    </div>
    <?php echo attachment(array());?>
