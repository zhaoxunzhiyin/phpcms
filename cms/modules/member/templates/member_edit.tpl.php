<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member&a=edit" method="post" id="myform">
<input type="hidden" name="info[userid]" id="userid" value="<?php echo $memberinfo['userid']?>"></input>
<input type="hidden" name="info[username]" value="<?php echo $memberinfo['username']?>"></input>
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%" class="table_form">
        <tr>
            <td width="80"><?php echo L('username')?></td> 
            <td><div class="input-group" style="width: 240px;">
                <input type="text" readonly="" value="<?php echo $memberinfo['username']?>" class="form-control input-text">
                <span class="input-group-btn">
                    <a class="btn red" href="javascript:dr_iframe('变更', '?m=member&c=member&a=username_edit&userid=<?php echo $memberinfo['userid']?>&pc_hash=<?php echo dr_get_csrf_token()?>', 500, 280);"><i class="fa fa-edit"></i> 变更</a>
                </span>
                </div><?php if($memberinfo['islock']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?><?php if($memberinfo['vip']) {?><img title="<?php echo L('lock')?>" src="<?php echo IMG_PATH?>icon/vip.gif"><?php }?></td>
        </tr>
        <tr>
            <td><?php echo L('avatar')?></td> 
            <td><img class="img-circle" src="<?php echo $memberinfo['avatar']?>" width="90" height="90"><label class="mt-checkbox mt-checkbox-outline" style="margin-top: 15px;margin-left: 15px;"><input type="checkbox" name="delavatar" id="delavatar" class="input-text" value="1"><?php echo L('delete').L('avatar')?><span></span></label></td>
        </tr>
        <tr>
            <td><?php echo L('password')?></td> 
            <td><input type="password" name="info[password]" id="password" class="input-text"></input></td>
        </tr>
        <tr>
            <td><?php echo L('nickname')?></td> 
            <td><input type="text" name="info[nickname]" id="nickname" value="<?php echo $memberinfo['nickname']?>" class="input-text"></input></td>
        </tr>
        <tr>
            <td><?php echo L('email')?></td>
            <td>
            <input type="text" name="info[email]" value="<?php echo $memberinfo['email']?>" class="input-text" id="email" size="30"></input>
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
            <?php echo form::select($grouplist, $memberinfo['groupid'], 'name="info[groupid]"', '');?> <div class="onShow"><?php echo L('changegroup_notice')?></div>
            </td>
        </tr>
        <tr>
            <td><?php echo L('point')?></td>
            <td>
            <input type="text" name="info[point]" value="<?php echo $memberinfo['point']?>" class="input-text" id="point" size="10"></input>
            </td>
        </tr>
        <tr>
            <td><?php echo L('member_model')?></td>
            <td>
            <?php echo form::select($modellist, $modelid, 'name="info[modelid]" onchange="changemodel($(this).val())"', '');?>
            </td>
        </tr>
        <tr>
            <td><?php echo L('vip')?></td>
            <td>
            <label class="mt-checkbox mt-checkbox-outline" style
          ="margin-bottom: 0;"><input type="checkbox" name="info[vip]" value=1 <?php if($memberinfo['vip']){?>checked<?php }?>/> <?php echo L('isvip')?> <span></span></label>
            <?php echo L('overduedate')?> <?php echo $form_overdudate?>
            </td>
        </tr>
    </table>
</fieldset>
<div class="bk15"></div>
<fieldset>
    <legend><?php echo L('more_configuration')?></legend>
    <table width="100%" class="table_form">
    <?php foreach($forminfos as $k=>$v) {?>
        <tr>
            <td width="80"><?php echo $v['name']?></td> 
            <td><?php echo $v['form']?></td>
        </tr>
    <?php }?>
    </table>
</fieldset>
</form>
</div>
</div>
</body>
<script language="JavaScript">
<!--
    function changemodel(modelid) {
        redirect('?m=member&c=member&a=edit&userid=<?php echo $memberinfo['userid']?>&modelid='+modelid+'&pc_hash=<?php echo dr_get_csrf_token()?>');
    }
//-->
</script>
</html>