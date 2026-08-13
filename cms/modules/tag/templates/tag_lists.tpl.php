<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form id="myform" name="myform" action="" method="post">
<input type="hidden" name="m" value="tag" />
<input type="hidden" name="c" value="tag" />
<input type="hidden" name="a" value="del" />
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th><?php echo L('name')?></th>
        <th><?php echo L('stdcall')?></th>
        <th><?php echo L('stdcode')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center"><?php echo $v['name']?></td>
<td align="center"><?php switch($v['type']){case 0:echo L('model_configuration');break;case 1:echo L('custom_sql');break;case 2:echo L('block');}?></td>
<td align="center"><textarea ondblclick="copy_text(this)" style="width: 100%;height:30px" /><?php echo new_html_special_chars($v['tag'])?></textarea></td>
</tr>
<?php 
    endforeach;
endif;
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
<script type="text/javascript">
<!--
function copy_text(matter){
    matter.select();
    js1=matter.createTextRange();
    js1.execCommand("Copy");
    Dialog.alert('<?php echo L('copy_code');?>');
}
//-->
</script>
</body>
</html>