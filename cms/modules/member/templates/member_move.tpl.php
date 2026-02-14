<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="?m=member&c=member&a=move" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="80"><?php echo L('username')?></td> 
            <td><div class="mt-checkbox-inline">
                <?php foreach($userarr as $v) {?>
                    <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="userid[]" value="<?php echo $v['userid']?>" checked /> <?php echo $v['username']?> <span></span></label>
                <?php }?>
        </div>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_group')?></td> 
            <td>
                <?php echo form::select($grouplist, $this->input->get('groupid'), 'id="groupid" name="groupid"', L('please_select'))?>
            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>