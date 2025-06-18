<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad_10">
<form action="?m=admin&c=copyfrom&a=add" method="post" name="myform" id="myform">
<table width="100%" cellpadding="2" cellspacing="1" class="table_form">
    <tr> 
      <th width="80"><?php echo L('copyfrom_name');?> :</th>
      <td><input type="text" name="info[sitename]" id="sitename" size="25"></td>
    </tr>
    <tr> 
      <th><?php echo L('copyfrom_url')?> :</th>
      <td><input type="text" name="info[siteurl]" id="siteurl" size="25"></td>
    </tr> 
    <tr> 
      <th><?php echo L('copyfrom_logo')?> :</th>
      <td><?php echo form::images('info[thumb]', 'thumb', '', 'admin')?></td>
    </tr> 
</table> 
     </form>
</div>
</body>
</html>