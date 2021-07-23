<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
<?php if($modelid==-1) {?>
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('category').L('field_manage');?></h2>
<?php } else if($modelid==-2) {?>
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('category_page').L('field_manage');?></h2>
<?php } else if($modelid) {?>
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('model_manage');?>--<?php echo $r['name'];?><?php echo L('field_manage');?></h2>
<?php } else {?>
  <h2 class="title-1 line-x f14 fb blue lh28"><?php echo L('sites').L('field_manage');?></h2>
<?php }?>
<div class="content-menu ib-a blue line-x">
<?php if($modelid==-1) {?>
<a href="?m=admin&c=category&a=init&module=admin&menuid=<?php echo $this->input->get('menuid')?>"><em><?php echo L('管理栏目');?></em></a>
<?php } else if($modelid==-2) {?>
<a href="?m=admin&c=category&a=init&module=admin&menuid=<?php echo $this->input->get('menuid')?>"><em><?php echo L('管理栏目');?></em></a>
<?php } else if($modelid) {?>
<a href="?m=content&c=sitemodel&a=init&menuid=<?php echo $this->input->get('menuid')?>"><em><?php echo L('模型管理');?></em></a>
<?php } else {?>
<a href="?m=admin&c=site&a=init&menuid=<?php echo $this->input->get('menuid')?>"><em><?php echo L('站点管理');?></em></a>
<?php }?>
　<a class="add fb" href="?m=content&c=sitemodel_field&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>"><em><?php echo L('add_field');?></em></a>
　<a class="on" href="?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>"><em><?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?></em></a><?php if($modelid && $modelid!=-1 && $modelid!=-2) {?><span>|</span>
<a href="javascript:;" onclick="javascript:openwinx('?m=content&c=sitemodel_field&a=public_priview&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>','')"><em><?php echo L('priview_modelfield');?></em></a>
<?php }?>
</div></div>
<div class="pad-lr-10">
<form name="myform" action="?m=content&c=sitemodel_field&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
			 <th width="70"><?php echo L('listorder')?></th>
            <th width="90"><?php echo L('fieldname')?></th>
			<th width="100"><?php echo L('cnames');?></th>
			<th width="100"><?php echo L('type');?></th>
			<th width="50"><?php echo L('system');?></th> 
            <th width="50"><?php echo L('must_input');?></th>
            <th width="50"<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo L('search');?></th>
            <th width="50"<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo L('listorder');?></th>
            <th width="50"<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo L('contribute');?></th>
			<th ><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody class="td-line">
	<?php
	foreach($datas as $r) {
		$tablename = L($r['tablename']);
	?>
    <tr>
		<td align='center' width='70'><input name='listorders[<?php echo $r['fieldid']?>]' type='text' size='3' value='<?php echo $r['listorder']?>' class='input-text-c'></td>
		<td width='90'><?php echo $r['field']?></td>
		<td width="100"><?php echo $r['name']?></td>
		<td width="100" align='center'><?php echo $r['formtype']?></td>
		<td width="50" align='center'><?php echo $r['issystem'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td width="50" align='center'><?php echo $r['minlength'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td width="50" align='center'<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo $r['issearch'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td width="50" align='center'<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo $r['isorder'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td width="50" align='center'<?php if(!$modelid || $modelid==-1) {echo ' style="display: none;"';}?>><?php echo $r['isadd'] ? L('icon_unlock') : L('icon_locked')?></td>
		<td align='center'> <a href="?m=content&c=sitemodel_field&a=edit&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('edit');?></a> | 
		<?php if(!in_array($r['field'],$forbid_fields)) { ?>
		<a href="?m=content&c=sitemodel_field&a=disabled&disabled=<?php echo $r['disabled'];?>&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo $r['disabled'] ? L('field_enabled') : L('field_disabled');?></a>
		<?php } else { ?><font color="#BEBEBE"> <?php echo L('field_disabled');?> </font><?php } ?>|<?php if(!in_array($r['field'],$forbid_delete)) {?> 
		<a href="javascript:confirmurl('?m=content&c=sitemodel_field&a=delete&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>','<?php echo L('confirm',array('message'=>$r['name']))?>')"><?php echo L('delete')?></a><?php } else {?><font color="#BEBEBE"> <?php echo L('delete');?></font><?php }?> </td>
	</tr>
	<?php } ?>
    </tbody>
    </table>
   <div class="btn"><input type="submit" class="button" name="dosubmit" value="<?php echo L('listorder');?>" /></div></div>
</form>
</div>
</body>
</html>
