<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=announce&c=admin_announce&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th align="center"><?php echo L('title')?></th>
            <th width="180" align="center"><?php echo L('startdate')?></th>
            <th width='180' align="center"><?php echo L('enddate')?></th>
            <th width='100' align="center"><?php echo L('inputer')?></th>
            <th width="100" align="center"><?php echo L('hits')?></th>
            <th width="160" align="center"><?php echo L('inputtime')?></th>
            <th align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
    foreach($data as $announce){
?>   
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="aid[]" value="<?php echo $announce['aid']?>" />
                        <span></span>
                    </label></td>
    <td><?php echo $announce['title']?></td>
    <td align="center"><?php echo $announce['starttime']?></td>
    <td align="center"><?php echo $announce['endtime']?></td>
    <td align="center"><?php echo $announce['username']?></td>
    <td align="center"><?php echo $announce['hits']?></td>
    <td align="center"><?php echo dr_date($announce['addtime'], null, 'red')?></td>
    <td align="center">
    <?php if ($this->input->get('s')==1) {?><a class="btn btn-xs blue" href="<?php echo APP_PATH;?>index.php?m=announce&c=index&a=show&aid=<?php echo $announce['aid']?>" title="<?php echo L('preview')?>"  target="_blank"> <i class="fa fa-eye"></i> <?php echo L('index')?></a><?php }?> 
    <a class="btn btn-xs green" href="javascript:edit('<?php echo $announce['aid']?>', '<?php echo safe_replace($announce['title'])?>');void(0);"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a>
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
        <?php if($this->input->get('s')==1) {?>
        <label><button type="submit" onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=0'" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('cancel_all_selected')?></button></label>
        <?php } elseif($this->input->get('s')==2) {?>
        <label><button type="submit" onClick="document.myform.action='?m=announce&c=admin_announce&a=public_approval&passed=1'" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('pass_all_selected')?></button></label>
        <?php }?>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('affirm_delete')?>',function(){document.myform.action='?m=announce&c=admin_announce&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $this->db->pages;?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, title) {
    artdialog('edit','?m=announce&c=admin_announce&a=edit&aid='+id,'<?php echo L('edit_announce')?>--'+title,700,500);
}
</script>