<?php defined('IN_CMS') or exit('No permission resources.');?>

    <?php $sitemodel_field = $this->db->select(array('modelid'=>$this->input->get('modelid')), '*', '', 'listorder ASC,fieldid ASC');
    $_field = [
        '<option value=""> -- </option>'
    ];
    if ($sitemodel_field) {
        foreach ($sitemodel_field as $t) {
            if ($t['formtype'] == 'text' || $t['formtype'] == 'title' || $t['formtype'] == 'keyword') {
                $st = $setting['fieldname'] == $t['field'] ? 'selected' : '';
                $_field[] = '<option '.$st.' value="'.$t['field'].'">'.$t['name'].'</option>';
            }
        }
        $_field = implode('', array_unique($_field));
    }?>
	<div class="form-group">
      <label class="col-md-2 control-label">附加到指定字段</label>
      <div class="col-md-9">
            <label><select class="form-control" name="setting[fieldname]"><?php echo $_field;?></select></label>
            <span class="help-block">对文本类型字段有效,会实时变动颜色</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">控件宽度</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[width]" value="<?php echo $setting['width'];?>" class="form-control"></label>
            <span class="help-block">[整数]表示固定宽度；[整数%]表示百分比,会实时变动颜色</span>
      </div>
    </div>
	<div class="form-group">
      <label class="col-md-2 control-label">默认值</label>
      <div class="col-md-9">
            <label><input type="text" name="setting[defaultvalue]" value="<?php echo $setting['defaultvalue'];?>" class="form-control"></label>
      </div>
    </div>
