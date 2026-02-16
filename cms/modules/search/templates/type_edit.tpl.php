<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="?m=search&c=search_type&a=edit" method="post" id="myform">
<input type="hidden" name="typeid" value="<?php echo $typeid?>">
<div class="portlet light bordered">
    <div class="portlet-body form">
    <table width="100%"  class="table_form">
    <tr>
    <th width="120"><?php echo L('select_module_name')?>：</th>
    <td class="y-bg"><?php
    if($modelid && $typedir == 'yp') {
        $module = 'yp';
    } elseif($modelid && $typedir != 'yp') {
        $module = 'content';
    } else {
        $module = $module;
    }
    echo form::select($module_data,$module,'name="module" onchange="change_module(this.value)" disabled')?></td>
    <input name="module" type="hidden" value="<?php echo $module?>"><input name="typedir" type="hidden" value="<?php echo $typedir?>">
  </tr>

  <?php if($modelid && $typedir != 'yp') {?>
  <tr id="modelid_display">
    <th width="120"><?php echo L('select_model_name')?>：</th>
    <td class="y-bg"><?php echo form::select($model_data,$modelid,'name="info[modelid]"')?></td>
  </tr>
  <?php }?>

  <?php if($modelid && $typedir == 'yp') {?>
  <tr id="yp_modelid_display">
    <th width="120"><?php echo L('select_model_name')?>：</th>
    <td class="y-bg"><?php echo form::select($yp_model_data,$modelid,'name="info[yp_modelid]"')?></td>
  </tr>
  <?php }?>

  <tr>
    <th width="120"><?php echo L('type_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[name]" id="name" size="30" value="<?php echo $name?>"/></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><textarea name="info[description]" maxlength="255" class="form-group"><?php echo $description?></textarea></td>
  </tr>
</table>
</form>
</div>
</div>
</div>
</div>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
    function change_module(module) {
        redirect('?m=search&c=search_type&a=edit&typeid=<?php echo $typeid?>&module='+module+'&is_iframe=<?php echo $this->input->get('is_iframe')?>&pc_hash=<?php echo dr_get_csrf_token()?>');
    }
//-->
</SCRIPT>
</body>
</html>