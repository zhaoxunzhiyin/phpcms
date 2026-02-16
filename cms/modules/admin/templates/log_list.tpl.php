<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
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
<form name="searchform" action="?m=admin&c=log&a=init&menuid=<?php echo $this->input->get('menuid');?>" method="get" >
<input type="hidden" value="admin" name="m">
<input type="hidden" value="log" name="c">
<input type="hidden" value="init" name="a">
<input type="hidden" name="dosubmit" value="1">
        <div class="col-md-12 col-sm-12">
        <label><?php echo L('module')?></label>
        <label><i class="fa fa-caret-right"></i></label>
        <?php echo form::select($module_arr,'','name="search[module]"',$default)?>
        </div>
        <div class="col-md-12 col-sm-12">
        <label><?php echo L('username')?></label>
        <label><i class="fa fa-caret-right"></i></label>
        <label><input type="text" value="" class="form-control" name="search[username]" size='10'></label>
        </div>
        <div class="col-md-12 col-sm-12">
        <label><div class="formdate">
            <div class="input-group input-medium date-picker input-daterange">
                <input type="text" class="form-control" value="<?php echo $start_time;?>" name="search[start_time]">
                <span class="input-group-addon"> <?php echo L('to')?> </span>
                <input type="text" class="form-control" value="<?php echo $end_time;?>" name="search[end_time]">
            </div>
        </div></label>
        </div>
        <div class="col-md-12 col-sm-12">
        <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('determine_search')?></button></label>
        <label><button type="button" onclick="ajax_option('?m=admin&c=log&a=delete&week=4&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo dr_get_csrf_token();?>', '<?php echo L('你确定要删除一月前记录吗？')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('removed_data')?></button></label>
        <label><button type="button" onclick="ajax_option('?m=admin&c=log&a=delete&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo dr_get_csrf_token();?>', '<?php echo L('你确定要清空全部记录吗？')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('清空')?></button></label>
        </div>
</form>
</div>
<form name="myform" id="myform" action="?m=admin&c=log&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
 <table width="100%" cellspacing="0">
        <thead>
            <tr>
             <th width="80"><?php echo L('username')?></th>
            <th><?php echo L('module')?></th>
            <th><?php echo L('file')?></th>
             <th width="180"><?php echo L('time')?></th>
             <th>IP</th>
            </tr>
        </thead>
    <tbody>
 <?php
if(is_array($infos)){
    foreach($infos as $info){
?>
    <tr> 
        <td align="center"><?php echo $info['username'] ? $info['username'] : L('游客');?></td>
        <td align="center"><?php echo $info['module']?></td>
        <td align="left" title="<?php echo $info['querystring']?>"><?php echo str_cut($info['querystring'], 40);?></td>
         <td align="center"><?php echo dr_date(strtotime($info['time']), null, 'red');//echo $info['lastusetime'] ? date('Y-m-d H:i', $info['lastusetime']):''?></td>
         <td align="center"><?php echo $info['ip']?>　</td> 
    </tr>
<?php
    }
}
?></tbody>
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
function checkuid() {
    var ids='';
    $("input[name='logid[]']:checked").each(function(i, n){
        ids += $(n).val() + ',';
    });
    if(ids=='') {
        Dialog.alert('<?php echo L('select_operations')?>');
        return false;
    } else {
        myform.submit();
    }
}
</script>
 