<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td width="120">允许上传的文件类型</td>
      <td><input type="text" name="setting[upload_allowext]" value="gif|jpg|jpeg|png|bmp" size="40" class="input-text"></td>
    </tr>
	<tr> 
      <td>是否从已上传中选择</td>
      <td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[isselectimage]" value="1">是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[isselectimage]" value="0" checked> 否 <span></span></label>
        </div></td>
    </tr>
	<tr> 
      <td>允许同时上传的个数</td>
      <td><input type="text" name="setting[upload_number]" value="10" size=3 class="input-text"></td>
    </tr>
	<tr>
	<td>附件下载方式</td>
	<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadlink]" value="0" type="radio">链接到真实软件地址 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadlink]" value="1" checked="checked" type="radio"> 链接到跳转页面 <span></span></label>
        </div>
	</td></tr>	
	<tr>
	<td>文件下载方式</td>
	<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadtype]" value="0" type="radio">链接文件地址 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="setting[downloadtype]" value="1" checked="checked" type="radio">通过PHP读取<span></span></label>
        </div>
	</td></tr>
    <tr> 
      <td>附件存储策略：</td>
      <td><select class="form-control" name="setting[attachment]">
        <option value="0" selected>本地存储</option>
        <?php foreach ($remote as $i=>$t) {?>
        <option value="<?php echo $i;?>"<?php echo ($i == $attachment ? ' selected' : '');?>> <?php echo L($t['name']);?> </option>
        <?php }?>
      </select> 远程附件存储建议设置小文件存储，推荐10MB内，大文件会导致数据传输失败</td>
    </tr>
    <tr> 
      <td>图片压缩大小：</td>
      <td><input type="text" name="setting[image_reduce]" value="" size="20" class="input-text"> 填写图片宽度，例如1000，表示图片大于1000px时进行压缩图片</td>
    </tr>
</table>