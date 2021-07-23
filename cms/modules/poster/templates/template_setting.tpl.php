<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_header = 1;
include $this->admin_tpl('header', 'admin');
?>
<form method="post" action="?m=poster&c=space&a=public_tempate_setting" name="myform" id="myform">
<table class="table_form" width="100%" cellspacing="0">
<tbody>
	<tr>
		<th width="200"><strong><?php echo L('template_file_name')?>：</strong></th>
		<td><?php echo $template?><input type="hidden" value="<?php echo $template?>" name="template"></td>
	</tr>
	<tr>
		<th><strong><?php echo L('name_cn')?>：</strong></th>
		<td><input type="text" size="20" value="<?php echo $info['name']?>" name="info[name]"></td>
	</tr>
	<tr>
		<th><strong><?php echo L('show_this_param')?>：</strong></th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="align"<?php if ($info['align']=='align'){?> checked<?php }?> name="info[align]" onclick="$('#choose_select').show();"> <?php echo L('lightbox')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="scroll"<?php if ($info['align']=='scroll'){?> checked<?php }?> name="info[align]" onclick="$('#choose_select').show();"> <?php echo L('rolling')?><span></span></label>
        </div></td>
	</tr>
	<tr id="choose_select" style="display:<?php if ($info['align']=='') {?>none<?php }?>">
		<th><strong><?php echo L('this_param_selected')?>：</strong></th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="info[select]"<?php if(!isset($info) || $info['select']==1) {?> checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="info[select]"<?php if(!isset($info) || $info['select']==0) {?> checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><strong><?php echo L('is_set_space')?>：</strong></th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="info[padding]"<?php if(!isset($info) || $info['padding']==1) {?> checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="info[padding]"<?php if(!isset($info) || $info['padding']==0) {?> checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><strong><?php echo L('is_set_size')?>：</strong></th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="info[size]"<?php if(!isset($info) || $info['size']==1) {?> checked<?php }?>> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="info[size]"<?php if(!isset($info) || $info['size']==0) {?> checked<?php }?>> <?php echo L('no')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><strong><?php echo L('space_poster')?>：</strong></th>
		<td><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="info[option]"<?php if(!isset($info) || $info['option']==1) {?> checked<?php }?>> <?php echo L('all_list')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="info[option]"<?php if(!isset($info) || $info['option']==0) {?> checked<?php }?>> <?php echo L('only_one')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><strong><?php echo L('is_used_type')?>：</strong></th>
		<td><div class="mt-checkbox-inline">
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" value="images" name="info[type][]"<?php if (is_array($info['type']) && in_array('images', $info['type'])) {?> checked<?php }?>> <?php echo L('photo')?> <span></span></label>
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" value="flash" name="info[type][]"<?php if (is_array($info['type']) && in_array('flash', $info['type'])) {?> checked<?php }?>> <?php echo L('flash')?> <span></span></label>
          <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" value="text" name="info[type][]"<?php if (is_array($info['type']) && in_array('text', $info['type'])) {?> checked<?php }?>> <?php echo L('title')?> <span></span></label>
        </div></td>
	</tr>
	<tr>
		<th><strong><?php echo L('max_add_param')?>：</strong></th>
		<td><input type="text" size="10" value="<?php echo $info['num']?>" name="info[num]"></td>
	</tr>
	</tbody>
	</table>
<div class="bk15" ><?php if ($info['iscore']) {?><input type="hidden" name="info[iscore]" value="1"><?php } else {?>
<input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('ok')?> " >&nbsp;<input type="reset" value=" <?php echo L('clear')?> " class='dialog'><?php }?>
	</div>
</form>
</body>
</html>