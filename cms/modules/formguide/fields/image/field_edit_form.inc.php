<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">表单显示模式</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_type]" value="1" <?php if($setting['show_type']) echo 'checked';?>/> 图片模式 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio"  name="setting[show_type]" value="0" <?php if(!$setting['show_type']) echo 'checked';?>/> 文本框模式 <span></span></label>
        </div></label>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_maxsize">
      <label class="col-md-2 control-label">文件大小</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_maxsize]" id="upload_maxsize" value="<?php echo $setting['upload_maxsize'];?>" class="form-control"></label>
            <span class="help-block">单位MB</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">分段上传</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[chunk]" <?php if($setting['chunk']) echo 'checked';?> value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">当文件太大时可以采取分段上传，可以提升上传效率</span>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_allowext">
      <label class="col-md-2 control-label">扩展名</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_allowext]" id="upload_allowext" value="<?php echo $setting['upload_allowext'];?>" size="40" class="form-control"></label>
            <span class="help-block">填写用于图片上传的扩展名格式，格式：jpg|gif|png|exe|html|php|rar|zip</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">图片水印</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1" <?php if($setting['watermark']) echo 'checked';?>>是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0" <?php if(!$setting['watermark']) echo 'checked';?>> 否 <span></span></label>
        </div></label>
        <span class="help-block">上传的图片会加上水印图</span>
      </div>
    </div>
    <?php echo attachment($setting);?>
	<div class="form-group">
      <label class="col-md-2 control-label">显示浏览附件</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[isselectimage]" <?php if($setting['isselectimage']) echo 'checked';?> value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">允许用户选取自己已经上传的附件</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">图像大小</label>
      <div class="col-md-9">
          <div class="input-inline input-medium">
              <label><div class="input-group">
                  <span class="input-group-addon">宽</span>
                  <input type="text" name="setting[images_width]" value="<?php echo $setting['images_width'];?>" class="form-control">
                  <span class="input-group-addon">px</span>
              </div></label>
              <label><div class="input-group">
                  <span class="input-group-addon">高</span>
                  <input type="text" name="setting[images_height]" value="<?php echo $setting['images_height'];?>" class="form-control">
                  <span class="input-group-addon">px</span>
              </div></label>
          </div>
      </div>
    </div>
<script type="text/javascript">
$(function() {
    handleBootstrapSwitch();
});
</script>
