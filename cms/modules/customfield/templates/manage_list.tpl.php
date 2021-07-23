<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form action="?m=customfield&c=customfield&a=manage_save" method="post" id="myform">
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
		<td><div class="explain-col"> 
		<?php echo L('cm_site') ?>: &nbsp;&nbsp;
		<?php
			if(is_array($sitelist)){
			foreach($sitelist as $site){
		?>
			<a href="?m=customfield&c=customfield&a=manage_list&siteid=<?php echo $site['siteid'];?>&menuid=<?php echo intval($_GET['menuid']) ?>"><?php echo $site['name'];?></a>&nbsp;
		<?php }}?>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<?php if(count($root) > 0){ ?>
<div class="col-tab">
<ul class="tabBut cu-li">
<?php
	$i = 1;
	$j = 0;
	foreach($root as $r){
		$on = ($i == 1) ? "on" : "";
		echo "<li id='tab_setting_{$i}' class='$on' onclick=\"SwapTab('setting','on','',{$count},{$i})\">{$r['description']}</li>";
		$i++;
	}
?>
</ul>

<?php
$i = 1;
foreach($root as $k => $r){ ?>
<div id="div_setting_<?php echo $i ?>" class="contentList pad-10 <?php if($i > 1) echo "hidden"?>">
<div class="table-list">
<table width="100%" cellspacing="0" id="listtable<?php echo $r['id'] ?>">
	<thead>
		<tr>
			<th align="center"><?php echo L('listorder')?></th>
			<th><?php echo L('cm_field') ?></th>
			<th align="center"><?php echo L('cm_value') ?></th>
			<th align="center"><?php echo L('cm_description') ?></th>
			<th align="center"><?php echo L('cm_status') ?></th>
			<th align="center"><?php echo L('cm_textarea') ?></th>
			<th align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php if(is_array($filed_list[$r['id']])){ ?>
<?php foreach($filed_list[$r['id']] as $f){ ?>
	<tr id="tr<?php echo $j ?>">
		<td align="center">
		<input type="hidden" value="<?php echo $f['pid'] ?>" name="postdata[<?php echo $j ?>][pid]" />
		<input type="hidden" value="<?php echo $f['id'] ?>" name="postdata[<?php echo $j ?>][id]" />
		<input type="hidden" value="1" name="postdata[<?php echo $j ?>][options]" class="dataoptions" />
		<input name="postdata[<?php echo $j ?>][listorder]" type='text' size='3' value='<?php echo $f['listorder']?>' class="input-text-c" />
		</td>
		<td align="center"><input name="postdata[<?php echo $j ?>][name]" type='text' value='<?php echo $f['name']?>' class="input-text" style="width:200px" /></td>
		<td align="center"><textarea name="postdata[<?php echo $j ?>][val]" class="input-text" style="width:400px" rows="2"><?php echo $f['val']?></textarea></td>
		<td align="center"><input name="postdata[<?php echo $j ?>][description]" type='text' value='<?php echo $f['description']?>' class="input-text" style="width:200px" /></td>
		<td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][status]" value="1" <?php if($f['conf']['status'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
		<td align="center"><label class="mt-checkbox mt-checkbox-outline"><input type="checkbox" name="postdata[<?php echo $j ?>][conf][textarea]" value="1" <?php if($f['conf']['textarea'] == 1) echo " checked='checked'"; ?>  /><span></span></label></td>
		<td align="center"><a href="javascript:;" onclick="delTr('tr<?php echo $j ?>')"><?php echo L('delete');?></a></td>
	</tr>
<?php $j++;}} ?>
<tr><td colspan="7" align="center"><input type="button" value=" <?php echo L('cm_add')?> " class="button" onclick="addTr(<?php echo $r['id'] ?>)" /></td></tr>
</tbody>
</table>
</div>

</div>
<?php $i++;} ?>
<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('cm_save')?>" class="button" />
</div>
<?php }else{echo "<div style='text-align:center;padding-top:30px;color:#999'>". L('cm_no_data') ."</div>";} ?>
</div>
</form>
</body>
<script type="text/javascript">
	var addnum = <?php echo $j+1 ?>;
	//添加行
	function addTr(pid){
	var trHtml   =	"<tr id='ntr" + addnum + "'>";
		trHtml  +=	"<td align='center'>";
		trHtml  +=	"<input type='hidden' value='"+ pid +"' name='postdata[" + addnum + "][pid]' />";
		trHtml  +=	"<input type='hidden' value='2' name='postdata[" + addnum + "][options]' class='dataoptions' />";
		trHtml  +=	"<input name='postdata[" + addnum + "][listorder]' type='text' size='3' value='0' class='input-text-c' />";
		trHtml  +=	"</td>";
		trHtml  +=	"<td align='center'><input name='postdata[" + addnum + "][name]' type='text' value='' class='input-text' style='width:200px' /></td>";
		trHtml  +=	"<td align='center'><textarea name='postdata[" + addnum + "][val]' class='input-text' style='width:400px' rows='2'></textarea></td>";
		trHtml  +=	"<td align='center'><input name='postdata[" + addnum + "][description]' type='text' value='' class='input-text' style='width:200px' /></td>";
		trHtml  +=	"<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][status]' value='1' checked='checked' /><span></span></label></td>";
		trHtml  +=	"<td align='center'><label class='mt-checkbox mt-checkbox-outline'><input type='checkbox' name='postdata[" + addnum + "][conf][textarea]' value='1' /><span></span></label></td>";
		trHtml  +=	"<td align='center'><a href='javascript:;' onclick=\"delTr('ntr"+ addnum +"')\"><?php echo L('delete');?></a></td>";
		trHtml  +=	"</tr>";
	addnum++;
	var $tr=$("#listtable"+ pid +" tr").eq(-2);
	if($tr.size() <= 1){
		$tr=$("#listtable"+ pid +" tr").eq(-1);
		$tr.before(trHtml);
	}else{$tr.after(trHtml);}
	}
	//删除行
	function delTr(trid){
		$("#"+trid).hide();
		$("#"+trid+" .dataoptions").val(3);
	}
	//切换分类选项卡
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