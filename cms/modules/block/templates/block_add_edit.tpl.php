<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form action="?m=block&c=block_admin&a=<?php echo ROUTE_A?>&pos=<?php echo $this->input->get('pos')?>&id=<?php if(isset($id) && !empty($id))echo $id;?>" method="post" id="myform">
<div>
<fieldset>
    <legend><?php echo L('block_configuration')?></legend>
    <table width="100%"  class="table_form">
    <tr>
    <th width="80"><?php echo L('name')?>：</th>
    <td class="y-bg"><input type="text" name="name" id="name" size="30" value="<?php echo isset($data['name']) ?  $data['name'] : '';?>" /></td>
      </tr>
    <tr>
    <th width="80"><?php echo L('display_position')?>：</th>
    <td class="y-bg"> <?php echo isset($data['pos']) ?  $data['pos'] : $this->input->get('pos');?></td>
      </tr>
      <tr>
    <th width="80"><?php echo L('type')?>：</th>
    <td class="y-bg"><?php echo form::radio(array('1'=>L('code'), '2'=>L('table_style')), (isset($data['type']) ? $data['type'] : 1), 'name="type"'.(ROUTE_A=='edit' ? ' disabled = "disabled"' : ''))?></td>
      </tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('permission_configuration')?></legend>
    <table width="100%"  class="table_form">
    <tr>
    <th width="80"><?php echo L('role')?>：</th>
    <td class="y-bg"><?php echo form::checkbox($administrator, (isset($priv_list) ? implode(',', $priv_list) : ''), 'name="priv[]"')?></td>
      </tr>
</table>
</fieldset>
</div>
</div>
</form>
</body>
</html>