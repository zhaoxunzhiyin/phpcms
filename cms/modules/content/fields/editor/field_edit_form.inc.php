<?php defined('IN_CMS') or exit('No permission resources.');?>
    <div class="form-group">
      <label class="col-md-2 control-label">编辑器默认宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">编辑器默认高度</label>
        <div class="col-md-9">
            <label><input type="text" name="setting[height]" value="<?php echo $setting['height'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">编辑器样式</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="basic" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='basic') echo 'checked';?>>简洁型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="standard" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='standard') echo 'checked';?>> 标准型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="full" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='full') echo 'checked';?>> 完整型 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="modetool" onclick="$('#bjqms').show()" <?php if($setting['toolbar']=='modetool') echo 'checked';?>> 自定义 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group" id="bjqms"<?php if($setting['toolbar']!='modetool') echo ' style="display:none;"';?>>
      <label class="col-md-2 control-label">工具栏</label>
        <div class="col-md-9">
            <textarea name="setting[toolvalue]" id="toolvalue" style="height:90px;" class="form-control"><?php echo $setting['toolvalue'];?></textarea>
            <span class="help-block"><?php if (SYS_EDITOR) {?>必须严格按照CKEditor工具栏格式：'Maximize', 'Source', '-', 'Undo', 'Redo'<?php } else {?>必须严格按照Ueditor工具栏格式：'Fullscreen', 'Source', '|', 'Undo', 'Redo'<?php }?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
        <div class="col-md-9">
            <textarea name="setting[defaultvalue]" id="defaultvalue" style="height:90px;" class="form-control"><?php echo $setting['defaultvalue'];?></textarea>
        </div>
    </div>
    <div class="form-group"<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">是否启用关联链接</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="1" <?php if($setting['enablekeylink']) echo 'checked';?>>是 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablekeylink]" value="0" <?php if(!$setting['enablekeylink']) echo 'checked';?>> 否 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group"<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">替换次数</label>
        <div class="col-md-9">
            <label><input type="text" name="setting[replacenum]" value="<?php echo $setting['replacenum'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('（留空则为替换全部）')?></span>
        </div>
    </div>
    <div class="form-group"<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>> 
      <label class="col-md-2 control-label">关联链接方式</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="1" <?php if($setting['link_mode']) echo 'checked';?>> 关键字链接 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[link_mode]" value="0" <?php if(!$setting['link_mode']) echo 'checked';?>> 网址链接 <span></span></label>
            </div>
        </div>
    </div>
    <div class="form-group"> 
      <label class="col-md-2 control-label">底部工具栏</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_bottom_boot]" value="1" onclick="$('#sdmrx').show()" <?php if($setting['show_bottom_boot']) echo 'checked';?>> 开启 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_bottom_boot]" value="0" onclick="$('#sdmrx').hide()" <?php if(!$setting['show_bottom_boot']) echo 'checked';?>> 关闭 <span></span></label>
            </div>
            <span class="help-block">编辑器底部工具栏，有提取描述、提取缩略图、下载远程图等控制按钮</span>
        </div>
    </div>
    <div class="form-group" id="sdmrx" <?php if(!$setting['show_bottom_boot']) echo 'style="display:none"';?>>
        <label class="col-md-1 control-label">&nbsp;&nbsp;</label>
        <div class="col-md-9">
            <div class="form-group"<?php if(!$modelid) {echo ' style="display: none;"';}?>>
                <label class="col-md-2 control-label">提取描述</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_1]" value="1" <?php if($setting['tool_select_1']) echo 'checked';?> data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                </div>
            </div>
            <div class="form-group"<?php if(!$modelid) {echo ' style="display: none;"';}?>>
                <label class="col-md-2 control-label">提取缩略图</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_2]" value="1" <?php if($setting['tool_select_2']) echo 'checked';?> data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">下载远程图</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_3]" value="1" <?php if($setting['tool_select_3']) echo 'checked';?> data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-2 control-label">去除站外链接</label>
                <div class="col-md-9">
                    <input type="checkbox" name="setting[tool_select_4]" value="1" <?php if($setting['tool_select_4']) echo 'checked';?> data-on-text="默认选中" data-off-text="默认不选" data-on-color="success" data-off-color="danger" class="make-switch" data-size="small">                             
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group"<?php if (!SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">编辑器颜色</label>
        <div class="col-md-9">
            <?php echo color_select('setting[color]', $setting['color']);?>
        </div>
    </div>
    <div class="form-group"> 
      <label class="col-md-2 control-label">编辑器主题</label>
        <div class="col-md-9">
            <?php if (!SYS_EDITOR) {?>
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="default" <?php if(!$setting['theme'] || $setting['theme']=='default' || !file_exists(CMS_PATH.'statics/js/ueditor/themes/'.$setting['theme'].'/')) echo 'checked';?>> 默认 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="notadd" <?php if($setting['theme']=='notadd') echo 'checked';?>> 主题1 <span></span></label>
            </div>
            <?php } else {
            $themefile = dr_dir_map(CMS_PATH.'statics/js/ckeditor/skins/', 1);?>
            <label><select class="form-control" name="setting[theme]">
                <option value="" <?php if(!$setting['theme']) echo 'selected';?>> 默认 </option>
                <?php foreach($themefile as $t) {?>
                <option<?php if ($t==$setting['theme']) {?> selected=""<?php }?> value="<?php echo $t;?>"><?php echo $t;?></option>
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
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[language]" value="zh-cn" <?php if($setting['language']=='zh-cn' || !file_exists(CMS_PATH.'statics/js/ueditor/lang/'.$setting['language'].'/'.$setting['language'].'.js')) echo 'checked';?>> 中文 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[language]" value="en" <?php if($setting['language']=='en') echo 'checked';?>> 英文 <span></span></label>
            </div>
            <?php } else {
            $languagefile = dr_file_map(CMS_PATH.'statics/js/ckeditor/lang/', 1);?>
            <label><select class="form-control" name="setting[language]">
                <option value="" <?php if(!$setting['language']) echo 'selected';?>> 默认 </option>
                <?php foreach($languagefile as $t) {
                if (strpos($t, '.js') !== false) {?>
                <option<?php if (str_replace('.js', '', $t)==$setting['language']) {?> selected=""<?php }?> value="<?php echo str_replace('.js', '', $t);?>"><?php echo str_replace('.js', '', $t);?></option>
                <?php }}?>
            </select></label>
            <?php }?>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
        <label class="col-md-2 control-label">固定编辑器图标栏</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="1" <?php if($setting['autofloat']) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="0" <?php if(!$setting['autofloat']) echo 'checked';?>> 否 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">将div标签转换为p标签</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="1" <?php if($setting['div2p']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="0" <?php if(!$setting['div2p']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">锚点控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[anchorduiqi]" value="1" <?php if($setting['anchorduiqi']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[anchorduiqi]" value="0" <?php if(!$setting['anchorduiqi']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到锚点控件时，开启状态就直接弹出编辑锚点窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">图片控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[imageduiqi]" value="1" <?php if($setting['imageduiqi']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[imageduiqi]" value="0" <?php if(!$setting['imageduiqi']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到图片控件时，开启状态就直接弹出编辑图片窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">视频控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[videoduiqi]" value="1" <?php if($setting['videoduiqi']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[videoduiqi]" value="0" <?php if(!$setting['videoduiqi']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到视频控件时，开启状态就直接弹出编辑视频窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">音乐控件显示对齐快捷操作</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[musicduiqi]" value="1" <?php if($setting['musicduiqi']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[musicduiqi]" value="0" <?php if(!$setting['musicduiqi']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
            <span class="help-block">鼠标移动到音乐控件时，开启状态就直接弹出编辑音乐窗口，关闭状态将显示浮动快捷框</span>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">自动伸长高度</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="1" <?php if($setting['autoheight']) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="0" <?php if(!$setting['autoheight']) echo 'checked';?>> 否 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group"<?php if (SYS_EDITOR) {?> style="display: none;"<?php }?>> 
      <label class="col-md-2 control-label">回车换行符号</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enter]" value="1" <?php if($setting['enter']) echo 'checked';?>> br标签 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enter]" value="0" <?php if(!$setting['enter']) echo 'checked';?>> p标签 <span></span></label>
        </div>
            <span class="help-block"><?php echo L('选择回车换行的符号，默认是p标签换行')?></span>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">下载远程图片</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="1" <?php if($setting['enablesaveimage']) echo 'checked';?>> 自动 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="0" <?php if(!$setting['enablesaveimage']) echo 'checked';?>> 手动 <span></span></label>
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
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1" <?php if($setting['watermark']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0" <?php if(!$setting['watermark']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
        <span class="help-block">上传的图片会加上水印图</span>
        </div>
    </div>
    <?php }?>
    <?php echo attachment($setting);?>
    <div class="form-group">
      <label class="col-md-2 control-label">是否允许上传附件</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowupload]" value="1" <?php if($setting['allowupload']) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[allowupload]" value="0" <?php if(!$setting['allowupload']) echo 'checked';?>> 否 <span></span></label>
        </div>
        </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">上传数量</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_number]" value="<?php echo $setting['upload_number'];?>" class="form-control"></label>
      </div>
    </div>
    <div class="form-group">
      <label class="col-md-2 control-label">文件大小</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[upload_maxsize]" value="<?php echo $setting['upload_maxsize'];?>" class="form-control"></label>
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
    <div class="form-group">
      <label class="col-md-2 control-label">本地图片自动上传</label>
        <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="1" <?php if($setting['local_img']) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="0" <?php if(!$setting['local_img']) echo 'checked';?>> 否 <span></span></label>
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
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="1" <?php if($setting['local_watermark']) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="0" <?php if(!$setting['local_watermark']) echo 'checked';?>> 关闭 <span></span></label>
        </div>
        </div>
    </div>
    <?php }?>
    <?php echo local_attachment($setting);?>
    <?php if(!$modelid || $modelid==-1 || $modelid==-2) {?>
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
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="1" <?php if($setting['disabled_page']) echo 'checked';?>> 禁止 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[disabled_page]" value="0" <?php if(!$setting['disabled_page']) echo 'checked';?>> 显示 <span></span></label>
        </div>
        </div>
    </div>
    <?php }?>
<script type="text/javascript">
$(function() {
    handleBootstrapSwitch();
});
</script>