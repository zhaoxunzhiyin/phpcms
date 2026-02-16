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
<input type="hidden" value="payment" name="c">
<input type="hidden" value="pay_stat" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $this->input->get('menuid')?>" name="menuid">
<div class="col-md-12 col-sm-12">
    <label><?php echo L('username')?></label>
    <label><i class="fa fa-caret-right"></i></label>
    <label><input type="text" value="<?php echo $username?>" class="input-text" name="info[username]"></label>
</div>
<div class="col-md-12 col-sm-12">
    <?php echo form::select($trade_status,$status,'name="info[status]"', L('all_status'))?>
</div>
<div class="col-md-12 col-sm-12">
    <label><div class="formdate">
        <div class="input-group input-medium date-picker input-daterange">
            <input type="text" class="form-control" value="<?php echo $start_addtime;?>" name="info[start_addtime]">
            <span class="input-group-addon"> <?php echo L('to')?> </span>
            <input type="text" class="form-control" value="<?php echo $end_addtime;?>" name="info[end_addtime]">
        </div>
    </div></label>
</div>
<div class="col-md-12 col-sm-12">
    <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
</div>
<fieldset>
    <legend><?php echo L('finance').L('totalize')?></legend>
    <table width="100%" class="table_form">
  <tbody>
  <tr>
    <th width="80"><?php echo L('total').L('transactions')?></th>
    <td class="y-bg"><?php echo L('money')?>&nbsp;&nbsp;<span class="font-fixh green"><?php echo $total_amount_num?></span> <?php echo L('bi')?>（<?php echo L('trade_succ').L('trade')?>&nbsp;&nbsp;<span class="font-fixh"><?php echo $total_amount_num_succ?></span> <?php echo L('bi')?>）<br/><?php echo L('point')?>&nbsp;&nbsp;<span class="font-fixh green"><?php echo $total_point_num?></span> <?php echo L('bi')?>（<?php echo L('trade_succ').L('trade')?>&nbsp;&nbsp;<span class="font-fixh"><?php echo $total_point_num_succ?></span> <?php echo L('bi')?>）</td>
  </tr>   
  <tr>
    <th width="80"><?php echo L('total').L('amount')?></th>
    <td class="y-bg"><span class="font-fixh green"><?php echo $total_amount?></span> <?php echo L('yuan')?>（<?php echo L('trade_succ').L('trade')?>&nbsp;&nbsp;<span class="font-fixh"><?php echo $total_amount_succ?></span><?php echo L('yuan')?>）<br/><span class="font-fixh green"><?php echo $total_point?></span><?php echo L('dian')?>（<?php echo L('trade_succ').L('trade')?>&nbsp;&nbsp;<span class="font-fixh"><?php echo $total_point_succ?></span><?php echo L('dian')?>）</td>
  </tr>
</table>
</fieldset>
<div class="bk10"></div>
<fieldset>
    <legend><?php echo L('query_stat')?></legend>
    <table width="100%" class="table_form">
  <tbody>
  <?php if($num) {?>
  <tr>
    <th width="80"><?php echo L('total_transactions')?>：</th>
    <td class="y-bg"><?php echo L('money')?>：<span class="font-fixh green"><?php echo $amount_num?></span> <?php echo L('bi')?>（<?php echo L('transactions_success')?>：<span class="font-fixh"><?php echo $amount_num_succ?></span> <?php echo L('bi')?>）<br/><?php echo L('point')?>：<span class="font-fixh green"><?php echo $point_num?></span> <?php echo L('bi')?>（<?php echo L('transactions_success')?>：<span class="font-fixh"><?php echo $point_num_succ?></span> <?php echo L('bi')?>）</td>
  </tr>   
  <tr>
    <th width="80"><?php echo L('total').L('amount')?>：</th>
    <td class="y-bg"><span class="font-fixh green"><?php echo $amount?></span><?php echo L('yuan')?>（<?php echo L('transactions_success')?>：<span class="font-fixh"><?php echo $amount_succ?></span><?php echo L('yuan')?>）<br/><span class="font-fixh green"><?php echo $point?></span><?php echo L('dian')?>（<?php echo L('transactions_success')?>：<span class="font-fixh"><?php echo $point_succ?></span><?php echo L('dian')?>）</td>
  </tr>
  <?php }?>
</table>
</fieldset>
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