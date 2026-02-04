<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=special&c=special&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0" class="nHover">
        <thead>
            <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="80" align="center">ID</th>
            <th width="80" align="center"><?php echo L('listorder')?></th>
            <th><?php echo L('special_info')?></th>
            <th><?php echo L('operations_manage')?></th>
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
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id'];?>" />
                        <span></span>
                    </label></td>
    <td align="center"><?php echo $info['id']?></td>
    <td align="center"><input type='text' name='listorder[<?php echo $info['id']?>]' value="<?php echo $info['listorder']?>" class="displayorder form-control input-sm input-inline input-mini"></td>
    <td>
    <div class="col-left mr10" style="width:146px; height:112px"><?php if ($info['thumb']) {?>
<a href="<?php echo $info['url']?>" target="_blank"><img src="<?php echo dr_get_file($info['thumb'])?>" width="146" height="112" style="border:1px solid #eee" align="left"></a><?php }?>
</div>
<div class="col-auto">  
    <h2 class="title-1 f14 lh28 mb6 blue"><a href="<?php echo $info['url']?>" target="_blank"><?php echo $info['title']?></a></h2>
    <div class="lh22"><?php echo $info['description']?></div>
<p class="gray4"><?php echo L('create_man')?>：<span class="blue"><?php echo $info['username']?></span>， <?php echo L('create_time')?>：<?php echo dr_date($info['createtime'], null, 'red')?></p>
</div>
    </td>
    <td align="center"><a class="btn btn-xs blue" href='?m=special&c=content&a=init&specialid=<?php echo $info['id']?>'> <i class="fa fa-table"></i> <?php echo L('manage_news')?></a> <a class="btn btn-xs yellow" href='javascript:import_c(<?php echo $info['id']?>);void(0);'> <i class="fa fa-sign-in"></i> <?php echo L('import_news')?></a> <a class="btn btn-xs dark" href='?m=special&c=special&a=elite&value=<?php if($info['elite']==0) {?>1<?php } elseif($info['elite']==1) { ?>0<?php }?>&id=<?php echo $info['id']?>'> <i class="fa fa-flag"></i> <?php if($info['elite']==0) { echo L('elite_special'); } else {?><?php echo L('remove_elite')?><?php }?></a><br><br><a class="btn btn-xs blue" href="javascript:comment('<?php echo id_encode('special', $info['id'], $this->get_siteid())?>', '<?php echo new_addslashes(new_html_special_chars($info['title']))?>');void(0);"> <i class="fa fa-comment"></i> <?php echo L('special_comment')?></a> <a class="btn btn-xs green" href="?m=special&c=special&a=edit&specialid=<?php echo $info['id']?>&menuid=<?php echo $menu_data['id']?>"> <i class="fa fa-edit"></i> <?php echo L('edit_special')?></a> <a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('confirm', array('message'=>new_addslashes(new_html_special_chars($info['title']))))?>',function(){redirect('?m=special&c=special&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('del_special')?></a><br><br><a class="btn btn-xs yellow" href='?m=special&c=template&specialid=<?php echo $info['id']?>' target="_blank"> <i class="fa fa-eye"></i> <?php echo L('template_manage')?></a></td>
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
        <label><button type="button" onclick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=special&c=special&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
        <label><button type="submit" onclick="document.myform.action='?m=special&c=special&a=html'" class="btn blue btn-sm"> <i class="fa fa fa-file-code-o"></i> <?php echo L('update')?>html</button></label>
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
function edit(id, name) {
    artdialog('edit','?m=special&c=special&a=edit&specialid='+id,'<?php echo L('edit_special')?>--'+name,700,500);
}

function comment(id, name) {
    artdialog('comment','?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id,'<?php echo L('see_comment')?>：'+name,700,500);
}

function import_c(id) {
    omnipotent('import','?m=special&c=special&a=import&specialid='+id,'<?php echo L('import_news')?>',0,'60%','60%');
}
</script>