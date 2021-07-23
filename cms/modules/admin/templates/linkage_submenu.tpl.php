<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="pad_10">
<form name="myform" action="?m=admin&c=linkage&a=public_listorder" method="post">
<input type="hidden" name="keyid" value="<?php echo $keyid?>">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
		<tr>
		<th width="10%"><?php echo L('listorder')?></th>
		<th width="10%">ID</th>
		<th width="10%" align="left" ><?php echo L('linkage_name')?></th>
		<th width="20%"><?php echo L('linkage_desc')?></th>
		<th width="15%"><?php echo L('operations_manage')?></th>
		</tr>
        </thead>
        <tbody>
		<?php echo $submenu?>
		</tbody>
	</table>
	<div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder')?>" /></div>  </div>
</div>
</div>
</form>
<script type="text/javascript">
<!--
function add(id, name,linkageid) {
	artdialog('add','?m=admin&c=linkage&a=public_sub_add&keyid='+id+'&linkageid='+linkageid,name,500,320);
}
function edit(id, name,parentid) {
	artdialog('edit','?m=admin&c=linkage&a=edit&linkageid='+id+'&parentid='+parentid,name,500,200);
}
//-->
</script>
</body>
</html>