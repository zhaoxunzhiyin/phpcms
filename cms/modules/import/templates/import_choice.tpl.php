<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<form action="?m=import&c=import&a=choice&type=<?php echo $type;?>&importid=<?php echo $importid;?>" method="post" id="myform">
<fieldset>
	<legend><?php echo L('change_import_set')?></legend>
	<table width="100%"  class="table_form"> 
	
    <tr>
    <th width="120"><?php echo L('select_model')?></th>
	<?php if($type=="content"){?>
	<td id="content_model" class="y-bg">
		<select name="info[contentmodelid]" id="">
		<option value="0" selected><?php echo L('please_select')?></option>
 		<?php
		$i=0;
		foreach($models as $typeid=>$type){
		$i++;
		?>
		<option value="<?php echo $type['modelid'];?>" <?php if($now_modelid==$type['modelid']){echo 'selected';}?>><?php echo $type['name'];?></option>
		<?php }?>
		</select>
	</td>
	<?php }?>
	
	<?php if($type=="member"){?>
	<td id="member_model" class="y-bg">
		<select name="info[membermodelid]" id="">
		<option value="0" selected><?php echo L('please_select')?></option>
 		<?php
		$i=0;
		foreach($members as $memberid=>$member){
		$i++;
		?>
		<option value="<?php echo $member['modelid'];?>" <?php if($now_modelid==$member['modelid']){echo 'selected';}?>><?php echo $member['name'];?></option>
		<?php }?>
		</select>
	</td>
	<?php }?>
	
	<?php if($type=="other"){?>
	<td id="other_model" style="" class="y-bg"><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="info[type]" value="other" class="radio_style" checked> <?php echo L('other_model')?> <span></span></label>
        </div>
	</td>
	<?php }?>
	
  	</tr>  
</table>

<div class="bk15"></div>
<input type="submit" id="dosubmit" name="dosubmit" class="button" value="<?php echo L('submit')?>" />
</fieldset>
</form>
</div>
</body>
</html>
