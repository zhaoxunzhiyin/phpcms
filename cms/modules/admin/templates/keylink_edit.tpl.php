<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="pad_10">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
<form action="?m=admin&c=keylink&a=edit&keylinkid=<?php echo $keylinkid?>" method="post" name="myform" id="myform">
     <tr> 
      <th width="25%"><?php echo L('keylink_name');?> :</th>
      <td><input type="text" name="info[word]" id="word" size="20" value="<?php echo $word?>"></td>
    </tr>
    
    <tr> 
      <th><?php echo L('keylink_url');?> :</th>
      <td><input type="text" name="info[url]" id="url" size="30" value="<?php echo $url ?>" ></td>
    </tr>
    </form>
</table>
</div>
</body>
</html>