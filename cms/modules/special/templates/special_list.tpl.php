<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<form name="myform" id="myform" action="?m=special&c=special&a=listorder" method="post">
    <table width="100%" cellspacing="0" class="table-list nHover">
        <thead>
            <tr>
            <th width="40" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
			<th width="40" align="center">ID</th>
			<th width="80" align="center"><?php echo L('listorder')?></th>
			<th ><?php echo L('special_info')?></th>
			<th width="160"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
        <tbody>
 <?php 
if(is_array($infos)){
	foreach($infos as $info){
?>   
	<tr>
	<td align="center" width="40" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $r['id'];?>" />
                        <span></span>
                    </label></td>
	<td width="40" align="center"><?php echo $info['id']?></td>
	<td width="80" align="center"><input type='text' name='listorder[<?php echo $info['id']?>]' value="<?php echo $info['listorder']?>" class="input-text-c" size="4"></td>
	<td>
    <div class="col-left mr10" style="width:146px; height:112px"><?php if ($info['thumb']) {?>
<a href="<?php echo $info['url']?>" target="_blank"><img src="<?php echo $info['thumb']?>" width="146" height="112" style="border:1px solid #eee" align="left"></a><?php }?>
</div>
<div class="col-auto">  
    <h2 class="title-1 f14 lh28 mb6 blue"><a href="<?php echo $info['url']?>" target="_blank"><?php echo $info['title']?></a></h2>
    <div class="lh22"><?php echo $info['description']?></div>
<p class="gray4"><?php echo L('create_man')?>???<a href="#" class="blue"><?php echo $info['username']?></a>??? <?php echo L('create_time')?>???<?php echo format::date($info['createtime'], 1)?></p>
</div>
	</td>
	<td align="center"><span style="height:22"><a href='?m=special&c=content&a=init&specialid=<?php echo $info['id']?>' onclick="javascript:openwinx('?m=special&c=content&a=add&specialid=<?php echo $info['id']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>','')"><?php echo L('add_news')?></a></span> | 
<span style="height:22"><a href='javascript:import_c(<?php echo $info['id']?>);void(0);'><?php echo L('import_news')?></a></span><br />
<span style="height:22"><a href='?m=special&c=content&a=init&specialid=<?php echo $info['id']?>'><?php echo L('manage_news')?></a></span> | 
<span style="height:22"><a href='?m=special&c=template&specialid=<?php echo $info['id']?>' style="color:red" target="_blank"><?php echo L('template_manage')?></a></span><br/>
<span style="height:22"><a href='?m=special&c=special&a=elite&value=<?php if($info['elite']==0) {?>1<?php } elseif($info['elite']==1) { ?>0<?php }?>&id=<?php echo $info['id']?>'><?php if($info['elite']==0) { echo L('elite_special'); } else {?><font color="red"><?php echo L('remove_elite')?></font><?php }?></a></span> | 
<span style="height:22"><a href="javascript:comment('<?php echo id_encode('special', $info['id'], $this->get_siteid())?>', '<?php echo addslashes(new_html_special_chars($info['title']))?>');void(0);"><?php echo L('special_comment')?></a></span><br/>
<span style="height:22"><a href="?m=special&c=special&a=edit&specialid=<?php echo $info['id']?>&menuid=<?php echo $_GET['menuid']?>"><?php echo L('edit_special')?></a></span> | 
<span style="height:22"><a href="###" onclick="Dialog.confirm('<?php echo L('confirm', array('message'=>addslashes(new_html_special_chars($info['title']))))?>',function(){redirect('?m=special&c=special&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"><?php echo L('del_special')?></a></span></td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
  
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
        <input name='dosubmit' type='submit' class="button" value='<?php echo L('listorder')?>'>&nbsp;
        <input type="button" class="button" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=special&c=special&a=delete';$('#myform').submit();});"/>
        &nbsp;<input type="submit" class="button" value="<?php echo L('update')?>html" onclick="document.myform.action='?m=special&c=special&a=html'"/></div>
 <div id="pages"><?php echo $this->db->pages;?></div><script>window.top.$("#display_center_id").css("display","none");</script>
</form>
</div>
</body>
</html>
<script type="text/javascript">
<!--
function edit(id, name) {
	artdialog('edit','?m=special&c=special&a=edit&specialid='+id,'<?php echo L('edit_special')?>--'+name,700,500);
}

function comment(id, name) {
	artdialog('comment','?m=comment&c=comment_admin&a=lists&show_center_id=1&commentid='+id,'<?php echo L('see_comment')?>???'+name,700,500);
}

function import_c(id) {
	artdialog('import','?m=special&c=special&a=import&specialid='+id,'<?php echo L('import_news')?>--',700,500);
}

</script>