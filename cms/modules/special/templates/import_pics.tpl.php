<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
$show_header = $show_validator = $show_scroll = true; 
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
<div class="row table-search-tool" id="searchid" style="display:">
<form name="searchform" action="" method="get" >
<input type="hidden" value="special" name="m">
<input type="hidden" value="special" name="c">
<input type="hidden" value="public_get_pics" name="a">
<input type="hidden" name="dosubmit" value="1">
<div class="col-md-12 col-sm-12">
             <label><?php echo $model_form?></label>
</div>
<div class="col-md-12 col-sm-12">
            <label id="catids"></label>
</div>
<div class="col-md-12 col-sm-12">
            <span id="title" style="display:none;"><?php echo L('title')?>：<label><input type="text" name="title" size="20"></label></span>
</div>
<div class="col-md-12 col-sm-12">
            <label><div class="formdate">
                <div class="input-group input-medium date-picker input-daterange">
                    <input type="text" class="form-control" value="<?php echo $this->input->get('start_time');?>" name="start_time" id="start_time">
                    <span class="input-group-addon"> - </span>
                    <input type="text" class="form-control" value="<?php echo $this->input->get('end_time');?>" name="end_time" id="end_time">
                </div>
            </div></label>
</div>
<div class="col-md-12 col-sm-12">
            <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
</div>
</form>
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
            <th><?php echo L('content_title')?></th>
            </tr>
        </thead>
<tbody>
    <?php if(is_array($data)) { foreach ($data as $r) {?>
        <tr>
        <td><div class="mt-radio-inline"><label class="mt-radio mt-radio-outline" style="display:block"><input type="radio" onclick="choosed(<?php echo $r['id']?>, <?php echo $r['catid']?>, '<?php echo $r['title']?>')" class="inputcheckbox" name='ids' value="<?php echo $r['id'];?>"> <?php echo $r['title'];?><span></span></label></div></td>
        </tr>
     <?php } }?>
</tbody>
     </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-12 list-page"><?php echo $pages?></div>
</div>
<input type="hidden" name="msg_id" id="msg_id">
</div>
</div>
</div>
</div>
</body>
</html>
<script type="text/javascript">

    function choosed(contentid, catid, title) {
        var msg = contentid+'|'+catid+'|'+title;
        $('#msg_id').val(msg);
    }

    function select_categorys(modelid, id) {
        if(modelid) {
            $.get('', {m: 'special', c: 'special', a: 'public_categorys_list', modelid: modelid, catid: id, pc_hash: pc_hash }, function(data){
                if(data) {
                    $('#catids').html(data);
                    $('#title').show();
                } else {
                    $('#catids').html('');
                    $('#title').hide();
                }
            });
        }
    }
    select_categorys(<?php echo $this->input->get('modelid')?>, <?php echo $this->input->get('catid')?>);
    $(document).ready(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#typeid").formValidator({tipid:"msg_id",onshow:"<?php echo L('please_choose_type')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_choose_type')?>"});    
    });
    $("#myform").submit(function (){
        var str = 0;
        $("input[name='ids[]']").each(function() {
            if($(this).attr('checked')==true) str = 1;
        });
        if(str==0) {
            Dialog.alert('<?php echo L('choose_news')?>');
            return false;
        }
        return true;
    });
</script>