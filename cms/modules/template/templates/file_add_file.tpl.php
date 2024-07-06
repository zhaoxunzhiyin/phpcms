<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>

<div class="pad-10">
<form action="?m=template&c=file&a=add_file&style=<?php echo $this->style?>&dir=<?php echo $dir?>" method="post" id="myform">
<div>
	<table width="100%"  class="table_form">
    <tr>
    <th width="80"><?php echo L('name')?>：</th>
    <td class="y-bg"><input type="text" name="name" id="name" /></td>
  </tr>
</table>
</div>
</form>
</div>
</body>
</html>