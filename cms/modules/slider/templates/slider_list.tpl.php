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
        <label>位置</label>
        <label><i class="fa fa-caret-right"></i></label>
        <?php
    if(is_array($type_arr)){
    foreach($type_arr as $typeid => $type){
        ?><label><a href="?m=slider&c=slider&typeid=<?php echo $typeid;?>"><?php echo $type;?></a></label>
        <?php }}?></label>
</div>
</div>
<form name="myform" id="myform" action="?m=slider&c=slider&a=listorder" method="post">
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
            <th width="80"><?php echo L('listorder')?></th>
            <th><?php echo L('slider_name')?></th>
            <th width="100"><?php echo L('image')?></th>
            <th width="100"><?php echo L('url')?></th>
            <th width='100'><?php echo L('typeid')?></th>
            <th width="100"><?php echo L('status')?></th>
            <th width="160"><?php echo L('slider_adddate')?></th>
            <th><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
        <td><input name='listorders[<?php echo $info['id']?>]' type='text' value='<?php echo $info['listorder']?>' class="displayorder form-control input-sm input-inline input-mini"></td>
        <td><?php if ($info['url']!="#" && $info['url']){?><a href="<?php echo $info['url'];?>" title="<?php echo $info['name']?>" target="_blank"><?php }?><?php echo $info['name']?><?php if ($info['url']!="#" && $info['url']){?></a><?php }?></td>
        <td><a href="javascript:preview('<?php echo dr_get_file($info['image']);?>')" title="<?php echo $info['description'];?>"><img src="<?php echo dr_get_file($info['image']);?>" style="max-width:80px;max-height:60px;"></a></td>
        <td align="center"><?php if ($info['url']!="#" && $info['url']){?><a class="btn btn-xs yellow" href="<?php echo $info['url'];?>" target="_blank">点击查看</a><?php }else{?>无<?php }?></td>
        <td align="center"><?php echo $type_arr[$info['typeid']];?></td>
        <td align="center"><?php if($info['isshow']=='0'){ echo "不显示";}else{echo "显示";}?></td>
        <td align="center"><?php echo dr_date($info['addtime'], null, 'red');?></td>
        <td><a class="btn btn-xs green" href="javascript:void(0);"
            onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['name'])?>')"
            title="<?php echo L('edit')?>"><?php echo L('edit')?></a> <a class="btn btn-xs red"
            href='javascript:void(0);'
            onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=slider&c=slider&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a> 
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=slider&c=slider&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
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
    artdialog('edit','?m=slider&c=slider&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',700,450);
}
</script>
</body>
</html>