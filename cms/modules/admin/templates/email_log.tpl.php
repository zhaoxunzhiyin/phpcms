<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('邮件发送失败时返回的错误代码，格式为：时间 [邮件服务器 - 服务器账号 - 发送给的邮箱] 错误代码');?></p>
</div>
<div class="right-card-box">
<form name="myform" id="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
    <tbody>
 <?php 
if(is_array($list)){
    foreach($list as $t){
?>   
    <tr>
    <td style="text-align:left;padding: 10px;"><?php echo $t;?></td>
    </tr>
<?php 
    }
}
?>
    </tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
<?php if($list){?>
    <div class="col-md-5 col-sm-5 table-footer-button">
        <label><button type="button" onclick="ajax_option('?m=admin&c=index&a=public_email_log_del', '<?php echo L('你确定要清空全部记录吗？')?>')" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('清空全部')?></button></label>
    </div>
<?php }?>
    <div class="col-md-7 col-sm-7 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>