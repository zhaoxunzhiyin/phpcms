
	<div class="form-group" id="dr_row_upload_maxsize">
      <label class="col-md-2 control-label">文件大小</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_maxsize]" id="upload_maxsize" value="0" class="form-control"></label>
            <span class="help-block">单位MB</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">分段上传</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[chunk]" value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">当文件太大时可以采取分段上传，可以提升上传效率</span>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_allowext">
      <label class="col-md-2 control-label">扩展名</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_allowext]" id="upload_allowext" value="" size="40" class="form-control"></label>
            <span class="help-block">格式：jpg|gif|png|exe|html|php|rar|zip</span>
      </div>
    </div>
    <?php echo attachment(array());?>
	<div class="form-group">
      <label class="col-md-2 control-label">显示浏览附件</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[isselectimage]" checked value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">允许用户选取自己已经上传的附件</span>
      </div>
    </div>
<script type="text/javascript">
$(function() {
    handleBootstrapSwitch();
});
</script>
