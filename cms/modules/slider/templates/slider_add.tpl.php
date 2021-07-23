<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>


<div class="pad_10">
<form action="?m=slider&c=slider&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


	<tr>
		<th width="20%"><?php echo L('typeid')?>：</th>
		<td><select name="slider[typeid]" id="">
		<?php
		  $i=0;
		  foreach($types as $typeid=>$type){
		  $i++;
		?>
		<option value="<?php echo $type['typeid'];?>" <?php if($type['typeid']==$this->input->get('typeid')){echo "selected";}?>><?php echo $type['name'];?></option>
		<?php }?>
		</select></td>
	</tr>
	

	
	<tr>
		<th width="100"><?php echo L('slider_name')?>：</th>
		<td><input type="text" name="slider[name]" id="slider_name"
			size="30" class="input-text"></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('url')?>：</th>
		<td><input type="text" name="slider[url]" id="slider_url"
			size="30" class="input-text">(若不想输入地址，请输入 # )</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('image')?>：</th>
		<td><?php echo form::images('slider[image]', 'image', '', 'slider')?></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('slider_desc')?>：</th>
		<td><textarea name="slider[description]" id="slider_description" rows="4" cols="80"></textarea></td>
	</tr>

	
	<tr>
		<th width="100"><?php echo L('slider_listorder')?>：</th>
		<td><input type="text" name="slider[listorder]" id="listorder"
			size="10" class="input-text" value="0"></td>
	</tr>

	 
	<tr>
		<th><?php echo L('status')?>：</th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input name="slider[isshow]" type="radio" value="1" checked>&nbsp;<?php echo L('isshow')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input name="slider[isshow]" type="radio" value="0">&nbsp;<?php echo L('notshow')?> <span></span></label>
        </div></td>
	</tr>

<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=slider&c=slider&a=add"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html> 