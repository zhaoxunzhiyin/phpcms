<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">表单</label>
      <div class="col-md-9">
            <textarea name="setting[formtext]" id="options" style="height:100px;" class="form-control"><?php echo $setting['formtext'];?></textarea>
            <span class="help-block">例如：&lt;input type='text' name='info[voteid]' id='voteid' value='{FIELD_VALUE}'&gt;</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">字段类型</label>
      <div class="col-md-9">
	  <select name="setting[fieldtype]" onchange="javascript:fieldtype_setting(this.value);">
	  <option value="varchar" <?php if($setting['fieldtype']=='varchar') echo 'selected';?>>字符 VARCHAR</option>
	  <option value="tinyint" <?php if($setting['fieldtype']=='tinyint') echo 'selected';?>>整数 TINYINT(3)</option>
	  <option value="smallint" <?php if($setting['fieldtype']=='smallint') echo 'selected';?>>整数 SMALLINT(5)</option>
	  <option value="mediumint" <?php if($setting['fieldtype']=='mediumint') echo 'selected';?>>整数 MEDIUMINT(8)</option>
	  <option value="int" <?php if($setting['fieldtype']=='int') echo 'selected';?>>整数 INT(10)</option>
	  </select> <span id="minnumber" style="display:none"><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[minnumber]" value="1" <?php if($setting['minnumber']==1) echo 'checked';?>/> <font color='red'>正整数</font> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[minnumber]" value="-1" <?php if($setting['minnumber']==-1) echo 'checked';?>/> 整数</span><span></span></label></div>
	  </div>
    </div>
