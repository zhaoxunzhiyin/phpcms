<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member_model&a=edit" method="post" id="myform">
<input type="hidden" name="info[modelid]" value="<?php echo $this->input->get('modelid')?>">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="100"><?php echo L('model_name')?></td> 
            <td><input type="text" name="info[name]" class="input-text" id="name" size="30" value="<?php echo $modelinfo['name']?>"></input></td>
        </tr>
        <tr>
            <td><?php echo L('table_name')?></td>
            <td>
            <?php echo $this->db->db_tablepre.$modelinfo['tablename']?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('model_description')?></td>
            <td>
            <input type="text" name="info[description]" value="<?php echo $modelinfo['description']?>" class="input-text" id="description" size="30"></input>
            </td>
        </tr>
        <tr>
            <td><?php echo L('deny_model')?></td>
            <td><div class="mt-checkbox-inline">
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" value="1" name="info[disabled]" <?php if($modelinfo['disabled']) {?>checked<?php }?>> <span></span></label>
        </div>
            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>