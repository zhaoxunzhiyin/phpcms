<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form action="?m=customfield&c=customfield&a=field_save" method="post" id="myform">
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
	<?php
	$i=1;
	foreach($root as $r){
	$on = ($i == 1) ? "on" : "";
	echo "<li id='tab_setting_{$i}' class='$on' onclick=\"SwapTab('setting','on','',{$count},{$i})\">{$r['description']}</li>";
	$i++;
	}
	?>
</ul>

<?php
$i = 1;
$j = 0;
foreach($root as $k => $r){ ?>
<div id="div_setting_<?php echo $i ?>" class="contentList pad-10 <?php if($i > 1) echo "hidden"?>">
<table class="table_form">
<?php if(is_array($filed_list[$r['id']])){ ?>
<?php foreach($filed_list[$r['id']] as $f){

	if($f['conf']['status'] == 1){
	?>
  <tr>
    <th><?php echo $f['description']?></th>
    <td class="y-bg">
	<input type="hidden" name="postdata[<?php echo $j ?>][id]" value="<?php echo $f['id'] ?>" />
	<?php if($f['conf']['textarea'] == 1){ ?>
	<textarea class="input-text" name="postdata[<?php echo $j ?>][val]"  cols="60" rows="3"><?php echo $f['val']?></textarea></td>
	<?php }else{ ?>
	<input class="input-text" name="postdata[<?php echo $j ?>][val]"  value="<?php echo $f['val']?>" style="width: 442px;" /></td>
	<?php } ?>
  </tr>
<?php }$j++;}} ?>
</table>
</div>
<?php $i++;} ?>

<div class="bk15"></div>
<input name="dosubmit" type="submit" value="<?php echo L('cm_save') ?>" class="button" />
</div>
</div>
</form>
</body>
<script type="text/javascript">

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