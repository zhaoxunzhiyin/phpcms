<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = true;
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=poster&c=space&a=delete" method="post" id="myform">
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
            <th width="200"><?php echo L('boardtype')?></th>
            <th width="100" align="center"><?php echo L('ads_type')?></th>
            <th width='120' align="center"><?php echo L('size_format')?></th>
            <th width="80" align="center"><?php echo L('ads_num')?></th>
            <th width="180" align="center"><?php echo L('description')?></th>
            <th align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos)){
    foreach($infos as $info){
?>   
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="spaceid[]" value="<?php echo $info['spaceid']?>" />
                        <span></span>
                    </label></td>
    <td><?php echo $info['name']?></td>
    <td align="center"><?php echo $TYPES[$info['type']]?></td>
    <td align="center"><?php echo $info['width']?>*<?php echo $info['height']?></td>
    <td align="center"><?php echo $info['items']?></td>
    <td align="center"><?php echo $info['description']?></td>
    <td align="center"><a class="btn btn-xs blue" href="?m=poster&c=space&a=public_preview&spaceid=<?php echo $info['spaceid']?>" target="_blank"> <i class="fa fa-eye"></i> <?php echo L('preview')?></a> <a class="btn btn-xs dark" href="javascript:call(<?php echo $info['spaceid']?>);void(0);"> <i class="fa fa-code"></i> <?php echo L('get_code')?></a> <a class="btn btn-xs yellow" href='?m=poster&c=poster&a=init&spaceid=<?php echo $info['spaceid']?>&menuid=<?php echo $this->input->get('menuid')?>' > <i class="fa fa-table"></i> <?php echo L('ad_list')?></a> 
    <a class="btn btn-xs green" href="javascript:void(0);" onclick="edit(<?php echo $info['spaceid']?>, '<?php echo new_addslashes(new_html_special_chars($info['name']))?>')" title="<?php echo L('edit')?>" > <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red" href='javascript:void(0);' onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes(new_html_special_chars($info['name']))))?>',function(){redirect('?m=poster&c=space&a=delete&spaceid=<?php echo $info['spaceid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> <a class="btn btn-xs blue" href="<?php echo SELF;?>?m=poster&c=poster&a=add&spaceid=<?php echo $info['spaceid']?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash=<?php echo dr_get_csrf_token()?>"> <i class="fa fa-plus"></i> <?php echo L('add_poster')?></a></td>
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
<!--
function edit(id, name){
    artdialog('testIframe'+id,'?m=poster&c=space&a=edit&spaceid='+id,'<?php echo L('edit_space')?>--'+name,540,320);
};
function call(id) {
    omnipotent('call','?m=poster&c=space&a=public_call&sid='+id,'<?php echo L('get_code')?>',1,600,470);
}
//-->
</script>
</body>
</html>