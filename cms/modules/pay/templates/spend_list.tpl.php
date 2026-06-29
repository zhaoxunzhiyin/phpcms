<?php 
    defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
    include $this->admin_tpl('header','admin');
?>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            format: "yyyy-mm-dd",
            orientation: "left",
            autoclose: true
        });
    }
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="" method="get" >
<input type="hidden" value="pay" name="m">
<input type="hidden" value="spend" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $this->input->get('menuid')?>" name="menuid">
<div class="col-md-12 col-sm-12">
        <?php echo  form::select(array('1'=>L('username'), '2'=>L('userid')), $user_type, 'name="user_type"')?>
        <label><i class="fa fa-caret-right"></i></label>
        <label><input type="text" value="<?php echo $username?>" class="input-text" name="username"></label>
</div>
<div class="col-md-12 col-sm-12">
        <?php echo form::select(array(''=>L('op'), '1'=>L('username'), '2'=>L('userid')), $op_type, 'name="op_type"')?>
        <label><i class="fa fa-caret-right"></i></label>
        <label><input type="text" value="<?php echo $op?>" class="input-text" name="op"></label>
</div>
<div class="col-md-12 col-sm-12">
        <?php echo form::select(array(''=>L('expenditure_patterns'), '1'=>L('money'), '2'=>L('point')), $type, 'name="type"')?>
</div>
<div class="col-md-12 col-sm-12">
        <label><div class="formdate">
            <div class="input-group input-medium date-picker input-daterange">
                <input type="text" class="form-control" value="<?php echo ($starttime ? format::date($starttime) : '');?>" name="starttime">
                <span class="input-group-addon"> <?php echo L('to')?> </span>
                <input type="text" class="form-control" value="<?php echo ($endtime ? format::date($endtime) : '');?>" name="endtime">
            </div>
        </div></label>
</div>
<div class="col-md-12 col-sm-12">
        <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="10%"><?php echo L('username')?></th>
            <th width="20%"><?php echo L('content_of_consumption')?></th>
            <th width="15%"><?php echo L('empdisposetime')?> </th>
            <th width="9%"><?php echo L('op')?></th>
            <th width="8%"><?php echo L('expenditure_patterns')?></th>
            <th width="8%"><?php echo L('consumption_quantity')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($list)){
    $amount = $point = 0;
    foreach($list as $info){
?>   
    <tr>
    <td width="10%" align="center"><?php echo $info['username']?></td>
    <td width="20%" align="center"><?php echo $info['msg']?></td>
    <td  width="15%" align="center"><?php echo format::date($info['creat_at'], 1)?></td>
    <td width="9%" align="center"><?php if (!empty($info['op_userid'])) {echo $info['op_username'];} else {echo L('self');}?></td>
    <td width="8%" align="center"><?php if ($info['type'] == 1) {echo L('money');} elseif($info['type'] == 2) {echo L('point');}?></td>
    <td width="8%" align="center"><?php echo $info['value']?></td>
    </tr>
<?php 
    }
}
?>
    </tbody>
    </table>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
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
function discount(id, name) {
    artdialog('discount','?m=pay&c=payment&a=public_discount&id='+id,'<?php echo L('discount')?>--'+name,500,200);
}
function detail(id, name) {
    omnipotent('discount','?m=pay&c=payment&a=public_pay_detail&id='+id,'<?php echo L('discount')?>--'+name,1,500,550);
}
//-->
</script>