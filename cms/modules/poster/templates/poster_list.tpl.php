<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=poster&c=poster&a=listorder" method="post" id="myform">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0" class="contentWrap">
        <thead>
            <tr>
            <th align="center" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="60">ID</th>
            <th width="80"><?php echo L('listorder')?></th>
            <th align="center"><?php echo L('poster_title')?></th>
            <th width="80" align="center"><?php echo L('poster_type')?></th>
            <th align="center"><?php echo L('for_postion')?></th>
            <th width="100" align="center"><?php echo L('status')?></th>
            <th width='100' align="center"><?php echo L('hits')?></th>
            <th width="160" align="center"><?php echo L('addtime')?></th>
            <th align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
        <tbody>
 <?php 
if(is_array($infos)){
    foreach($infos as $info){
        $space = $this->s_db->get_one(array('spaceid'=>$info['spaceid']), 'name');
?>   
    <tr>
    <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
    <td align="center"><?php echo $info['id']?></td>
    <th><input type="text" name="listorder[<?php echo $info['id']?>]" value="<?php echo $info['listorder']?>" id="listorder" class="displayorder form-control input-sm input-inline input-mini"></th>
    <td><?php echo $info['name']?></td>
    <td align="center"><?php echo $types[$info['type']]?></td>
    <td align="center"><?php echo $space['name']?></td>
    <td align="center"><?php if($info['disabled']) { echo L('stop'); } elseif((strtotime($info['enddate'])<SYS_TIME) && (strtotime($info['enddate'])>0)) { echo L('past'); } else { echo L('start'); }?></td>
    <td align="center"><?php echo $info['clicks']?></td>
    <td align="center"><?php echo dr_date($info['addtime'], null, 'red');?></td>
    <td align="center"><a class="btn btn-xs green" href="<?php echo SELF;?>?m=poster&c=poster&a=edit&id=<?php echo $info['id'];?>&pc_hash=<?php echo dr_get_csrf_token();?>&menuid=<?php echo $this->input->get('menuid')?>" > <i class="fa fa-edit"></i> <?php echo L('edit')?></a> <a class="btn btn-xs blue" href="?m=poster&c=poster&a=stat&id=<?php echo $info['id']?>&spaceid=<?php echo $this->input->get('spaceid');?>"> <i class="fa fa-bar-chart-o"></i> <?php echo L('stat')?></a></td>
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
        <label><button type="submit" onClick="document.myform.action='?m=poster&c=poster&a=public_approval&passed=0'" class="btn blue btn-sm"> <i class="fa fa-play-circle-o"></i> <?php echo L('start')?></button></label>
        <label><button type="submit" onClick="document.myform.action='?m=poster&c=poster&a=public_approval&passed=1'" class="btn dark btn-sm"> <i class="fa fa-stop-circle-o"></i> <?php echo L('stop')?></button></label>
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=poster&c=poster&a=delete';$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages;?></div>
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
    artdialog('edit','?m=poster&c=poster&a=edit&id='+id,'<?php echo L('edit_ads')?>--'+name,600,430);
}
//-->
</script>