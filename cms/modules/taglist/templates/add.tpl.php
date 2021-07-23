<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad_10">
<form action="?m=taglist&c=taglist&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th width="20%">关键字:</th>
		<td><input type="text" name="tag[keyword]" id="tag_keyword" size="30" class="input-text"></td>
	</tr>
	<tr>
		<th width="20%">拼音:</th>
		<td><input type="text" name="tag[pinyin]" id="tag_pinyin" size="30" class="input-text"></td>
	</tr>
	<tr>
		<th></th>
		<td>
			<input type="hidden" name="forward" value="?m=taglist&c=taglist&a=add">
			<input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
</body>
</html> 