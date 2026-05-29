<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="?m=member&c=member_model&a=move" method="post" id="myform">
<input type="hidden" name="from_modelid" value="<?php echo $this->input->get('modelid')?>">
<fieldset>
    <legend><?php echo L('move').L('model_member')?></legend>
    <div class="bk10"></div>
    <div class="explain-col">
        <?php echo L('move_member_model_alert')?>
    </div>
    <div class="bk10"></div>
    <table width="100%" class="table_form">
        <tr>
            <td width="120"><?php echo L('from_model_name')?></td> 
            <td>
                <?php echo $modellist[$this->input->get('modelid')];?>

            </td>
        </tr>
        <tr>
            <td width="120"><?php echo L('to_model_name')?></td> 
            <td>
                <?php echo form::select($modellist, 0, 'id="to_modelid" name="to_modelid"', L('please_select'))?>
            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>