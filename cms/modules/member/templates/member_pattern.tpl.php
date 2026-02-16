<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="row myfbody">
        <div class="col-md-12">

            <div class="portlet light bordered">

                <div class="portlet-body form">

                    <div class="form-body">



                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('测试文字')?></label>
                            <div class="col-md-9">
                                <textarea name="data[text]" class="form-control" style="height:100px; width:100%;"></textarea>
                                <p class="help-block"> <?php echo L('填写需要测试验证的文字')?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label"><?php echo L('正则表达式')?></label>
                            <div class="col-md-9">
                                <textarea name="data[code]" id="dr_code" class="form-control" style="height:100px; width:100%;"></textarea>
                                <p class="help-block"> <?php echo L('填写PHP格式的正则表达式代码，匹配函数preg_match')?></p>
                                <label><select class="form-control" onchange="dr_add_code(this.value)">
                                    <option value=""><?php echo L('常用正则表达式');?></option>
                                    <?php 
                                    if(is_array($code)){
                                    foreach($code as $name=>$cc){
                                    ?>
                                    <option value="<?php echo $cc;?>"><?php echo L($name);?></option>
                                    <?php }}?>
                                </select></label>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </div>

    </div>

    <div class="portlet-body form myfooter">
        <div class="form-actions text-center">
            <button type="button" onclick="dr_test_pattern()" class="btn green"> <i class="fa fa-send"></i> <?php echo L('立即测试')?></button>
        </div>
    </div>
</form>
<script type="text/javascript">
    function dr_add_code(v) {
        $('#dr_code').val(v);
    }
    function dr_test_pattern() {
        var loading = layer.load(2, {
            shade: [0.3,'#fff'], //0.1透明度的白色背景
            time: 100000000
        });
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "?m=member&c=member_setting&a=public_test_pattern",
            data: $("#myform").serialize(),
            success: function(json) {
                layer.close(loading);
                // token 更新
                if (json.token) {
                    var token = json.token;
                    $("#myform input[name='"+token.name+"']").val(token.value);
                }
                if (json.code) {
                    dr_tips(1, json.msg, json.data.time);
                } else {
                    dr_tips(0, json.msg, json.data.time);
                }
            },
            error: function(HttpRequest, ajaxOptions, thrownError) {
                dr_ajax_alert_error(HttpRequest, this, thrownError);
            }
        });
    }
</script>
</div>
</div>
</div>
</div>
</body>
</html>