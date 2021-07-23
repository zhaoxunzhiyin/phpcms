<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = 1;
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="?m=formguide&c=formguide&a=setting" id="myform" name="myform">
<table width="100%" cellpadding="0" cellspacing="1" class="table_form">
	<tr>
		<th width="130"><?php echo L('allows_more_ip')?>：</th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[allowmultisubmit]' value='1' <?php if($allowmultisubmit == 1) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[allowmultisubmit]' value='0' <?php if($allowmultisubmit == 0) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div></td>
	</tr>
	<tr id="setting"<?php if ($allowmultisubmit == 0) {?> style="display:none;"<?php }?>>
		<th width="130"><?php echo L('interval')?>：</th>
		<td><input type="text" value="<?php echo $interval?>" name="setting[interval]" size="10" class="input-text"> <?php echo L('minute')?></td>
	</tr>
	<tr>
		<th><?php echo L('allowunreg')?>：</th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[allowunreg]' value='1' <?php if($allowunreg == 1) {?>checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[allowunreg]' value='0' <?php if($allowunreg == 0) {?>checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><?php echo L('mailmessage')?>：</th>
		<td><textarea cols="50" rows="6" id="mailmessage" name="setting[mailmessage]"><?php echo $mailmessage?></textarea></td>
	</tr>
	<tr style="display:none">
		<td>&nbsp;</td>
		<td><input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('ok')?> ">&nbsp;<input type="reset" class="dialog" value=" <?php echo L('clear')?> "></td>
	</tr>
</table>
</form>
</body>
</html>
<script type="text/javascript">
$("input:radio[name='setting[allowmultisubmit]']").click(function (){
	if($("input:radio[name='setting[allowmultisubmit]'][checked]").val()==0) {
		$("#setting").hide();
	} else if($("input:radio[name='setting[allowmultisubmit]'][checked]").val()==1) {
		$("#setting").show();
	}
});
</script>