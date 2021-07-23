<table cellpadding="2" cellspacing="1" bgcolor="#ffffff">
	<tr> 
      <td><strong>时间格式：</strong></td>
      <td>
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="date" checked>日期（<?php echo date('Y-m-d');?>）<span></span></label><br />
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="datetime_a">日期+12小时制时间（<?php echo date('Y-m-d h:i:s');?>）<span></span></label><br />
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="datetime">日期+24小时制时间（<?php echo date('Y-m-d H:i:s');?>）<span></span></label><br />
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[fieldtype]" value="int">整数 显示格式：
	  <select name="setting[format]">
	  <option value="Y-m-d Ah:i:s">12小时制:<?php echo date('Y-m-d h:i:s');?></option>
	  <option value="Y-m-d H:i:s">24小时制:<?php echo date('Y-m-d H:i:s');?></option>
	  <option value="Y-m-d H:i"><?php echo date('Y-m-d H:i');?></option>
	  <option value="Y-m-d"><?php echo date('Y-m-d');?></option>
	  <option value="m-d"><?php echo date('m-d');?></option>
	  </select><span></span></label>
        </div>
	  </td>
    </tr>
	<tr> 
      <td><strong>默认值：</strong></td>
      <td>
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[defaulttype]" value="0" checked/>无<span></span></label>
        </div>
	 </td>
    </tr>
</table>