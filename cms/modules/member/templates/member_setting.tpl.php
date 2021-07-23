<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidator.js" charset="UTF-8"></script>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>formvalidatorregex.js" charset="UTF-8"></script>
<script type="text/javascript">
<!--
$(function(){
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#defualtpoint").formValidator({tipid:"pointtip",onshow:"<?php echo L('input').L('defualtpoint')?>",onfocus:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtpoint').L('between_1_to_8_num')?>"});
	$("#defualtamount").formValidator({tipid:"starnumtip",onshow:"<?php echo L('input').L('defualtamount')?>",onfocus:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('defualtamount').L('between_1_to_8_num')?>"});
	$("#rmb_point_rate").formValidator({tipid:"rmb_point_rateid",onshow:"<?php echo L('input').L('rmb_point_rate')?>",onfocus:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"}).regexValidator({regexp:"^\\d{1,8}$",onerror:"<?php echo L('rmb_point_rate').L('between_1_to_8_num')?>"});

});
//-->
</script>
<div class="pad-lr-10">
<div class="common-form">
<form name="myform" action="?m=member&c=member_setting&a=manage" method="post" id="myform">
	<table width="100%" class="table_form">
		<tr>
			<td width="200"><?php echo L('allow_register')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[allowregister]" value="1" <?php if($member_setting['allowregister']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[allowregister]" value="0" <?php if(!$member_setting['allowregister']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('register_model')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[choosemodel]" value="1" <?php if($member_setting['choosemodel']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[choosemodel]" value="0" <?php if(!$member_setting['choosemodel']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('register_email_auth')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[enablemailcheck]" value="1" <?php if($member_setting['enablemailcheck']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[enablemailcheck]" value="0" <?php if(!$member_setting['enablemailcheck']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label><font color=red><?php echo L('enablemailcheck_notice')?></font>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('enablcodecheck')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[enablcodecheck]" value="1" <?php if($member_setting['enablcodecheck']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[enablcodecheck]" value="0" <?php if(!$member_setting['enablcodecheck']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><font color="red"><?php echo L('mobile_checktype')?></font></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobile_checktype]"  class="input-radio" <?php if($member_setting['mobile_checktype']=='2') {?>checked<?php }?> value='2' <?php if($sms_disabled) {?>disabled<?php }?> onclick="$('#sendsms_titleid').hide();"> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[mobile_checktype]"  class="input-radio" <?php if($member_setting['mobile_checktype']=='0' ||$sms_disabled ) {?>checked<?php }?> value='0' onclick="$('#sendsms_titleid').hide();"> <?php echo L('no')?> <span></span></label><a href="?m=sms&c=sms&a=sms_setting"><font color=red>短信平台配置</font></a>
        </div>
			</td>
		</tr>
		<!--配置用户自发短信提示信息，需要结合CMS申请记录-->
		
		
		<tr id="sendsms_titleid" <?php if($member_setting['mobile_checktype']!='1'){?> style="display: none; " <?php }?>>
			<td width="200"><?php echo L('user_sendsms_title')?></td> 
			<td> 
			<input type="text" name="info[user_sendsms_title]" id="user_sendsms_title" class="input-text" size="50" value="<?php echo $member_setting['user_sendsms_title'];?>">
			</td>
		</tr> 
		
		
		
		<tr>
			<td width="200"><?php echo L('register_verify')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[registerverify]" value="1" <?php if($member_setting['registerverify']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[registerverify]" value="0" <?php if(!$member_setting['registerverify']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('show_app_point')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[showapppoint]" value="1" <?php if($member_setting['showapppoint']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[showapppoint]" value="0" <?php if(!$member_setting['showapppoint']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		
		<tr>
			<td width="200"><?php echo L('rmb_point_rate')?></td> 
			<td>
				<input type="text" name="info[rmb_point_rate]" id="rmb_point_rate" class="input-text" size="4" value="<?php echo $member_setting['rmb_point_rate'];?>">
			</td>
		</tr>
				
		<tr>
			<td width="200"><?php echo L('defualtpoint')?></td> 
			<td>
				<input type="text" name="info[defualtpoint]" id="defualtpoint" class="input-text" size="4" value="<?php echo $member_setting['defualtpoint'];?>">
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('defualtamount')?></td> 
			<td>
				<input type="text" name="info[defualtamount]" id="defualtamount" class="input-text" size="4" value="<?php echo $member_setting['defualtamount'];?>">
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('show_register_protocol')?></td> 
			<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[showregprotocol]" value="1" <?php if($member_setting['showregprotocol']) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[showregprotocol]" value="0" <?php if(!$member_setting['showregprotocol']) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('register_protocol')?></td> 
			<td>
				<textarea name="info[regprotocol]" id="regprotocol" style="width:80%;height:120px;"><?php echo $member_setting['regprotocol']?></textarea>
			</td>
		</tr>
		<tr>
			<td width="200"><?php echo L('register_verify_message')?></td> 
			<td>
				<textarea name="info[registerverifymessage]" id="registerverifymessage" style="width:80%;height:120px;"><?php echo $member_setting['registerverifymessage']?></textarea>
				<BR><?php echo L('register_func_tips');?>

			</td>
		</tr>

		<tr>
			<td width="200"><?php echo L('forgetpasswordmessage')?></td> 
			<td>
				<textarea name="info[forgetpassword]" id="forgetpassword" style="width:80%;height:120px;"><?php echo $member_setting['forgetpassword']?></textarea>
			</td>
		</tr>

	</table>
    <div class="bk15"></div>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</body>
</html>