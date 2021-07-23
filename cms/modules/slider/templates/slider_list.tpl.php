<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"> 
		位置: &nbsp;&nbsp;
		<?php
	if(is_array($type_arr)){
	foreach($type_arr as $typeid => $type){
		?><a href="?m=slider&c=slider&typeid=<?php echo $typeid;?>"><?php echo $type;?></a>&nbsp;
		<?php }}?>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<form name="myform" id="myform" action="?m=slider&c=slider&a=listorder" method="post" >
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('id[]');" />
                        <span></span>
                    </label></th>
			<th width="35" align="center"><?php echo L('listorder')?></th>
			<th><?php echo L('slider_name')?></th>
			<th width="25%" align="center"><?php echo L('image')?></th>
			<th width="10%" align="center"><?php echo L('url')?></th>
			<th width='10%' align="center"><?php echo L('typeid')?></th>
			<th width="8%" align="center"><?php echo L('status')?></th>
			<th width="13%" align="center"><?php echo L('slider_adddate')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="id[]" value="<?php echo $info['id']?>" />
                        <span></span>
                    </label></td>
		<td align="center" width="35"><input name='listorders[<?php echo $info['id']?>]' type='text' size='3' value='<?php echo $info['listorder']?>' class="input-text-c"></td>
		<td><?php if ($info['url']!="#" && $info['url']){?><a href="<?php echo $info['url'];?>" title="<?php echo $info['name']?>" target="_blank"><?php }?><?php echo $info['name']?><?php if ($info['url']!="#" && $info['url']){?></a><?php }?></td>
		<td align="center" width="12%"><a href="javascript:preview(<?php echo $info['id']?>, '<?php echo $info['name']?>','<?php echo $info['image']?>')" title="<?php echo $info['description'];?>"><img src="<?php echo $info['image'];?>" height=60></a></td>
		<td align="center" width="10%"><?php if ($info['url']!="#" && $info['url']){?><a href="<?php echo $info['url'];?>" target="_blank">点击查看</a><?php }else{?>无<?php }?></td>
		<td align="center" width="10%"><?php echo $type_arr[$info['typeid']];?></td>
		<td align="center"><?php if($info['isshow']=='0'){ echo "不显示";}else{echo "显示";}?></td>
		<td  align="center"><?php echo date("Y-m-d",$info['addtime']);?></td>
		<td align="center" width="12%"><a href="###"
			onclick="edit(<?php echo $info['id']?>, '<?php echo new_addslashes($info['name'])?>')"
			title="<?php echo L('edit')?>"><?php echo L('edit')?></a> |  <a
			href='###'
			onClick="Dialog.confirm('<?php echo L('confirm', array('message' => new_addslashes($info['name'])))?>',function(){redirect('?m=slider&c=slider&a=delete&id=<?php echo $info['id']?>&pc_hash='+pc_hash);});"><?php echo L('delete')?></a> 
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
</div>
<div class="btn"> 
<input name="dosubmit" type="submit" class="button"
	value="<?php echo L('listorder')?>">&nbsp;&nbsp;<input type="button" class="button" name="dosubmit" onClick="Dialog.confirm('<?php echo L('confirm', array('message' => L('selected')))?>',function(){document.myform.action='?m=slider&c=slider&a=delete';$('#myform').submit();});" value="<?php echo L('delete')?>"/></div>
<div id="pages"><?php echo $pages?></div>
</form>
</div>
<script type="text/javascript">

function edit(id, name) {
	artdialog('edit','?m=slider&c=slider&a=edit&id='+id,'<?php echo L('edit')?> '+name+' ',700,330);
}
function checkuid() {
	var ids='';
	$("input[name='id[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert("<?php echo L('before_select_operations')?>");
		return false;
	} else {
		myform.submit();
	}
}
//向下移动
function listorder_up(id) {
	$.get('?m=slider&c=slider&a=listorder_up&id='+id,null,function (msg) { 
	if (msg==1) { 
	//$("div [id=\'option"+id+"\']").remove(); 
		Dialog.alert('<?php echo L('move_success')?>');
	} else {
	Dialog.alert(msg); 
	} 
	}); 
} 

window.top.$('#display_center_id').css('display','none');
function preview(id, name,file) {
	if(IsImg(file)) {
        var width = '400px';
        var height = '300px';
        var att = 'height: 260px;';
        if (is_mobile()) {
            width = height = '90%';
            var att = 'height: 90%;';
        }
        var diag = new Dialog({
            title:name+'对应的幻灯图片',
            html:'<style type="text/css">a,a:hover{color: #337ab7; text-decoration:none;}</style><div style="'+att+'line-height: 24px;word-break: break-all;overflow: hidden auto;"><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><a href="'+file+'" target="_blank"><img style="max-width:100%" src="'+file+'"></a></p></div>',
            width:width,
            height:height,
            modal:true
        });
        diag.show();
	} else {
        var diag = new Dialog({
            title:'<?php echo L('preview')?>',
            html:'<style type="text/css">a,a:hover{color: #337ab7; text-decoration:none;}</style><p style="text-align: center;word-break: break-all;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><a href="'+file+'" target="_blank"><img src="<?php echo IMG_PATH?>admin_img/down.gif"><?php echo L('click_open')?></a></p>',
            modal:true
        });
        diag.show();
	}
}
function IsImg(url){
	  var sTemp;
	  var b=false;
	  var opt="jpg|gif|png|bmp|jpeg";
	  var s=opt.toUpperCase().split("|");
	  for (var i=0;i<s.length ;i++ ){
	    sTemp=url.substr(url.length-s[i].length-1);
	    sTemp=sTemp.toUpperCase();
	    s[i]="."+s[i];
	    if (s[i]==sTemp){
	      b=true;
	      break;
	    }
	  }
	  return b;
}
</script>
</body>
</html>