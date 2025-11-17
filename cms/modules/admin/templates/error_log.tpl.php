<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
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
                <div class="page-body">
<div class="right-card-box">
    <div class="row table-search-tool">
<form name="searchform" action="?m=admin&c=index&a=public_error_log" method="get" >
<input type="hidden" value="admin" name="m">
<input type="hidden" value="index" name="c">
<input type="hidden" value="public_error_log" name="a">
            <div class="col-md-12" style="padding-right: 0">
                <label>
                    <div class="input-group input-time date date-picker" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" name="time" value="<?php echo $time;?>">
                        <span class="input-group-btn">
                            <button class="btn default" type="button">
                                <i class="fa fa-calendar"></i>
                            </button>
                        </span>
                    </div>
                </label>
            </div>
            <div class="col-md-12">
                <label><button type="submit" class="btn blue btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=index&a=public_error_log_del&time=<?php echo $time;?>', '<?php echo L('你确定要清空当天记录吗？')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('清空')?></button></label>
            </div>
</form>
    </div>
<form name="myform" id="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="80" align="left"><?php echo L('编号');?></th>
            <th width="160" align="left"><?php echo L('时间');?></th>
            <th width="80" style="text-align: center;"><?php echo L('类型');?></th>
            <th align="left"><?php echo L('日志');?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($list)){
    foreach($list as $t){
?>   
    <tr>
    <td><?php echo $t['id'];?></td>
    <td><?php echo $t['time'];?></td>
    <td style="text-align: center"><?php echo $t['type'];?></td>
    <td><a href="javascript:show_file_code()"><?php echo $t['message'];?></a></td>
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
</div>
</body>
</html>
<script type="text/javascript">
<!--
function show_file_code() {
    openwinx('error','?m=admin&c=index&a=public_error_log_show&time=<?php echo $time;?>','查看文件','80%','80%');
}
//-->
</script>