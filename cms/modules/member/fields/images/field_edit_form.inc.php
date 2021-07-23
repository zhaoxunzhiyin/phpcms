<?php defined('IN_CMS') or exit('No permission resources.');?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td width="120">允许上传的图片类型</td>
      <td><input type="text" name="setting[upload_allowext]" value="<?php echo $setting['upload_allowext'];?>" size="40" class="input-text"></td>
    </tr>
	<tr> 
      <td>是否从已上传中选择</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[isselectimage]" value="1" <?php if($setting['isselectimage']) echo 'checked';?>>是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[isselectimage]" value="0" <?php if(!$setting['isselectimage']) echo 'checked';?>> 否 <span></span></label>
        </div></td>
    </tr>
	<tr> 
      <td>表单显示模式</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[show_type]" value="1" <?php if($setting['show_type']) echo 'checked';?>/> 图片模式 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio"  name="setting[show_type]" value="0" <?php if(!$setting['show_type']) echo 'checked';?>/> 文本框模式 <span></span></label>
        </div></td>
	<tr> 
      <td>允许同时上传的个数</td>
      <td><input type="text" name="setting[upload_number]" value="<?php echo $setting['upload_number'];?>" size=3 class="input-text"></td>
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
</table>