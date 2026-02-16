<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=link&c=link&a=check_register" method="post" onsubmit="checkuid();return false;">
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
             <th><?php echo L('link_name')?></th>
             <th width="300" align="center"><?php echo L('url')?></th> 
            <th width="120" align="center"><?php echo L('logo')?></th> 
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
                        <input type="checkbox" class="checkboxes" name="linkid[]" value="<?php echo $info['linkid']?>" />
                        <span></span>
                    </label></td>
         <td><a href="<?php echo $info['url'];?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo $info['name']?></a></td>
        <th align="center"><a href="<?php echo $info['url']?>" target="_blank"><?php echo $info['url']?></a></th>
        <td align="center"><?php if($info['linktype']==1){?><img src="<?php echo dr_get_file($info['logo']);?>" width=83 height=31><?php }?></td>
         <td align="center"><a class="btn btn-xs green" href="javascript:void(0);"
            onclick="edit(<?php echo $info['linkid']?>, '<?php echo new_addslashes($info['name'])?>')"
            title="<?php echo L('edit')?>"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=link&c=link&a=delete&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> 
        
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('pass_or_not')?>',function(){$('#myform').submit();});" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('pass_check')?></button></label>
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=link&c=link&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $this->pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=link&c=link&a=edit&linkid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
function checkuid() {
    var ids='';
    $("input[name='linkid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert("<?php echo L('select_operations')?>");
        return false;
    } else {
        myform.submit();
    }
}
</script>
