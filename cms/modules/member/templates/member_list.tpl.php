<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">var bs_selectAllText = '全选';var bs_deselectAllText = '全删';var bs_noneSelectedText = '没有选择'; var bs_noneResultsText = '没有找到 {0}';</script>
<link href="<?php echo JS_PATH?>bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH?>bootstrap-select/js/bootstrap-select.min.js" type="text/javascript"></script>
<script type="text/javascript">jQuery(document).ready(function(){$('.bs-select').selectpicker();});</script>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            orientation: "left",
            autoclose: true
        });
    }
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="member" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $this->input->get('menuid');?>" name="menuid">
                <div class="col-md-12 col-sm-12">
                <?php if(cleck_admin(param::get_session('userid'))) {?>
                <?php echo form::select($sitelist, $siteid, 'name="siteid[]" class="form-control bs-select" data-title="'.L('all_site').'" multiple="multiple"');?>
                <?php }?>
                <label><select name="status[]" class="form-control bs-select" data-title="<?php echo L('status')?>" multiple="multiple">
                    <option value='1' <?php if($this->input->get('status') && dr_in_array(1, $this->input->get('status'))){?>selected<?php }?>><?php echo L('lock')?></option>
                    <option value='0' <?php if($this->input->get('status') && dr_in_array(0, $this->input->get('status'))){?>selected<?php }?>><?php echo L('normal')?></option>
                </select></label>
                <?php echo form::select($modellist, $modelid, 'name="modelid[]" class="form-control bs-select" data-title="'.L('member_model').'" multiple="multiple"')?>
                <?php echo form::select($grouplist, $groupid, 'name="groupid[]" class="form-control bs-select" data-title="'.L('member_group').'" multiple="multiple" data-actions-box="true"')?>
                </div>
                <div class="col-md-12 col-sm-12">
                <label><select name="type" class="form-control">
                    <option value='1' <?php if($this->input->get('type') && $this->input->get('type')==1){?>selected<?php }?>><?php echo L('username')?></option>
                    <option value='2' <?php if($this->input->get('type') && $this->input->get('type')==2){?>selected<?php }?>><?php echo L('uid')?></option>
                    <option value='3' <?php if($this->input->get('type') && $this->input->get('type')==3){?>selected<?php }?>><?php echo L('email')?></option>
                    <option value='4' <?php if($this->input->get('type') && $this->input->get('type')==4){?>selected<?php }?>><?php echo L('regip')?></option>
                    <option value='5' <?php if($this->input->get('type') && $this->input->get('type')==5){?>selected<?php }?>><?php echo L('nickname')?></option>
                </select></label>
                <label><i class="fa fa-caret-right"></i></label>
                <label><input name="keyword" type="text" value="<?php if($this->input->get('keyword')) {echo $this->input->get('keyword');}?>" class="form-control input-text" /></label>
                </div>
                <div class="col-md-12 col-sm-12">
                <label>
                    <div class="input-group input-medium date-picker input-daterange" data-date="" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" value="<?php echo $start_time;?>" name="start_time" id="start_time">
                        <span class="input-group-addon"> - </span>
                        <input type="text" class="form-control" value="<?php echo $end_time;?>" name="end_time" id="end_time">
                    </div>
                </label>
                </div>
                <div class="col-md-12 col-sm-12">
                <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
                </div>
</form>
</div>
<form name="myform" id="myform" action="?m=member&c=member&a=delete" method="post" onsubmit="checkuid();return false;">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr class="heading">
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <?php 
            if(is_array($list_field)){
            foreach($list_field as $i=>$t){
            ?>
            <th<?php if($t['width']){?> width="<?php echo $t['width'];?>"<?php }?><?php if($t['center']){?> style="text-align:center"<?php }?> class="<?php echo dr_sorting($i);?>" name="<?php echo $i;?>"><?php echo L($t['name']);?></th>
            <?php }}?>
            <th><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    if(is_array($memberlist)){
    foreach($memberlist as $k=>$v) {
?>
    <tr>
        <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['userid']?>" name="userid[]" />
                        <span></span>
                    </label></td>
        <?php 
        if(is_array($list_field)){
        foreach($list_field as $i=>$tt){
        ?>
        <td<?php if($tt['center']){?> class="table-center" style="text-align:center"<?php }?>><?php echo dr_list_function($tt['func'], $v[$i], $param, $v, $field[$i], $i);?></td>
        <?php }}?>
        <td>
            <label><a href="javascript:edit(<?php echo $v['userid']?>, '<?php echo $v['username']?>')" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a></label>
            <label><a href="?m=member&c=member&a=public_alogin&id=<?php echo $v['userid']?>" target="_blank" class="btn btn-xs red"> <i class="fa fa-user"></i> <?php echo L('login')?></a></label>
            <?php foreach ($clink as $a) {
                echo ' <label><a class="btn '.$a['color'].' btn-xs" href="'.str_replace(['{modelid}', '{id}', '{siteid}', '{m}'], [$modelid, $v['userid'], $siteid, ROUTE_M], urldecode($a['url'])).'"><i class="'.$a['icon'].'"></i> '.L($a['name']);
                if ($a['field'] && $this->db->field_exists($a['field'])) {
                    echo '（'.intval($r[$a['field']]).'）';
                    
                }
                echo '</a></label>';
            }?>
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
        <?php echo $foot_tpl;?>
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
function edit(id, name) {
    artdialog('edit','?m=member&c=member&a=edit&userid='+id,'<?php echo L('edit').L('member')?>《'+name+'》',700,500);
}
function move() {
    var ids='';
    $("input[name='userid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('plsease_select').L('member')?>');
        return false;
    }
    artdialog('move','?m=member&c=member&a=move&ids='+ids,'<?php echo L('move').L('member')?>',700,500);
}
function delall() {
    var ids='';
    $("input[name='userid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('plsease_select').L('member')?>');
        return false;
    }
    Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});
}

function checkuid() {
    var ids='';
    $("input[name='userid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('plsease_select').L('member')?>');
        return false;
    } else {
        myform.submit();
    }
}
//-->
</script>
</body>
</html>