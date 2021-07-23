<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = $show_header = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="subnav">
    <div class="content-menu ib-a blue line-x">
    <?php if(isset($big_menu)) echo '<a class="add fb" href="'.$big_menu[0].'"><em>'.$big_menu[1].'</em></a>　';?>
    <?php echo admin::submenu($_GET['menuid'],$big_menu); ?><span>|</span><a href="javascript:artdialog('setting','?m=poster&c=space&a=setting','<?php echo L('module_setting')?>',540,320);void(0);"><em><?php echo L('module_setting')?></em></a>
    </div>
</div>
<div class="pad-lr-10">
<form name="myform" action="?m=poster&c=space&a=delete" method="post" id="myform">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="6%" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('spaceid[]');" />
                        <span></span>
                    </label></th>
			<th><?php echo L('boardtype')?></th>
			<th width="12%" align="center"><?php echo L('ads_type')?></th>
			<th width='10%' align="center"><?php echo L('size_format')?></th>
			<th width="10%" align="center"><?php echo L('ads_num')?></th>
			<th align="center" width="13%"><?php echo L('description')?></th>
			<th width="28%" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($infos)){
	foreach($infos as $info){
?>   
	<tr>
	<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="spaceid[]" value="<?php echo $info['spaceid']?>" />
                        <span></span>
                    </label></td>
	<td><?php echo $info['name']?></td>
	<td align="center"><?php echo $TYPES[$info['type']]?></td>
	<td align="center"><?php echo $info['width']?>*<?php echo $info['height']?></td>
	<td align="center"><?php echo $info['items']?></td>
	<td align="center"><?php echo $info['description']?></td>
	<td align="center">
	<a href="?m=poster&c=space&a=public_preview&spaceid=<?php echo $info['spaceid']?>" target="_blank"><?php echo L('preview')?></a> | <a href="javascript:call(<?php echo $info['spaceid']?>);void(0);"><?php echo L('get_code')?></a> | <a href='?m=poster&c=poster&a=init&spaceid=<?php echo $info['spaceid']?>&menuid=<?php echo $_GET['menuid']?>' ><?php echo L('ad_list')?></a> | 
	<a href="###" onclick="edit(<?php echo $info['spaceid']?>, '<?php echo addslashes(new_html_special_chars($info['name']))?>')" title="<?php echo L('edit')?>" ><?php echo L('edit')?></a> | 
	<a href='###' onClick="Dialog.confirm('<?php echo L('confirm', array('message' => addslashes(new_html_special_chars($info['name']))))?>',function(){redirect('?m=poster&c=space&a=delete&spaceid=<?php echo $info['spaceid']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a>
	| <a href="index.php?m=poster&c=poster&a=add&spaceid=<?php echo $info['spaceid']?>&menuid=<?php echo $_GET['menuid']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>">添加广告</a>
	</td>
	</tr>
<?php 
	}
}
?>
</tbody>
    </table>
    <div class="btn"><label for="check_box"><?php echo L('selected_all')?>/<?php echo L('cancel')?></label>
		<input name="button" type="button" class="button" value="<?php echo L('remove_all_selected')?>" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){$('#myform').submit();});">&nbsp;&nbsp;</div>  </div>
 <div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name){
	artdialog('testIframe'+id,'?m=poster&c=space&a=edit&spaceid='+id,'<?php echo L('edit_space')?>--'+name,540,320);
};
function call(id) {
	omnipotent('call','?m=poster&c=space&a=public_call&sid='+id,'<?php echo L('get_code')?>',1,600,470);
}
//-->
</script>
</body>
</html>