<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<style type="text/css">
body .table-list table tr>td:first-child, body .table-list table tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="row table-search-tool">
<form name="searchform" action="" method="get" >
<input type="hidden" value="content" name="m">
<input type="hidden" value="content" name="c">
<input type="hidden" value="public_relationlist" name="a">
<input type="hidden" name="dosubmit" value="1">
<input type="hidden" value="<?php echo $modelid;?>" name="modelid">
        <input type="hidden" value="<?php echo dr_get_csrf_token();?>" name="pc_hash">
        <div class="col-xs-12">
        <?php echo form::select_category('',(isset($catid) && $catid ? $catid : 0),'name="catid"',L('please_select_category'),$modelid,0,1);?>
        <label><select name="field">
            <option value='title' <?php if($this->input->get('field')=='title') echo 'selected';?>><?php echo L('title');?></option>
            <option value='keywords' <?php if($this->input->get('field')=='keywords') echo 'selected';?> ><?php echo L('keywords');?></option>
            <option value='description' <?php if($this->input->get('field')=='description') echo 'selected';?>><?php echo L('description');?></option>
            <option value='id' <?php if($this->input->get('field')=='id') echo 'selected';?>>ID</option>
        </select></label>
        <label><i class="fa fa-caret-right"></i></label>
        <label><input name="keywords" type="text" value="<?php echo $this->input->get('keywords')?>" class="form-control" /></label>
        <label><button type="submit" class="btn blue btn-sm onloading"><i class="fa fa-search"></i> <?php echo L('search');?></button></label>
        </div>
        </form>
    </div>
<form class="form-horizontal" name="myform" id="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
            <tr>
            <th ><?php echo L('title');?></th>
            <th width="100"><?php echo L('belong_category');?></th>
            <th width="100"><?php echo L('addtime');?></th>
            </tr>
        </thead>
    <tbody>
    <?php foreach($infos as $r) { ?>
    <tr onclick="select_list(this,'<?php echo safe_replace($r['title']);?>',<?php echo $r['id'];?>)" class="cu" onmouseover="layer.tips('<?php echo L('click_to_select');?>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();">
        <td align='left'><a href="javascript:;" class="tooltips" data-container="body" data-placement="top" data-original-title="<?php echo safe_replace($r['title']);?>" title="<?php echo safe_replace($r['title']);?>"><?php echo $r['title'];?></a></td>
        <td align='center'><?php echo $this->categorys[$r['catid']]['catname'];?></td>
        <td align='center'><?php echo format::date($r['inputtime']);?></td>
    </tr>
     <?php }?>
        </tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
<style type="text/css">
.line_ff9966,.line_ff9966:hover td{
    background-color:#FF9966;
}
.line_fbffe4,.line_fbffe4:hover td {
    background-color:#fbffe4;
}
</style>
<script>
function select_list(obj,title,id) {
    var relation_ids = dialogOpener.$('#relation').val();
    var sid = 'v<?php echo $modelid;?>'+id;
    if($(obj).attr('class')=='line_ff9966' || $(obj).attr('class')==null) {
        $(obj).attr('class','line_fbffe4');
        dialogOpener.$('#'+sid).remove();
        if(relation_ids !='' ) {
            var r_arr = relation_ids.split('|');
            var newrelation_ids = '';
            $.each(r_arr, function(i, n){
                if(n!=id) {
                    if(i==0) {
                        newrelation_ids = n;
                    } else {
                     newrelation_ids = newrelation_ids+'|'+n;
                    }
                }
            });
            dialogOpener.$('#relation').val(newrelation_ids);
        }
    } else {
        $(obj).attr('class','line_ff9966');
        var str = "<li id='"+sid+"'>·<span>"+title+"</span><a href='javascript:;' class='close' onclick=\"remove_relation('"+sid+"',"+id+")\"></a></li>";
        if(dialogOpener.$("#"+sid).length>0) {
            dr_tips(0, "<?php echo L('已经存在');?>");
            return;
        }
        dialogOpener.$('#relation_text').append(str);
        if(relation_ids =='' ) {
            dialogOpener.$('#relation').val(id);
        } else {
            relation_ids = relation_ids+'|'+id;
            dialogOpener.$('#relation').val(relation_ids);
        }
    }
}
</script>
</div>
</div>
</body>
</html>