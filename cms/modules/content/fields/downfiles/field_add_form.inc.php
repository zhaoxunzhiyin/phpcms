
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
    <div class="form-group">
      <label class="col-md-2 control-label">滚动显示</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[scroller]" value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">当文件文件上传太多时，已上传文件列表可以以固定高度来下来滚动显示</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">显示方式</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[grid]" value="1" data-on-text="卡片模式" data-off-text="传统模式" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">上传后的文件列表采用卡片式显示，适用于图片类文件显示方式</span>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_number">
      <label class="col-md-2 control-label">上传数量</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_number]" id="upload_number" value="" class="form-control"></label>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_allowext">
      <label class="col-md-2 control-label">扩展名</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_allowext]" id="upload_allowext" value="" size="40" class="form-control"></label>
            <span class="help-block">格式：jpg|gif|png|exe|html|php|rar|zip</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">显示浏览附件</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[isselectimage]" checked value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">允许用户选取自己已经上传的附件</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">附件下载方式</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadlink]" value="0" type="radio">链接到真实软件地址 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadlink]" value="1" checked="checked" type="radio"> 链接到跳转页面 <span></span></label>
        </div>
	</div></div>	
	<div class="form-group">
      <label class="col-md-2 control-label">文件下载方式</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadtype]" value="0" type="radio">链接文件地址 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadtype]" value="1" checked="checked" type="radio">通过PHP读取<span></span></label>
        </div>
	</div></div>
    <?php echo attachment(array());?>
<script type="text/javascript">
$(function() {
    handleBootstrapSwitch();
});
</script>
