<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=admin&c=role&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80"><?php echo L('userid')?></th>
        <th width="200"><?php echo L('username')?></th>
        <th><?php echo L('userinrole')?></th>
        <th width="180"><?php echo L('lastloginip')?></th>
        <th width="180"><?php echo L('lastlogintime')?></th>
        <th width="200"><?php echo L('email')?></th>
        <th width="100"><?php echo L('realname')?></th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($infos)){
    foreach($infos as $info){
?>
<tr>
<td><?php echo $info['userid']?></td>
<td><?php echo $info['username']?><?php if($info['islock']) {?> <span class="tooltips" data-container="body" data-placement="top" data-original-title="<?php echo L('lock')?>"><i class="fa fa-lock font-red"></i></span><?php }?></td>
<td><?php if(is_array($info['role'])){
foreach($info['role'] as $c){
?>
<span class="badge badge-blue"><?php echo $c;?></span>
<?php }}?>
</td>
<td><?php echo $info['lastloginip']?></td>
<td><?php echo $info['lastlogintime'] ? dr_date($info['lastlogintime'], null, 'red') : ''?></td>
<td><?php echo $info['email']?></td>
<td><?php echo $info['realname']?></td>
<td>
<a class="btn btn-xs green" href="?m=admin&c=admin_manage&a=edit&userid=<?php echo $info['userid']?>&menuid=<?php echo $this->input->get('menuid');?>"><?php echo L('edit')?></a>
<?php if(!dr_in_array($info['userid'], ADMIN_FOUNDERS)) {?>
<?php if($info['islock']) {?>
<a class="btn btn-xs yellow" href="?m=admin&c=admin_manage&a=unlock&userid=<?php echo $info['userid']?>"><?php echo L('unlock')?></a>
<?php } else { ?>
<a class="btn btn-xs dark" href="?m=admin&c=admin_manage&a=lock&userid=<?php echo $info['userid']?>"><?php echo L('lock')?></a>
<?php } ?>
<a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=admin_manage&a=delete&userid=<?php echo $info['userid']?>', '<?php echo L('admin_del_cofirm')?>')"><?php echo L('delete')?></a>
<?php } ?>
</td>
</tr>
<?php 
    }
}
?>
</tbody>
</table>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>