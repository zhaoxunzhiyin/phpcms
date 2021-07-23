<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header', 'admin');
?>
<form name="myform" action="?m=admin&c=position&a=listorder" method="post">
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="10%"  align="left"><?php echo L('payment_mode').L('name')?></th>
            <th width="5%"><?php echo L('plus_version')?></th>
            <th width="15%"><?php echo L('plus_author')?></th>
            <th width="45%"><?php echo L('desc')?></th>
             <th width="10%"><?php echo L('listorder')?></th>
            <th width="15%"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos['data'])){
	foreach($infos['data'] as $info){
?>   
	<tr>
	<td width="10%"><?php echo $info['pay_name']?></td>
	<td  width="5%" align="center"><?php echo $info['version']?></td>
	<td  width="15%" align="center"><?php echo $info['author']?></td>
	<td width="45%" align="center"><?php echo $info['pay_desc']?></td>
	<td width="10%" align="center"><?php echo $info['pay_order']?></td>
	<td width="15%" align="center">
	<?php if ($info['enabled']) {?>
	<a href="javascript:edit('<?php echo $info['pay_id']?>', '<?php echo $info['pay_name']?>')"><?php echo L('configure')?></a> | 
	<a href="javascript:confirmurl('?m=pay&c=payment&a=delete&id=<?php echo $info['pay_id']?>', '<?php echo L('confirm',array('message'=>$info['pay_name']))?>')"><?php echo L('plus_uninstall')?></a>
	<?php } else {?>
	<a href="javascript:add('<?php echo $info['pay_code']?>', '<?php echo $info['pay_name']?>')"><?php echo L('plus_install')?></a>
	<?php }?>
	</td>
	</tr>
<?php 
	}
}
?>
    </tbody>
    </table>
  
    <div class="btn"></div>  </div>

 <div id="pages"> <?php echo $pages?></div>
</div>
</div>
</form>
</body>
<a href="javascript:edit(<?php echo $v['siteid']?>, '<?php echo $v['name']?>')">
</html>
<script type="text/javascript">
<!--
function add(id, name) {
	artdialog('add','?m=pay&c=payment&a=add&code='+id,'<?php echo L('edit')?>--'+name,700,500);
}
function edit(id, name) {
	artdialog('edit','?m=pay&c=payment&a=edit&id='+id,'<?php echo L('edit')?>--'+name,700,500);
}
//-->
</script>