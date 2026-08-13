<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><form name="downform" action="?m=admin&c=downservers&a=init" method="post" >
<?php echo L('downserver_name')?> <label><input type="text" value="" class="input-text" name="info[sitename]"></label> <?php echo L('downserver_url')?> <label><input type="text" value="" class="input-text" name="info[siteurl]" size="50"></label> <?php echo L('downserver_site');?> <?php echo form::select($sitelist,self::get_siteid(),'name="info[siteid]"',$default)?> <label><input type="submit" value="<?php echo L('add');?>" class="button" name="dosubmit"></label>
</form></p>
</div>
<div class="right-card-box">
<form name="myform" action="?m=admin&c=downservers&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="10%"  align="left"><?php echo L('listorder');?></th>
            <th width="10%"  align="left">ID</th>
            <th width="20%"><?php echo L('downserver_name')?></th>
            <th width="35%"><?php echo L('downserver_url')?></th>
            <th width="15%"><?php echo L('downserver_site')?></th>
            <th width="15%"><?php echo L('posid_operation');?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos)){
    foreach($infos as $info){
?>   
    <tr>
    <td width="10%">
    <input name='listorders[<?php echo $info['id']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini">
    </td>    
    <td width="10%"><?php echo $info['id']?></td>
    <td  width="20%" align="center"><?php echo $info['sitename']?></td>
    <td width="35%" align="center"><?php echo $info['siteurl']?></td>
    <td width="15%" align="center"><?php echo $info['siteid'] ? $sitelist[$info['siteid']] : L('all_site')?></td>
    <td width="15%" align="center">
    <a class="btn btn-xs green" href="javascript:edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['sitename'])?>')"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a>
    <a class="btn btn-xs red" href="javascript:confirmurl('?m=admin&c=downservers&a=delete&id=<?php echo $info['id']?>', '<?php echo L('downserver_del_cofirm')?>')"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a>
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
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
<!--
function edit(id, name) {
    artdialog('edit','?m=admin&c=downservers&a=edit&id='+id,'<?php echo L('edit')?>--'+name,520,200);
}
//-->
</script>