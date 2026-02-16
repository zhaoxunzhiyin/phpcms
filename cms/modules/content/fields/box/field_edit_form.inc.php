<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">选项列表</label>
      <div class="col-md-9">
            <textarea name="setting[options]" id="options" style="height:100px;" class="form-control"><?php echo $setting['options'];?></textarea>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">选项类型</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[boxtype]" value="radio" <?php if($setting['boxtype']=='radio') echo 'checked';?> onclick="$('#setcols').show();"/> 单选按钮 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[boxtype]" value="checkbox" <?php if($setting['boxtype']=='checkbox') echo 'checked';?> onclick="$('#setcols').show();"/> 复选框 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[boxtype]" value="select" <?php if($setting['boxtype']=='select') echo 'checked';?> onclick="$('#setcols').hide();" /> 下拉框 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[boxtype]" value="multiple" <?php if($setting['boxtype']=='multiple') echo 'checked';?> onclick="$('#setcols').hide();" /> 多选列表框 <span></span></label>
        </div>
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
	  </select> <span id="minnumber"<?php if($setting['fieldtype']=='varchar') echo ' style="display:none"';?>><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[minnumber]" value="1" <?php if($setting['minnumber']==1) echo 'checked';?>/> <font color='red'>正整数</font> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[minnumber]" value="-1" <?php if($setting['minnumber']==-1) echo 'checked';?>/> 整数 <span></span></label>
        </div></span>
      </div>
    </div>
	<div class="form-group" id="setcols"<?php if($setting['boxtype']=='select' || $setting['boxtype']=='multiple') echo ' style="display:none"';?>> 
      <label class="col-md-2 control-label">每列宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('px')?></span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" class="form-control" value="<?php echo $setting['defaultvalue'];?>"></label>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">输出格式</label>
      <div class="col-md-9">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[outputtype]" value="1" <?php if($setting['outputtype']) echo 'checked';?> /> 输出选项值 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[outputtype]" value="0" <?php if(!$setting['outputtype']) echo 'checked';?> /> 输出选项名称 <span></span></label>
        </div>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">是否作为筛选字段</label>
      <div class="col-md-9">
	  <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[filtertype]" value="1" <?php if($setting['filtertype']) echo 'checked';?> /> 是 <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[filtertype]" value="0" <?php if(!$setting['filtertype']) echo 'checked';?>/> 否 <span></span></label>
        </div>
      </div>
    </div>

<SCRIPT LANGUAGE="JavaScript">
<!--
	function fieldtype_setting(obj) {
	if(obj!='varchar') {
		$('#minnumber').css('display','');
	} else {
		$('#minnumber').css('display','none');
	}
}
//-->
</SCRIPT>