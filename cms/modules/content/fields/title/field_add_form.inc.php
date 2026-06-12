
	<div class="form-group">
      <label class="col-md-2 control-label">控件宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="" class="form-control"></label>
            <span class="help-block"><?php echo L('[整数]表示固定宽度；[整数%]表示百分比')?></span>
      </div>
    </div>
	<div class="form-group"<?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {echo ' style="display: none;"';}?>>
      <label class="col-md-2 control-label">自动转拼音</label>
      <div class="col-md-9">
            <div class="mt-radio-inline">
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pinyin]" value="1">是 <span></span></label>
                <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[pinyin]" value="0" checked> 否 <span></span></label>
            </div>
            <span class="help-block"><?php echo L('如果有prefix字段将自动转拼音')?></span>
      </div>
    </div>
	<div class="form-group"<?php if(!$this->input->get('modelid') || $this->input->get('modelid')==-1 || $this->input->get('modelid')==-2) {echo ' style="display: none;"';}?>>
      <label class="col-md-2 control-label">拼音长度限制</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[length]" value="" class="form-control"></label>
            <span class="help-block"><?php echo L('默认20，超出转换拼音首字母')?></span>
      </div>
    </div>

