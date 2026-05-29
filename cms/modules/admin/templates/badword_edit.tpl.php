<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
<form action="?m=admin&c=badword&a=edit&badid=<?php echo $badid?>" method="post" name="myform" id="myform">
     <tr> 
      <th width="20%"><?php echo L('badword_name')?></th>
      <td><input type="text" name="badword" id="badword" value="<?php echo $badword?>"></td>
    </tr>
    
    <tr> 
      <th width="20%"><?php echo L('badword_replacename')?></th>
      <td><input type="text" name="replaceword" id="replaceword" value="<?php echo $replaceword ?>" ></td>
    </tr>
    
    <tr> 
    <th width="20%"><?php echo L('badword_level')?></th>
    <td>
    <select size="1" id="info[level]" name="info[level]">
    <?php 
    if($level=='1'){
        $level1 = "selected";
    }else{
        $level2 = "selected";
    }
    ?>
    <option <?php echo $level1;?> value="1"><?php echo L('badword_common')?></option>
    <option <?php echo $level2;?> value="2"><?php echo L('badword_dangerous')?></option> 
    
    </select>
    <?php echo L('badword_level_info')?>
    </td>
    </tr> 
    </form>
</table>
</div>
</body>
</html>