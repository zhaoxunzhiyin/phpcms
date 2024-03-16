<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_header = true;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-10">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="15%" align="right"><?php echo L('selects')?></th>
			<th align="left"><?php echo L('values')?></th>
		</tr>
	</thead>
<tbody>
 <?php
if(is_array($forminfos_data)){
	foreach($forminfos_data as $key => $form){
?>   
	<tr>
		<td><?php echo $fields[$key]['name']?>:</td>
		<td><?php echo code2html($form)?></td>
		
		
		</tr>
<?php 
	}
}
?>
	<?php if($info['userid']){?>
	<tr>
      <th><?php echo L('账号Id');?></th>
      <td><?php echo $info['userid'];?></td>
    </tr>
    <?php }?>
	<tr>
      <th><?php echo L('作者');?></th>
      <td><?php echo $info['username'] ? $info['username'] : '游客';?></td>
    </tr>
	</tbody>
</table>
</div>
</div>
</body>
</html>