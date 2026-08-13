<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form action="?m=release&c=index&a=del" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80" class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
        <th width="80">ID</th>
        <th width="80"><?php echo L('type')?></th>
        <th width="80"><?php echo L("site")?>ID</th>
        <th><?php echo L('path')?></th>
        <th><?php echo L('time')?></th>
        <?php foreach ($this->point as $v) :$r = $this->db->get_one(array('id'=>$v), 'name');?>
        <th><?php echo $r['name']?></th>
        <?php endforeach;?>
        </tr>
        </thead>
<tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td width="80" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $v['id']?>" />
                        <span></span>
                    </label></td>
<td width="80" align="center"><?php echo $v['id']?></td>
<td align="center"><?php switch($v['type']){case 'edit':case 'add':echo L('upload');break;case 'del':echo L('delete');break;}?></td>
<td align="center"><?php echo $v['siteid']?></td>
<td align="center"><?php echo $v['path'];?></td>
<td align="center"><?php echo format::date($v['times'], 1);?></td>
<?php $i=1;foreach ($this->point as $d) :?>
<td align="center"><?php switch($v['status'.$i]){case -1:echo '<div class="onError">'.L("failure").'</div>';break;case 0:echo '<div class="onShow">'.L('not_upload').'</div>';break; case 1:echo '<div class="onCorrect">'.L("success").'</div>';break;}?></td>
<?php $i++;endforeach;?>
</tr>
<?php 
    endforeach;
endif;
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
        <label><button type="button" onclick="sync_agin()" class="btn green btn-sm"> <i class="fa fa-save"></i> <?php echo L('sync_agin')?></button></label>
        <label><button type="button" onclick="sync_agin2()" class="btn green btn-sm"> <i class="fa fa-save"></i> <?php echo L('all').L('sync_agin')?></button></label>
        <label><button type="submit" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete')?></button></label>
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
function sync_agin() {
    var ids =  '';
    $("input[type='checkbox'][name='ids[]']:checked").each(function(i,n){ids += ids ? ','+$(n).val() : $(n).val();});
    if (ids) {
        var width = '700px';
        var height = '500px';
        if (is_mobile()) {
            width = height = '100%';
        }
        var diag = new Dialog({
            id:'sync',
            title:'<?php echo L('sync_agin')?>',
            url:'<?php echo SELF;?>?m=release&c=index&a=init&statuses=-1&iniframe=1&ids='+ids+'&pc_hash='+pc_hash,
            width:width,
            height:height,
            modal:true
        });
        diag.onCancel=function() {
            $DW.close();
            location.reload(true)
        };
        diag.show();
    }
}
function sync_agin2() {
    var width = '700px';
    var height = '500px';
    if (is_mobile()) {
        width = height = '100%';
    }
    var diag = new Dialog({
        id:'sync',
        title:'<?php echo L('sync_agin')?>',
        url:'<?php echo SELF;?>?m=release&c=index&a=init&statuses=-1&iniframe=1&pc_hash='+pc_hash,
        width:width,
        height:height,
        modal:true
    });
    diag.onCancel=function() {
        $DW.close();
        location.reload(true)
    };
    diag.show();
}
//-->
</script>
</body>
</html>