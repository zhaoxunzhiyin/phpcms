<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member_model&a=add" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="80"><?php echo L('model_name')?></td> 
            <td><label><input type="text" name="info[name]" class="input-text" id="name" size="30" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','tablename',12);"></label></td>
        </tr>
        <tr>
            <td><?php echo L('table_name')?></td>
            <td>
            <?php echo $this->db->db_tablepre?>member_<label><input type="text" name="info[tablename]" value="" class="input-text" id="tablename" size="16"></label>
            </td>
        </tr>
        <tr>
            <td><?php echo L('model_description')?></td>
            <td>
            <label><input type="text" name="info[description]" value="" class="input-text" id="description" size="50"></label>
            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>