<?php 
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="?m=guestbook&c=guestbook&a=setting">
<table width="100%" cellpadding="0" cellspacing="1" class="table_form"> 
 
	<tr>
		<th width="20%"><?php echo L('application_or_not')?>：</th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[is_post]' value='1' <?php if($is_post == 1) {?>checked<?php }?>> <?php echo L('yes')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[is_post]' value='0' <?php if($is_post == 0) {?>checked<?php }?>> <?php echo L('no')?><span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><?php echo L('code_or_not')?>：</th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[enablecheckcode]' value='1' <?php if($enablecheckcode == 1) {?>checked<?php }?>> <?php echo L('yes')?><span></span></label>
          <label class="mt-radio mt-radio-outline"><input type='radio' name='setting[enablecheckcode]' value='0' <?php if($enablecheckcode == 0) {?>checked<?php }?>> <?php echo L('no')?><span></span></label>
        </div></td>
	</tr>
	 
	<tr>
		<td>　</td>
		<td><input type="submit" name="dosubmit" id="dosubmit" value=" <?php echo L('ok')?> " class="button">&nbsp;</td>
	</tr>
</table>
</form>
</body>
</html>
 