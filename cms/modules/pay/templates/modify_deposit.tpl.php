<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">
<!--
$(function(){
    $.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
    $("#unit").formValidator({onshow:"<?php echo L('input_price_to_change')?>",onfocus:"<?php echo L('number').L('empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('number').L('empty')?>"}).regexValidator({regexp:"^(([1-9]{1}\\d*)|([0]{1}))(\\.(\\d){1,2})?$",onerror:"<?php echo L('must_be_price')?>"});
    $("#username").formValidator({onshow:"<?php echo L('input').L('username')?>",onfocus:"<?php echo L('username').L('empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('username').L('empty')?>"}).ajaxValidator({
        type : "get",
        url : "",
        data :"m=pay&c=payment&a=public_checkname_ajax",
        datatype : "html",
        async:'false',
        success : function(data){    
            if(data!= 'FALSE')
            {
                $("#balance").html(data);
                return true;
            }
            else
            {
                $("#balance").html('');
                return false;
            }
        },
        buttons: $("#dosubmit"),
        onerror : "<?php echo L('user_not_exist')?>",
        onwait : "<?php echo L('checking')?>"
    });
    $("#usernote").formValidator({onshow:"<?php echo L('input').L('reason_of_modify')?>",onfocus:"<?php echo L('usernote').L('empty')?>"}).inputValidator({min:1,max:999,onerror:"<?php echo L('usernote').L('empty')?>"});
})
//-->
</script>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                <div class="page-body">
<div class="note note-danger">
    <p><?php echo L('为指定账户进行充值金额')?></p>
</div>
<form name="myform" class="form-horizontal" action="?m=pay&c=payment&a=<?php echo ROUTE_A?>" method="post" id="myform">

    <div class="myfbody">
    <div class="portlet bordered light">
        <div class="portlet-title">
            <div class="caption">
                <span class="caption-subject font-green "><i class="fa fa-rmb"></i> <?php echo L('用户充值')?></span>
            </div>
        </div>
        <div class="portlet-body form">
            <div class="form-body">

                <div class="form-group" id="dr_row_paytype">
                    <label class="col-md-2 control-label"><?php echo L('recharge_type')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input name="pay_type" checked type="radio" value="1" /> <?php echo L('money')?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input name="pay_type" type="radio" value="2" /> <?php echo L('point')?> <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="dr_row_username">
                    <label class="col-md-2 control-label"><?php echo L('username')?></label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control" id="username" name="username" value="<?php echo $username?>"></label>
                    </div>
                </div>
                <div class="form-group" id="dr_row_payunit">
                    <label class="col-md-2 control-label"><?php echo L('处理方式')?></label>
                    <div class="col-md-9">
                        <div class="mt-radio-inline">
                            <label class="mt-radio mt-radio-outline"><input name="pay_unit" checked type="radio"  value="1"  /> <?php echo L('increase')?> <span></span></label>
                            <label class="mt-radio mt-radio-outline"><input name="pay_unit"  type="radio" value="0"  /> <?php echo L('reduce')?> <span></span></label>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="dr_row_unit">
                    <label class="col-md-2 control-label"><?php echo L('recharge_quota')?></label>
                    <div class="col-md-9">
                        <label><input type="text" class="form-control input-large" id="unit" name="unit" value="<?php echo $unit?>"></label>
                    </div>
                </div>
                <div class="form-group" id="dr_row_usernote">
                    <label class="col-md-2 control-label"><?php echo L('trading').L('usernote')?></label>
                    <div class="col-md-9">
                        <textarea class="form-control" id="usernote" name="usernote" rows="4"></textarea>
                    </div>
                </div>
                <div class="form-group" id="dr_row_sendemail">
                    <label class="col-md-2 control-label"><?php echo L('op_notice')?></label>
                    <div class="col-md-9">
                        <div class="mt-checkbox-inline">
                            <label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" id="sendemail" name="sendemail" value="1" checked> <?php echo L('op_sendemail')?> <span></span></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <button name="dosubmit" id="dosubmit" type="submit" class="btn blue"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
        </div>
    </div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>