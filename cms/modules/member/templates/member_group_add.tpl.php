<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member_group&a=add" method="post" id="myform">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="120"><?php echo L('member_group_name')?></td> 
            <td><input type="text" name="info[name]"  class="input-text" id="name"></td>
        </tr>
        <tr>
            <td><?php echo L('member_group_creditrange')?></td> 
            <td>
            <input type="text" name="info[point]" class="input-text" id="point" value="0"></td>
        </tr>
        <tr>
            <td><?php echo L('member_group_starnum')?></td> 
            <td><input type="text" name="info[starnum]" class="input-text" id="starnum" value="0"></td>
        </tr>
    </table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('more_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td><?php echo L('member_group_permission')?></td> 
            <td><div class="mt-checkbox-inline">
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowpost]"><?php echo L('member_group_publish')?><span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowpostverify]"><?php echo L('member_group_publish_verify')?><span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowupgrade]"><?php echo L('member_group_upgrade')?> <span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowsendmessage]"><?php echo L('member_group_sendmessage')?> <span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowattachment]"><?php echo L('allowattachment')?> <span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowdownfile]"><?php echo L('附件下载权限')?> <span></span></label>
                    <label class="mt-checkbox mt-checkbox-outline" style="width:120px;"><input type="checkbox" name="info[allowsearch]"><?php echo L('allowsearch')?> <span></span></label>
            </div></td>

        </tr>

        <tr>
            <td width="100"><?php echo L('member_group_upgradeprice')?></td> 
            <td>
                <span class="ik lf" style="width:120px;">
                    <?php echo L('member_group_dayprice')?>：
                    <input type="text" name="info[price_d]" class="input-text">    
                </span>
                <span class="ik lf" style="width:120px;">
                    <?php echo L('member_group_monthprice')?>：
                    <input type="text" name="info[price_m]" class="input-text">
                </span>
                <span class="ik lf" style="width:120px;">
                    <?php echo L('member_group_yearprice')?>：
                    <input type="text" name="info[price_y]" class="input-text">
                </span>
            </td>
        </tr>
        <tr>
            <td width="100"><?php echo L('附件总空间')?></td> 
            <td><div class="input-inline input-medium">
                    <div class="input-group">
                        <input type="text" name="info[filesize]" value="" class="form-control" placeholder="">
                        <span class="input-group-addon">MB</span>
                    </div>
                </div>
                <span class="help-block"><?php echo L('设置0或者空时，表示不限制附件大小');?></span></td>
        </tr>
        <tr>
            <td width="100"><?php echo L('member_group_maxmessagenum')?></td> 
            <td><input type="text" name="info[allowmessage]" class="input-text" id="allowmessage"></td>
        </tr>
        <tr>
            <td width="100"><?php echo L('allowpostnum')?></td> 
            <td><input type="text" name="info[allowpostnum]" class="input-text" id="allowpostnum">
            <span class="help-block"><?php echo L('zero_nolimit');?></span></td>
        </tr>
        <tr>
            <td width="100"><?php echo L('member_group_username_color')?></td> 
            <td><?php echo color_select('info[usernamecolor]', '#000000');?></td>
        </tr>
        <tr>
            <td width="100"><?php echo L('member_group_icon')?></td> 
            <td><input type="text" name="info[icon]" class="input-text" id="icon" value="images/group/vip.jpg"></td>
        </tr>
        <tr>
            <td width="100"><?php echo L('member_group_description')?></td> 
            <td><input type="text" name="info[description]" class="input-text"></td>
        </tr>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
</html>