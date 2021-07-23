<?php defined('IN_CMS') or exit('No permission resources.');$siteinfo = getcache('sitelist', 'commons');$config = string2array($siteinfo[$this->siteid]['setting']);?>
<link href="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>jquery-minicolors/jquery.minicolors.min.js" type="text/javascript"></script>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td width="140">编辑器样式：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="basic" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='basic') echo 'checked';?>>简洁型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="standard" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='standard') echo 'checked';?>> 标准型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="full" onclick="$('#bjqms').hide()" <?php if($setting['toolbar']=='full') echo 'checked';?>> 完整型 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[toolbar]" value="modetool" onclick="$('#bjqms').show()" <?php if($setting['toolbar']=='modetool') echo 'checked';?>> 自定义 <span></span></label>
        </div></td>
    </tr>
	<tr id="bjqms"<?php if($setting['toolbar']!='modetool') echo ' style="display:none;"';?>>
      <td>工具栏：</td>
      <td><textarea name="setting[toolvalue]" rows="2" cols="20" id="toolvalue" style="height:100px;width:250px;"><?php echo $setting['toolvalue'];?></textarea><br><?php if (pc_base::load_config('system', 'editor')) {?>必须严格按照CKEditor工具栏格式：'Maximize', 'Source', '-', 'Undo', 'Redo'<?php } else {?>必须严格按照Ueditor工具栏格式：'Fullscreen', 'Source', '|', 'Undo', 'Redo'<?php }?></td>
    </tr>
	<tr> 
      <td>默认值：</td>
      <td><textarea name="setting[defaultvalue]" rows="2" cols="20" id="defaultvalue" style="height:100px;width:250px;"><?php echo $setting['defaultvalue'];?></textarea></td>
    </tr>
	<tr<?php if (!pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>编辑器颜色：</td>
      <td><input type="text" id="style_color" name="setting[color]" value="<?php echo $setting['color'];?>" size="6" autocomplete="off" class="input-text" style="height: 30px;"><script type="text/javascript">
      $(function(){
          $("#style_color").minicolors({
              control: $("#style_color").attr("data-control") || "hue",
              defaultValue: $("#style_color").attr("data-defaultValue") || "",
              inline: "true" === $("#style_color").attr("data-inline"),
              letterCase: $("#style_color").attr("data-letterCase") || "lowercase",
              opacity: $("#style_color").attr("data-opacity"),
              position: $("#style_color").attr("data-position") || "bottom left",
              change: function(t, o) {
                  t && (o && (t += ", " + o), "object" == typeof console && console.log(t));
              },
              theme: "bootstrap"
          });
      });
      </script></td>
    </tr>
	<tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>编辑器样式：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="default" <?php if($setting['theme']=='default') echo 'checked';?>> 默认 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[theme]" value="notadd" <?php if($setting['theme']=='notadd') echo 'checked';?>> 样式1 <span></span></label>
        </div></td>
    </tr>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>固定编辑器图标栏：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="1" <?php if($setting['autofloat']==1) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autofloat]" value="0" <?php if($setting['autofloat']==0) echo 'checked';?>> 否 <span></span></label>
        </div></td>
    </tr>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>自动伸长高度：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="1" <?php if($setting['autoheight']==1) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[autoheight]" value="0" <?php if($setting['autoheight']==0) echo 'checked';?>> 否 <span></span></label>
        </div></td>
    </tr>
    <?php if ($config['ueditor']) {?>
    <tr> 
      <td>图片水印：</td>
      <td>系统强制开启水印</td>
    </tr>
    <?php } else {?>
    <tr> 
      <td>图片水印：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1" <?php if($setting['watermark']==1) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0" <?php if($setting['watermark']==0) echo 'checked';?>> 关闭 <span></span></label>
        </div></td>
    </tr>
    <?php }?>
    <tr<?php if (pc_base::load_config('system', 'editor')) {?> style="display: none;"<?php }?>> 
      <td>将div标签转换为p标签：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="1" <?php if($setting['div2p']==1) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[div2p]" value="0" <?php if($setting['div2p']==0) echo 'checked';?>> 关闭 <span></span></label>
        </div></td>
    </tr>
    <tr> 
      <td>附件存储策略：</td>
      <td><select class="form-control" name="setting[attachment]">
        <option value="0"<?php echo ($setting['attachment']=='0') ? ' selected' : ''?>>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"<?php echo ($i == $setting['attachment'] ? ' selected' : '');?>> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>图片压缩大小：</td>
      <td><input type="text" name="setting[image_reduce]" value="<?php echo $setting['image_reduce'];?>" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
	<tr> 
      <td>编辑器默认高度：</td>
      <td><input type="text" name="setting[height]" value="<?php echo $setting['height'];?>" size="4" class="input-text"> px</td>
    </tr>
    <tr> 
      <td>本地图片自动上传：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="1" <?php if($setting['local_img']==1) echo 'checked';?>> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_img]" value="0" <?php if($setting['local_img']==0) echo 'checked';?>> 否 <span></span></label>
        </div></td>
    </tr>
    <?php if ($config['ueditor']) {?>
    <tr> 
      <td>本地图片上传水印：</td>
      <td>系统强制开启水印</td>
    </tr>
    <?php } else {?>
    <tr> 
      <td>本地图片上传水印：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="1" <?php if($setting['local_watermark']==1) echo 'checked';?>> 开启 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[local_watermark]" value="0" <?php if($setting['local_watermark']==0) echo 'checked';?>> 关闭 <span></span></label>
        </div></td>
    </tr>
    <?php }?>
    <tr> 
      <td>本地附件存储策略：</td>
      <td><select class="form-control" name="setting[local_attachment]">
        <option value="0"<?php echo ($setting['local_attachment']=='0') ? ' selected' : ''?>>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"<?php echo ($i == $setting['local_attachment'] ? ' selected' : '');?>> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>本地图片压缩大小：</td>
      <td><input type="text" name="setting[local_image_reduce]" value="<?php echo $setting['local_image_reduce'];?>" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
</table>