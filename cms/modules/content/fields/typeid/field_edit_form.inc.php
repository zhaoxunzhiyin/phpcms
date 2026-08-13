<?php defined('IN_CMS') or exit('No permission resources.');?>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label>
	  <input type="hidden" name="setting[minnumber]" value="<?php echo $setting['minnumber'];?>">
	  <input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
            <span class="help-block"><?php echo L('正整数 最大长度 5')?></span>
      </div>
    </div>
