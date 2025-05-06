<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="80">Siteid</th>
        <th><?php echo L('site_name')?></th>
        <th><?php echo L('site_dirname')?></th>
        <th><?php echo L('site_domain')?></th>
        <th><?php echo L('mobile_domain')?></th>
        <th><?php echo L('godaddy')?></th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
        <tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td><?php echo $v['siteid'];?></td>
<td align="center"><?php echo $v['name'];?></td>
<td align="center"><?php echo $v['dirname'];?></td>
<td align="center"><a href="<?php echo $v['domain'];?>" target="_blank"><?php echo $v['domain'];?></a></td>
<td align="center"><a href="<?php echo $v['mobile_domain'];?>" target="_blank"><?php echo $v['mobile_domain'];?></a></td>
<td align="center"><?php if ($v['siteid']!=1){echo WEB_PATH.$v['dirname'];}else{echo WEB_PATH;}?></td>
<td><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['siteid'];?>, '<?php echo new_addslashes(new_html_special_chars($v['name']));?>')"><?php echo L('edit');?></a>
<?php if($v['siteid']!=1) { ?><a class="btn btn-xs red" href="javascript:void(0);" onclick="Dialog.confirm('<?php echo new_addslashes(new_html_special_chars(L('confirm', array('message'=>$v['name']))));?>',function(){redirect('?m=admin&c=site&a=del&siteid=<?php echo $v['siteid'];?>&pc_hash='+pc_hash);});"><?php echo L('delete');?></a><?php } ?></td>
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
<script type="text/javascript">
<!--
function edit(id, name) {
    dr_iframe('edit','?m=admin&c=site&a=edit&siteid='+id+'&is_menu=1','80%','80%');
}
//-->
</script>
</body>
</html>