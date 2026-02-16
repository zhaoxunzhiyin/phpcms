<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=admin&c=position&a=public_item" method="post">
<input type="hidden" value="<?php echo $posid?>" name="posid">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="80" align="left"><?php echo L('listorder');?></th>
            <th width="80" align="left">ID</th>
            <th><?php echo L('title');?></th>
            <th width="200"><?php echo L('catname');?></th>
            <th width="160"><?php echo L('inputtime')?></th>
            <th><?php echo L('posid_operation');?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos)){
    foreach($infos as $info){
?>   
    <tr>
    <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="items[]" value="<?php echo $info['id'],'-',$info['modelid']?>" id="items" />
                        <span></span>
                    </label></td>    
    <td>
    <input name='listorders[<?php echo $info['catid'],'-',$info['id']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini">
    </td>    
    <td><?php echo $info['id']?></td>
    <td><?php echo $info['title']?> <?php if($info['thumb']) {echo '<i class="fa fa-photo"></i>'; }?></td>
    <td align="center"><?php echo $info['catname']?></td>
    <td align="center"><?php echo dr_date($info['inputtime'], null, 'red')?></td>
    <td align="center"><a class="btn btn-xs blue" href="<?php echo $info['url']?>" target="_blank"> <i class="fa fa-eye"></i> <?php echo L('posid_item_view')?></a> <a class="btn btn-xs green" onclick="javascript:dr_content_submit('?m=content&c=content&a=edit&catid=<?php echo $info['catid']?>&id=<?php echo $info['id']?>','edit')" href="javascript:;"> <i class="fa fa-edit"></i> <?php echo L('posid_item_edit');?></a> <a class="btn btn-xs yellow" href="javascript:item_manage(<?php echo $info['id']?>,<?php echo $info['posid']?>, <?php echo $info['modelid']?>,'<?php echo $info['title']?>')"> <i class="fa fa-table"></i> <?php echo L('posid_item_manage')?></a>
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
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onclick="myform.action='?m=admin&c=position&a=public_item_listorder';myform.submit();" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
        <label><button type="submit" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('posid_item_remove')?></button></label>
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
function item_manage(id,posid, modelid, name) {
    artdialog('edit','?m=admin&c=position&a=public_item_manage&id='+id+'&posid='+posid+'&modelid='+modelid,'<?php echo L('edit')?>--'+name,550,430);
}
</script>