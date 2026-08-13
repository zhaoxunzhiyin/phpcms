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
<input type="hidden" value="import" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $this->input->get('specialid')?>" name="specialid">
<div class="col-md-12 col-sm-12">
             <label><?php echo $model_form?></label>
            <label id="catids"></label>
</div>
<div class="col-md-12 col-sm-12">
            <label><?php echo L('keyword')?></label>
            <label><i class="fa fa-caret-right"></i></label>
            <label><input type='text' name="key" id="key" value="<?php echo $this->input->get('key');?>" size="25"></label>
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
</div>
<form name="myform" id="myform" action="?m=special&c=special&a=import&specialid=<?php echo $this->input->get('specialid')?>=" method="post">
<input name="modelid" type="hidden" value="<?php echo $this->input->get('modelid')?>">
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
            <th class="myselect table-checkable">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
            <th width="80"><?php echo L('listorder')?></th>
            <th><?php echo L('content_title')?></th>
            </tr>
        </thead>
<tbody>
    <?php if(is_array($data)) { foreach ($data as $r) {?>
        <tr>
        <td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name='ids[]' value="<?php echo $r['id'];?>" />
                        <span></span>
                    </label></td>
        <td align='center'><input name='listorder[<?php echo $r['id'];?>]' type='text' value='<?php echo $r['listorder'];?>' class='displayorder form-control input-sm input-inline input-mini'></td>
        <td><?php echo $r['title'];?></td>
    </tr>
     <?php } }?>
</tbody>
     </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><?php echo form::select($types, '', 'name="typeid" id="typeid"', L('please_choose_type'))?><span id="msg_id"></span></label>
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
    function select_categorys(modelid, id) {
        if(modelid) {
            $.get('', {m: 'special', c: 'special', a: 'public_categorys_list', modelid: modelid, catid: id, pc_hash: pc_hash }, function(data){
                if(data) {
                    $('#catids').html(data);
                } else $('#catids').html('');
            });
        }
    }
    select_categorys(<?php echo $this->input->get('modelid')?>, <?php echo $this->input->get('catid')?>);
    $(document).ready(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#typeid").formValidator({tipid:"msg_id",onshow:"<?php echo L('please_choose_type')?>",oncorrect:"<?php echo L('true')?>"}).inputValidator({min:1,onerror:"<?php echo L('please_choose_type')?>"});    
    });
    $("#myform").submit(function (){
        var ids='';
        $("input[name='ids[]']:checked").each(function() {
            ids += $(n).val() + ',';
        });
        if(ids=='') {
            Dialog.alert('<?php echo L('choose_news')?>');
            return false;
        }
        return true;
    });
</script>