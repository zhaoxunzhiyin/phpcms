<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad_10">
<form action="?m=admin&c=ipbanned&a=add" method="post" name="myform" id="myform">
<table width="100%" cellpadding="2" cellspacing="1" class="table_form">
    <tr> 
      <th width="80">IP :</th>
      <td><input type="text" name="info[ip]" id="ip" size="25"></td>
    </tr>
    <tr> 
      <th><?php echo L('deblocking_time')?> :</th>
      <td><?php echo form::date('info[expires]', '', '')?></td>
    </tr>  
</table> 
     </form>
</div>
</body>
</html>