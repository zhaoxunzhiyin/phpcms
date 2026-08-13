<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_all_cache');?></a></p>
</div>
<div class="right-card-box">
<form name="myform" action="?m=admin&c=role&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80"><?php echo L('listorder');?></th>
        <th width="80">ID</th>
        <th><?php echo L('role_name');?></th>
        <th width="200"><?php echo L('role_desc');?></th>
        <th width="80"><?php echo L('role_status');?></th>
        <th><?php echo L('role_operation');?></th>
        </tr>
        </thead>
<tbody>
<?php 
if(is_array($infos)){
    foreach($infos as $info){
?>
<tr>
<td align="center"><input name='listorders[<?php echo $info['roleid']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini"></td>
<td align="center"><?php echo $info['roleid']?></td>
<td><?php echo $info['rolename']?></td>
<td><?php echo $info['description']?></td>
<td><a href="?m=admin&c=role&a=change_status&roleid=<?php echo $info['roleid']?>&disabled=<?php echo ($info['disabled']==1 ? 0 : 1)?>"<?php echo $info['disabled']? 'class="badge badge-no"':'class="badge badge-yes"'?>><?php echo $info['disabled']? '<i class="fa fa-times"></i>':'<i class="fa fa-check"></i>'?></a></td>
<td>
<?php if($info['roleid'] > 1) {?>
<a class="btn btn-xs blue" href="javascript:setting_role(<?php echo $info['roleid']?>, '<?php echo new_addslashes($info['rolename'])?>')"><?php echo L('role_setting');?></a> <a class="btn btn-xs dark" href="javascript:void(0)" onclick="setting_cat_priv(<?php echo $info['roleid']?>, '<?php echo new_addslashes($info['rolename'])?>')"><?php echo L('usersandmenus')?></a>
<?php }?>
<?php if($info['roleid'] > 1) {?><a class="btn btn-xs green" href="?m=admin&c=role&a=edit&roleid=<?php echo $info['roleid']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('edit')?></a>
<a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=role&a=delete&roleid=<?php echo $info['roleid']?>', '<?php echo L('posid_del_cofirm')?>')"><?php echo L('delete')?></a>
<?php }?>
</td>
</tr>
<?php 
    }
}
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
    </div>
    <div class="col-md-7 list-page"></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
<script type="text/javascript">
<!--
function setting_role(id, name) {
    openwinx('role','?m=admin&c=role&a=priv_setting&roleid='+id+'&pc_hash='+pc_hash,'<?php echo L('sys_setting')?>《'+name+'》','80%','80%');
}

function setting_cat_priv(id, name) {
    openwinx('role','?m=admin&c=role&a=setting_cat_priv&roleid='+id+'&pc_hash='+pc_hash,'<?php echo L('usersandmenus')?>《'+name+'》','80%','80%');
}
//-->
</script>
</html>
