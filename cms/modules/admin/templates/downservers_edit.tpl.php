<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<div class="common-form">
<form name="myform" action="?m=admin&c=downservers&a=edit" method="post" id="myform">
<input type="hidden" name="id" value="<?php echo $id?>"></input>
<table width="100%" class="table_form">
<tr>
<td  width="80"><?php echo L('mirrors_name')?></td> 
<td><input type="text" name="info[sitename]" class="input-text" value="<?php echo $sitename?>" id="sitename"></input></td>
</tr>
<tr>
<td  width="80"><?php echo L('mirror_address')?></td> 
<td><input type="text" name="info[siteurl]" class="input-text" value="<?php echo $siteurl?>" id="siteurl" size="40"></input></td>
</tr> 
<tr>
<td><?php echo L('site_select')?></td>
<td><?php echo form::select($sitelist,$siteid,'name="info[siteid]"',$default)?></td>
</tr> 
</table>
</form>
</div>
</div>
</body>
</html>




