<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member&a=add" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="80"><?php echo L('username')?></td> 
            <td><input type="text" name="info[username]"  class="input-text" id="username"></input></td>
        </tr>
        <tr>
            <td><?php echo L('password')?></td> 
            <td><input type="password" name="info[password]" class="input-text" id="password" value=""></input></td>
        </tr>
        <tr>
            <td><?php echo L('nickname')?></td> 
            <td><input type="text" name="info[nickname]" id="nickname" value="" class="input-text"></input></td>
        </tr>
        <tr>
            <td><?php echo L('email')?></td>
            <td>
            <input type="text" name="info[email]" value="" class="input-text" id="email" size="30"></input>
            </td>
        </tr>
        <tr>
            <td><?php echo L('mp')?></td>
            <td>
            <input type="text" name="info[mobile]" value="<?php echo $memberinfo['mobile']?>" class="input-text" id="mobile" size="15"></input>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_group')?></td>
            <td>
            <?php echo form::select($grouplist, '2', 'name="info[groupid]"', '');?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('point')?></td>
            <td>
            <input type="text" name="info[point]" value="0" class="input-text" id="point" size="10"></input>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_model')?></td>
            <td>
            <?php echo form::select($modellist, '44', 'name="info[modelid]"', '');?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('vip')?></td>
            <td>
          <label class="mt-checkbox mt-checkbox-outline" style
          ="margin-bottom: 0;"><input type="checkbox" name="info[vip]" value=1 /> <?php echo L('isvip')?> <span></span></label>
            <?php echo L('overduedate')?> <?php echo form::date('info[overduedate]', '', 1)?>
            </td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>