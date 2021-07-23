<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">

<form name="myform" id="myform" action="?m=collection&c=node&a=del" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('nodeid[]');" />
                        <span></span>
                    </label></th>
			<th align="left">ID</th>
			<th align="left"><?php echo L('nodename')?></th>
			<th align="left"><?php echo L('lastdate')?></th>
			<th align="left"><?php echo L('content').L('operation')?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	foreach($nodelist as $k=>$v) {
?>
    <tr>
		<td align="left" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['nodeid']?>" name="nodeid[]" />
                        <span></span>
                    </label></td>
		<td align="left"><?php echo $v['nodeid']?></td>
		<td align="left"><?php echo $v['name']?></td>
		<td align="left"><?php echo format::date($v['lastdate'], 1)?></td>
		<td align="left"><a href="?m=collection&c=node&a=col_url_list&nodeid=<?php echo $v['nodeid']?>">[<?php echo L('collection_web_site')?>]</a> 
		<a href="?m=collection&c=node&a=col_content&nodeid=<?php echo $v['nodeid']?>">[<?php echo L('collection_content')?>]</a>
		 <a href="?m=collection&c=node&a=publist&nodeid=<?php echo $v['nodeid']?>&status=2" style="color:red">[<?php echo L('public_content')?>]</a>
		</td>
		<td align="left">
		<a href="javascript:void(0)" onclick="test_spider(<?php echo $v['nodeid']?>)">[<?php echo L('test')?>]</a>
		
		<a href="?m=collection&c=node&a=edit&nodeid=<?php echo $v['nodeid']?>&menuid=957">[<?php echo L('edit')?>]</a>
		 <a href="javascript:void(0)"  onclick="copy_spider(<?php echo $v['nodeid']?>)">[<?php echo L('copy')?>]</a>
		 <a href="?m=collection&c=node&a=export&nodeid=<?php echo $v['nodeid']?>">[<?php echo L('export')?>]</a>
		
		 </td>
    </tr>
<?php
	}

?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="button" class="button" name="dosubmit" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});" value="<?php echo L('delete')?>"/>
 <input type="button" class="button" value="<?php echo L('import_collection_points')?>" onclick="import_spider()" />
</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function test_spider(id) {
	var diag = new Dialog({
		id:'test',
		title:'<?php echo L('data_acquisition_testdat')?>',
		url:'<?php echo SELF;?>?m=collection&c=node&a=public_test&nodeid='+id+'&pc_hash='+pc_hash,
		width:700,
		height:500,
		modal:true
	});
	diag.onCancel=function() {
		$DW.close();
	};
	diag.show();
}

function copy_spider(id) {
	artdialog('test','?m=collection&c=node&a=copy&nodeid='+id,'<?php echo L('copy_node')?>',420,200);
}

function import_spider() {
	artdialog('test','?m=collection&c=node&a=node_import','<?php echo L('import_collection_points')?>',420,200);
}

window.top.$('#display_center_id').css('display','none');
//-->
</script>
</body>
</html>