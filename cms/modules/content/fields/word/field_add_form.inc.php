
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
