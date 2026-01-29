<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<div class="col-md-12 col-sm-12">
<label><?php echo L('all_linktype')?></label>
<label><i class="fa fa-caret-right"></i></label>
<label><a href="?m=link&c=link"><?php echo L('all')?></a></label>
            <?php
    if(is_array($type_arr)){
    foreach($type_arr as $typeid => $type){
        ?>
            <label><a href="?m=link&c=link&typeid=<?php echo $typeid;?>"><?php echo $type;?></a></label>
            <?php }}?>

</div>
</div>
<form name="myform" id="myform" action="?m=link&c=link&a=listorder" method="post">
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
            <th width="80" align="center"><?php echo L('listorder')?></th>
            <th><?php echo L('link_name')?></th>
            <th width="120" align="center"><?php echo L('logo')?></th>
            <th width="120" align="center"><?php echo L('typeid')?></th>
            <th width='120' align="center"><?php echo L('link_type')?></th>
            <th width="80" align="center"><?php echo L('status')?></th>
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
        <td align="center"><input name='listorders[<?php echo $info['linkid']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini"></td>
        <td><a href="<?php echo $info['url'];?>" title="<?php echo L('go_website')?>" target="_blank"><?php echo new_html_special_chars($info['name'])?></a> </td>
        <td align="center"><?php if($info['linktype']==1){?><?php if($info['passed']=='1'){?><?php if($info['logo']){?><img src="<?php echo dr_get_file($info['logo']);?>" width="83" height="31"><?php }}}?></td>
        <td align="center"><?php echo $type_arr[$info['typeid']];?></td>
        <td align="center"><?php if($info['linktype']==0){echo L('word_link');}else{echo L('logo_link');}?></td>
        <td align="center"><?php if($info['passed']=='0'){?><a
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('pass_or_not')?>',function(){redirect('?m=link&c=link&a=check&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"><font color=red><?php echo L('audit')?></font></a><?php }else{echo L('passed');}?></td>
        <td align="center"><a class="btn btn-xs green" href="javascript:void(0);"
            onclick="edit(<?php echo $info['linkid']?>, '<?php echo new_addslashes(new_html_special_chars($info['name']))?>')"
            title="<?php echo L('edit')?>"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes(new_html_special_chars($info['name']))))?>',function(){redirect('?m=link&c=link&a=delete&linkid=<?php echo $info['linkid']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> 
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
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('listorder')?></button></label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm_delete')?>',function(){document.myform.action='?m=link&c=link&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function edit(id, name) {
    artdialog('edit','?m=link&c=link&a=edit&linkid='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
</script>
</body>
</html>