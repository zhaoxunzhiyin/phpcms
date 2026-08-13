<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<link href="<?php echo JS_PATH?>bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>bootstrap-switch/js/bootstrap-switch.min.js"></script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('主动推送的记录日志');?></p>
</div>
<div class="right-card-box">
<form action="?m=bdts&c=bdts&a=del&menuid=<?php echo $this->input->get('menuid');?>" class="form-horizontal" method="post" name="myform" id="myform">
    <div class="table-list">
        <table width="100%">
            <tbody>
            <?php 
            if(is_array($list)){
            foreach($list as $t){
            ?>
            <tr>
                <td><?php echo $t;?></td>
            </tr>
            <?php }}?>
            </tbody>
        </table>
    </div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 col-sm-5 table-footer-button">
        <label><button type="button" onClick="Dialog.confirm('<?php echo L('你确定要清空全部记录吗？')?>',function(){$('#myform').submit();});" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('清空全部')?></button></label>
    </div>
    <div class="col-md-7 col-sm-7 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>