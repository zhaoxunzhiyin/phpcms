<?php defined('IN_CMS') or exit('No permission resources.');?>

	<div class="form-group">
      <label class="col-md-2 control-label">默认选择的会员组</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[groupids]" value="<?php echo $setting['groupids'];?>" class="form-control"> 填写会员组ID，多个用 “|” 分开</label>
      </div>
    </div>
