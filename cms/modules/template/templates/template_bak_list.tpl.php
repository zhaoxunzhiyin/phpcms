<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th><?php echo L('time')?></th>
        <th><?php echo L('who')?></th>
        <th width="150"><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center"><?php echo format::date($v['creat_at'], 1)?></td>
<td align="center"><?php echo $v['username']?></td>
<td align="center"><a href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('are_you_sure_you_want_to_restore')?>',function(){redirect('?m=template&c=template_bak&a=restore&id=<?php echo $v['id']?>&style=<?php echo $this->style?>&dir=<?php echo $this->dir?>&filename=<?php echo $this->filename?>&pc_hash='+pc_hash);});" class="btn btn-xs green"> <i class="fa fa-reply"></i> <?php echo L('restore')?></a> <a href="javascript:void(0);" onclick="Dialog.confirm('<?php echo L('confirm', array('message'=>format::date($v['creat_at'], 1)))?>',function(){redirect('?m=template&c=template_bak&a=del&id=<?php echo $v['id']?>&style=<?php echo $this->style?>&dir=<?php echo $this->dir?>&filename=<?php echo $this->filename?>&pc_hash='+pc_hash);});" class="btn btn-xs red"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a></td>
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
</div>
</div>
</div>
</div>
</body>
</html>