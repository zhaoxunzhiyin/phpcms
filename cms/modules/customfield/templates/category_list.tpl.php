<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form action="?m=customfield&c=customfield&a=category_save" method="post" id="myform">
<input type="hidden" value="<?php echo $siteid ?>" name="siteid" />
<div class="pad-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		标签：{php $allFields = customField();} {php $cm = $allFields[$siteid];} {$cm['变量名']}
		</div>
		</td>
		</tr>
    </tbody>
</table>
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
			<div class="explain-col"> 
				<?php echo L('cm_site')?>: &nbsp;&nbsp;
				<?php
					if(is_array($sitelist)){
					foreach($sitelist as $site){
				?>
					<a href="?m=customfield&c=customfield&a=category_list&siteid=<?php echo $site['siteid'];?>&menuid=<?php echo intval($_GET['menuid']) ?>"><?php echo $site['name'];?></a>&nbsp;
				<?php }}?>
			</div>
		</td>
		</tr>
    </tbody>
</table>

<div class="col-tab">
<div class="contentList pad-10 ">
<div class="table-list" id="listtable">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="center"><?php echo L('listorder')?></th>
			<th><?php echo L('cm_cate_name')?></th>
			<th align="center"><?php echo L('cm_status')?></th>
			<th align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
	$j = 1;
	foreach($cate_list as $f){
?>
	<tr id="tr<?php echo $j ?>">
		<td align="center">

			<input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j ?>][id]" />
			<input type="hidden" value="1" name="postdata[<?php echo $j ?>][options]" class="dataoptions" />
			<input name="postdata[<?php echo $j ?>][listorder]" type='text' size='3' value='<?php echo $f['listorder']?>' class="input-text-c" />
		</td>
		<td align="center"><input name="postdata[<?php echo $j ?>][description]" type='text' value='<?php echo $f['description']?>' class="input-text" style="width:200px" /></td>
		<td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
		<td align="center"><a href="javascript:;" onclick="delTr('tr<?php echo $j ?>')"><?php echo L('delete');?></a></td>
	</tr>
<?php $j++;} ?>

<tr>
<td colspan="6" align="center"><input type="button" value=" <?php echo L('cm_add');?> " class="button" onclick="addTr()" /></td>
</tr>

</tbody>
</table>
</div>

</div>

<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('cm_save');?>" class="button" />
</div>

</div>
</form>
</body>
<script type="text/javascript">
	//添加行
	var addnum = <?php echo $j+1 ?>;
	function addTr(){
	var trHtml   =	"<tr id='ntr" + addnum + "'>";
		trHtml  +=	"<td align='center'>";
		trHtml  +=	"<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
		trHtml  +=	"<input name='postdata[" + addnum + "][listorder]' type='text' size='3' value='0' class='input-text-c' />";
		trHtml  +=	"</td>";
		trHtml  +=	"<td align='center'><input name='postdata[" + addnum + "][description]' type='text' value='' class='input-text' style='width:200px' /></td>";
		trHtml  +=	"<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
		trHtml  +=	"<td align='center'><a href='javascript:;' onclick=\"delTr('ntr"+ addnum +"')\"><?php echo L('delete');?></a></td>";
		trHtml  +=	"</tr>";
	addnum++;
	var $tr=$("#listtable tr").eq(-2);
	if($tr.size() <= 1){
		$tr=$("#listtable tr").eq(-1);
		$tr.before(trHtml);
	}else{$tr.after(trHtml);}
	}
	
	//删除行
	function delTr(trid){
		$("#"+trid).hide();
		$("#"+trid+" .dataoptions").val(3);
	}

	//切换选项卡
	function SwapTab(name,cls_show,cls_hide,cnt,cur){
	    for(i=1;i<=cnt;i++){
			if(i==cur){
				 $('#div_'+name+'_'+i).show();
				 $('#tab_'+name+'_'+i).attr('class',cls_show);
			}else{
				 $('#div_'+name+'_'+i).hide();
				 $('#tab_'+name+'_'+i).attr('class',cls_hide);
			}
		}
	}
</script>
</html>