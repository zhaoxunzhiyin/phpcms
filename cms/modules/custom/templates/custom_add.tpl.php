<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>


<div class="pad_10">
<form action="?m=custom&c=custom&a=add" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">


	<tr>
		<th width="100"><?php echo L('custom_title')?>：</th>
		<td><input type="text" name="custom[title]" id="custom_title"
			size="30" class="input-text"> （<?php echo L('custom_title_tips')?>）</td>
	</tr>

	<tr>
		<th><?php echo L('custom_content')?>：</th>
		<td><textarea name="custom[content]" id="content"></textarea><?php echo form::editor('content',"full");?></td>
	</tr>

<tr>
		<th></th>
		<td><input type="hidden" name="forward" value="?m=custom&c=custom&a=add"> <input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
	</tr>

</table>
</form>
</div>
</body>
</html> 