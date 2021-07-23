<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form action="?m=import&c=import&a=add" method="post" id="myform">
<fieldset>
	<legend><?php echo L('add_import_setting')?></legend>
	<table width="100%"  class="table_form">
	
    <tr>
    <th width="120"><?php echo L('import_type')?></th>
    <td class="y-bg"><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[type]" value="content" checked="" onclick="$('#other_model').hide();$('#member_model').hide();$('#content_model').show();" class="radio_style"> <?php echo L('content_import')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[type]" value="member" onclick="$('#member_model').show();$('#content_model').hide();$('#other_model').hide();" class="radio_style"> <?php echo L('member_import')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[type]" value="other" onclick="$('#other_model').show();$('#member_model').hide();$('#content_model').hide();" class="radio_style"> <?php echo L('other_import')?> <span></span></label>
        </div>
	</td>
	</tr>
	
    <tr>
    <th width="120"><?php echo L('check_model')?></th>
	
	<td id="content_model" class="y-bg">
		<select name="info[contentmodelid]" id="">
		<option value="0" selected><?php echo L('please_select')?></option>
 		<?php
		$i=0;
		foreach($models as $typeid=>$type){
		$i++;
		?>
		<option value="<?php echo $type['modelid'];?>"><?php echo $type['name'];?></option>
		<?php }?>
		</select>
	</td>
	
	<td id="member_model" style="display:none;" class="y-bg">
		<select name="info[membermodelid]" id="">
		<option value="0" selected><?php echo L('please_select')?></option>
 		<?php
		$i=0;
		foreach($members as $memberid=>$member){
		$i++;
		?>
		<option value="<?php echo $member['modelid'];?>"><?php echo $member['name'];?></option>
		<?php }?>
		</select>
	</td>
	
	<td id="other_model" style="display:none;" class="y-bg">
		<font color=red><?php echo L('no_confie')?></font>
	</td>
	
  	</tr>  
</table>

<div class="bk15"></div>
<input type="submit" id="dosubmit" name="dosubmit" class="button" value="<?php echo L('submit')?>" />
</fieldset>
</form>
</div>
</body>
</html>
