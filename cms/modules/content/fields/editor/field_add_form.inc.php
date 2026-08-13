    <div class="form-group">
      <label class="col-md-2 control-label">编辑器默认宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">编辑器默认高度</label>
        <div class="col-md-9">
            <label><input type="text" name="setting[height]" value="" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">编辑器样式</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="basic" onclick="$('#bjqms').hide()" checked> 简洁型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="standard" onclick="$('#bjqms').hide()"> 标准型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="full" onclick="$('#bjqms').hide()"> 完整型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="modetool" onclick="$('#bjqms').show()"> 自定义 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group" id="bjqms" style="display:none;">
      <label class="col-md-2 control-label">工具栏</label>
        <div class="col-md-9">
            <textarea name="setting[toolvalue]" id="toolvalue" style="height:90px;" class="form-control">'Bold', 'Italic', 'Underline'</textarea>
            <span class="help-block"><?php if (SYS_EDITOR) {?>必须严格按照CKEditor工具栏格式：'Maximize', 'Source', '-', 'Undo', 'Redo'<?php } else {?>必须严格按照Ueditor工具栏格式：'Fullscreen', 'Source', '|', 'Undo', 'Redo'<?php }?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
        <div class="col-md-9">
            <textarea name="setting[defaultvalue]" id="defaultvalue" style="height:90px;" class="form-control"></textarea>
        </div>
    </div>
    <div class="form-group"<?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">是否启用关联链接</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="1">是 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="0" checked> 否 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group"<?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">替换次数</label>
        <div class="col-md-9">
            <label><input type="text" name="setting[replacenum]" value="1" class="form-control"></label>
            <span class="help-block"><?php echo L('（留空则为替换全部）')?></span>
        </div>
    </div>
    <div class="form-group"<?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">关联链接方式</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="1" checked> 关键字链接 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="0"> 网址链接 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group"> 
      <label class="col-md-2 control-label">底部工具栏</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_bottom_boot]" value="1" onclick="$('#sdmrx').show()"> 开启 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_bottom_boot]" value="0" onclick="$('#sdmrx').hide()" checked> 关闭 <span></span></label>
            </div>
            <span class="help-block">编辑器底部工具栏，有提取描述、提取缩略图、下载远程图等控制按钮</span>
        </div>
    </div>
    <div class="form-group" id="sdmrx" style="display:none">
        <label class="col-md-1 control-label">&nbsp;&nbsp;</label>
        <div class="col-md-9">
            <div class="form-group"<?php if(!$this->input->get('modelid')) {echo ' style="display: none;"';}?>>
                <label class="col-md-2 control-label">提取描述</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_1]" value="1" data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                </div>
            </div>
            <div class="form-group"<?php if(!$this->input->get('modelid')) {echo ' style="display: none;"';}?>>
                <label class="col-md-2 control-label">提取缩略图</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_2]" value="1" data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">下载远程图</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_3]" value="1" data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">去除站外链接</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_4]" value="1" data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group"<?php if (!SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">编辑器颜色</label>
        <div class="col-md-9">
            <?php echo color_select('setting[color]', '');?>
        </div>
    </div>
    <div class="form-group"> 
      <label class="col-md-2 control-label">编辑器主题</label>
        <div class="col-md-9">
            <?php if (!SYS_EDITOR) {?>
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="default" checked> 默认 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="notadd"> 主题1 <span></span></label>
            </div>
            <?php } else {
            $themefile = dr_dir_map(CMS_PATH.'statics/js/ckeditor/skins/', 1);?>
            <label><select class="form-control" name="setting[theme]">
                <option value="" selected> 默认 </option>
                <?php foreach($themefile as $t) {?>
                <option value="<?php echo $t;?>"><?php echo $t;?></option>
                <?php }?>
            </select></label>
            <?php }?>
        </div>
    </div>
    <div class="form-group"> 
      <label class="col-md-2 control-label">编辑器语言</label>
        <div class="col-md-9">
            <?php if (!SYS_EDITOR) {?>
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[language]" value="zh-cn" checked> 中文 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[language]" value="en"> 英文 <span></span></label>
            </div>
            <?php } else {
            $languagefile = dr_file_map(CMS_PATH.'statics/js/ckeditor/lang/', 1);?>
            <label><select class="form-control" name="setting[language]">
                <option value="" selected> 默认 </option>
                <?php foreach($languagefile as $t) {
                if (strpos($t, '.js') !== false) {?>
                <option value="<?php echo str_replace('.js', '', $t);?>"><?php echo str_replace('.js', '', $t);?></option>
                <?php }}?>
            </select></label>
            <?php }?>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
        <label class="col-md-2 control-label">固定编辑器图标栏</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="1"> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="0" checked> 否 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">将div标签转换为p标签</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="0"> 关闭 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">锚点控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[anchorduiqi]" value="1"> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[anchorduiqi]" value="0" checked> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到锚点控件时，开启状态就直接弹出编辑锚点窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">图片控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[imageduiqi]" value="1"> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[imageduiqi]" value="0" checked> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到图片控件时，开启状态就直接弹出编辑图片窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">视频控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[videoduiqi]" value="1"> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[videoduiqi]" value="0" checked> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到视频控件时，开启状态就直接弹出编辑视频窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">音乐控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[musicduiqi]" value="1"> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[musicduiqi]" value="0" checked> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到音乐控件时，开启状态就直接弹出编辑音乐窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">自动伸长高度</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="1"> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="0" checked> 否 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">回车换行符号</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enter]" value="1"> br标签 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enter]" value="0" checked> p标签 <span></span></label>
        </div>
            <span class="help-block"><?php echo L('选择回车换行的符号，默认是p标签换行')?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">下载远程图片</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="1" checked> 自动 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="0"> 手动 <span></span></label>
        </div>
        <span class="help-block">自动模式下每一次编辑内容时都会下载图片；手动模式可以在编辑器下放工具栏中控制“是否下载”</span>
        </div>
    </div>
    <?php if (dr_site_value('ueditor', $this->siteid)) {?>
    <div class="form-group">
      <label class="col-md-2 control-label">图片水印</label>
        <div class="col-md-9">
            <div class="form-control-static">系统强制开启水印</div>
        </div>
    </div>
    <?php } else {?>
    <div class="form-group">
      <label class="col-md-2 control-label">图片水印</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0"> 关闭 <span></span></label>
        </div>
        <span class="help-block">上传的图片会加上水印图</span>
        </div>
    </div>
    <?php }?>
    <?php echo attachment(array());?>
    <div class="form-group">
      <label class="col-md-2 control-label">是否允许上传附件</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowupload]" value="1" checked> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowupload]" value="0"> 否 <span></span></label>
        </div>
        </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">显示浏览附件</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[isselectimage]" checked value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">允许用户选取自己已经上传的附件</span>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_number">
      <label class="col-md-2 control-label">上传数量</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_number]" id="upload_number" value="10" class="form-control"></label>
      </div>
    </div>
	<div class="form-group" id="dr_row_upload_maxsize">
      <label class="col-md-2 control-label">文件大小</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_maxsize]" id="upload_maxsize" value="0" class="form-control"></label>
            <span class="help-block">单位MB</span>
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
      <label class="col-md-2 control-label">分段上传</label>
      <div class="col-md-9">
        <input type="checkbox" name="setting[chunk]" value="1" data-on-text="已开启" data-off-text="已关闭" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
        <span class="help-block">当文件太大时可以采取分段上传，可以提升上传效率</span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">本地图片自动上传</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="1" checked> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="0" > 否 <span></span></label>
        </div>
        </div>
    </div>
    <?php if (dr_site_value('ueditor', $this->siteid)) {?>
    <div class="form-group">
      <label class="col-md-2 control-label">本地图片上传水印</label>
        <div class="col-md-9">
            <div class="form-control-static">系统强制开启水印</div>
        </div>
    </div>
    <?php } else {?>
    <div class="form-group">
      <label class="col-md-2 control-label">本地图片上传水印</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="1" checked> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="0" > 关闭 <span></span></label>
        </div>
        </div>
    </div>
    <?php }?>
    <?php echo local_attachment(array());?>
    <?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {?>
    <div class="form-group" style="display: none;">
      <label class="col-md-2 control-label">显示分页符与子标题</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="1" checked> 禁止<span></span></label>
        </div>
        </div>
    </div>
    <?php } else {?>
    <div class="form-group">
      <label class="col-md-2 control-label">显示分页符与子标题</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="1"> 禁止 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="0" checked> 显示 <span></span></label>
        </div>
        </div>
    </div>
    <?php }?>
<script type="text/javascript">
$(function() {
    handleBootstrapSwitch();
});
</script>