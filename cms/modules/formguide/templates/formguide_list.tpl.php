<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = true; 
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" id="myform" action="?m=formguide&c=formguide&a=listorder" method="post">
<input name="dosubmit" type="hidden" value="1">
<input name="menuid" type="hidden" value="<?php echo $this->input->get('menuid')?>">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="70" style="text-align:center"><?php echo L('listorder');?></th>
            <th width='200'><?php echo L('name_items')?></th>
            <th width='150'><?php echo L('tablename')?></th>
            <th width="160"><?php echo L('create_time')?></th>
            <th width="120" style="text-align:center;"><?php echo L('call')?></th>
            <th width="60" style="text-align:center;"><?php echo L('状态')?></th>
            <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($data)){
    foreach($data as $form){
?>
    <tr>
    <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="formid[]" value="<?php echo $form['modelid']?>" />
                        <span></span>
                    </label></td>
    <td style="text-align:center"><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=formguide&c=formguide&a=public_order_edit&formid='.$form['modelid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $form['sort'];?>" class="displayorder form-control input-sm input-inline input-mini"></td>
    <td><?php echo $form['name']?> <?php if ($form['items']) {?>(<?php echo $form['items']?>)<?php }?></td>
    <td align="center"><?php echo $form['tablename']?></td>
    <td align="center"><?php echo date('Y-m-d H:i:s', $form['addtime'])?></td>
    <td align="center"><input type="text" value="<script language='javascript' src='{APP_PATH}index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&action=js&siteid=<?php echo $form['siteid']?>'></script>"></td>
    <td align="center"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=formguide&c=formguide&a=disabled&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>', 1);" class="badge badge-<?php echo $form['disabled'] ? 'no' : 'yes';?>"><i class="fa fa-<?php echo $form['disabled'] ? 'times' : 'check';?>"></i></a></td>
    <td align="center">
    <a class="btn btn-xs yellow" href="<?php echo APP_PATH;?>index.php?m=formguide&c=index&a=show&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&siteid=<?php echo $form['siteid']?>" target="_blank"> <i class="fa fa-eye"></i> <?php echo L('preview')?></a>
    <a class="btn btn-xs blue" href="?m=formguide&c=formguide_info&a=init&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>"> <i class="fa fa-table"></i> <?php echo L('info_list')?></a>
    <a class="btn btn-xs green" href="?m=formguide&c=formguide&a=edit&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>"> <i class="fa fa-edit"></i> <?php echo L('modify')?></a>
    <a class="btn btn-xs dark" href="javascript:dr_iframe_show('<?php echo L('field');?>','?m=formguide&c=formguide_field&a=init&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&is_menu=1', '80%', '90%');"> <i class="fa fa-code"></i> <?php echo L('field')?></a>
    <a class="btn btn-xs red" href="javascript:void(0);" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes(new_html_special_chars($form['name']))))?>',function(){redirect('?m=formguide&c=formguide&a=delete&formid=<?php echo $form['modelid']?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash='+pc_hash);});"> <i class="fa fa-trash"></i> <?php echo L('del')?></a>
    <a class="btn btn-xs yellow" href="javascript:stat('<?php echo $form['modelid']?>', '<?php echo safe_replace($form['name'])?>');void(0);"> <i class="fa fa-bar-chart-o"></i> <?php echo L('stat')?></a></td>
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
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('affirm_delete')?>',function(){document.myform.action='?m=formguide&c=formguide&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('remove_all_selected')?></button></label>
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
function stat(id, title) {
    var w = 700;
    var h = 500;
    if (is_mobile()) {
        w = h = '100%';
    }
    var diag = new Dialog({
        id:'stat',
        title:'<?php echo L('stat_formguide')?>--'+title,
        url:'<?php echo SELF;?>?m=formguide&c=formguide&a=stat&formid='+id+'&pc_hash='+pc_hash,
        width:w,
        height:h,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
    };
    diag.show();
}
</script>