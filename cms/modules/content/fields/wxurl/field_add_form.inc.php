<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td width="150">文本域宽度</td>
      <td><input type="text" name="setting[width]" value="400" size="10" class="input-text">px</td>
    </tr>
	<tr> 
      <td>默认值</td>
      <td><input type="text" name="setting[defaultvalue]" value="" size="40" class="input-text"></td>
    </tr>
	<tr> 
      <td>表单附加属性<br />可以通过此处加入javascript事件</td>
      <td><input type="text" name="setting[bformattribute]" value="get_wxurl('title','keywords','content')" size="40" class="input-text"><br />javascript事件：get_wxurl('标题字段名称','关键词字段名称','内容字段名称')，内容必须是编辑器。</td>
    </tr>
    <tr> 
      <td>是否保存远程图片：</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="1" checked>是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[enablesaveimage]" value="0"> 否 <span></span></label>
        </div></td>
    </tr>
	<tr> 
      <td>是否在图片上添加水印</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="1">是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[watermark]" value="0" checked> 否 <span></span></label>
        </div></td>
    </tr>
    <tr> 
      <td>附件存储策略：</td>
      <td><select class="form-control" name="setting[attachment]">
        <option value="0" selected>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>图片压缩大小：</td>
      <td><input type="text" name="setting[image_reduce]" value="" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
</table>