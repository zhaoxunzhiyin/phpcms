<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad_10">
<table width="100%" cellpadding="2" cellspacing="1" class="table_form">
<form action="?m=admin&c=keylink&a=add" method="post" name="myform" id="myform">
    <tr> 
      <th width="25%"><?php echo L('keylink_name');?> :</th>
      <td><input type="text" name="info[word]" id="word" size="20"></td>
    </tr>
    <tr> 
      <th><?php echo L('keylink_url');?> :</th>
      <td><input type="text" name="info[url]" value="http://www." size="30" id="url"></td>
    </tr> 
     </form>
</table> 
</div>
</body>
</html>