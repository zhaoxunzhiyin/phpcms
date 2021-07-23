<?php
defined('IN_CMS') or exit('No permission resources.');
$show_header = $show_scroll = 1;
include $this->admin_tpl('header','attachment');
?>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<style type="text/css">
img{max-width: 180px;max-height: 180px;border:none;}
body .table-list tr>td:first-child, body .table-list tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="pad-lr-10">
<div class="table-list">
<table width="100%" cellspacing="0" id="imgPreview">
<tr>
<td align="left"><?php echo L("local_dir")?>：<?php echo $local?></td>
</tr>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left"><a href="<?php echo '?m=attachment&c=attachments&a=album_dir&args='.$this->input->get('args').'&dir='.stripslashes(dirname($dir))?>"><img src="<?php echo IMG_PATH?>folder-closed.gif" /><?php echo L("parent_directory")?></td></a>
</tr>
<?php endif;?>
<?php 
if(is_array($list)):
	foreach($list as $v):
	$filename = basename($v);
?>
<tr>
<?php if (is_dir($v)) {
	echo '<td align="left"><img src="'.IMG_PATH.'folder-closed.gif" /> <a href="?m=attachment&c=attachments&a=album_dir&args='.$this->input->get('args').'&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? stripslashes($this->input->get('dir')).'/' : '').$filename.'"><b>'.$filename.'</b></a></td>';
} else {
	echo '<td align="left" onclick="javascript:album_cancel(this)"><img src="'.file_icon($filename,'gif').'" /> <a href="javascript:;" rel="'.$url.$filename.'" name="'.file_name($filename).'"';
	if (is_image(CMS_PATH.$local.'/'.$filename)) {
		echo ' onmouseover="layer.tips(\'<img src='.$url.$filename.'>\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';
	}
	echo '>'.$filename.'</a><span style="float: right;">'.format_file_size(filesize(CMS_PATH.$local.'/'.$filename)).'</span></td>';
}?>
</tr>
<?php 
	endforeach;
endif;
?>
</table>
</div>
</div>
</body>
<script type="text/javascript">
$(document).ready(function(){
	set_status_empty();
});	
function set_status_empty(){
	parent.window.$('#att-status').html('');
	parent.window.$('#att-name').html('');
}
function album_cancel(obj){
	var src = $(obj).children("a").attr("rel");
	var filename = $(obj).children("a").attr("name");
	if($(obj).hasClass('on')){
		$(obj).removeClass("on");
		var imgstr = parent.window.$("#att-status").html();
		var length = $("a[class='on']").children("a").length;
		var strs = filenames = '';
		for(var i=0;i<length;i++){
			strs += '|'+$("a[class='on']").children("a").eq(i).attr('rel');
			filenames += '|'+$("a[class='on']").children("a").eq(i).attr('name');
		}
		parent.window.$('#att-status').html(strs);
		parent.window.$('#att-name').html(filenames);
	} else {
		var num = parent.window.$('#att-status').html().split('|').length;
		var file_upload_limit = '<?php echo $file_upload_limit?>';
		if(num > file_upload_limit) {
			Dialog.alert('不能选择超过'+file_upload_limit+'个附件');
		}else{
			$(obj).addClass("on");
			parent.window.$('#att-status').append('|'+src);
			parent.window.$('#att-name').append('|'+filename);
		}
	}
}
</script>
</html>