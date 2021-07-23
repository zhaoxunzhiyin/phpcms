<?php 
$show_header = $show_validator = $show_scroll = 1; 
include $this->admin_tpl('header', 'attachment');
?>
<!--上传组件js-->
<script src="<?php echo JS_PATH?>assets/ds.min.js"></script>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<div style="float: left;">
<form name="myform" action="" method="get" >
<input type="hidden" value="attachment" name="m">
<input type="hidden" value="attachments" name="c">
<input type="hidden" value="album_load" name="a">
<input type="hidden" value="<?php echo $site_allowext?>" name="site_allowext">
<input type="hidden" value="<?php echo $file_upload_limit?>" name="info[file_upload_limit]">
<div class="lh26" style="padding:10px 0 0">
<label><?php echo L('name')?></label>
<input type="text" value="<?php echo $filename?>" class="input-text" name="info[filename]"> 
<label><?php echo L('date')?></label>
<?php echo form::date('info[uploadtime]', $uploadtime)?>
<input type="submit" value="<?php echo L('search')?>" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;" name="dosubmit">
</div>
</form>
</div>
<div style="float: right;"><span id="all" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全选</span><span id="allno" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全不选</span><span id="other" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">反选</span></div>
<div class="bk20 hr"></div>
<div class="files clear">
<?php foreach($infos as $r) {?>
	<div class="files_row" onmouseover="layer.tips('<?php echo $r['filename']?>&nbsp;&nbsp;<?php echo format_file_size($r['filesize'])?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
		<span class="checkbox"></span>
		<input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['aid']?>" />
		<a><img src="<?php echo $r['src']?>" id="<?php echo $r['aid']?>" width="<?php echo $r['width']?>" path="<?php echo SYS_UPLOAD_URL.$r['filepath']?>" size="<?php echo format_file_size($r['filesize'])?>" filename="<?php echo $r['filename']?>"/></a>
		<i class="size"> <?php echo format_file_size($r['filesize'])?> </i>
		<i class="name" title="<?php echo $r['filename']?>"><?php echo $r['filename']?></i>
	</div>
<?php } ?>
</div>
<div class="clear"></div>
<div id="pages" class="text-c"> <?php echo $pages?></div>
<script type="text/javascript">
$(document).ready(function(){
	set_status_empty();
});	
function set_status_empty(){
	parent.window.$('#att-status').html('');
	parent.window.$('#att-name').html('');
}
var ds = new DragSelect({
	selectables: document.getElementsByClassName('files_row'),
	multiSelectMode: true,
	//选中
	onElementSelect: function(element){
		var id = $(element).children("a").children("img").attr("id");
		var src = $(element).children("a").children("img").attr("path");
		var filename = $(element).children("a").children("img").attr("filename");
		var size = $(element).children("a").children("img").attr("size");
		var num = parent.window.$('#att-status').html().split('|').length;
		var file_upload_limit = '<?php echo $file_upload_limit?>';
		if(num > file_upload_limit) {
			//Dialog.alert('不能选择超过'+file_upload_limit+'个附件');
		}else{
			$(element).children("a").addClass("on");
			$.get('index.php?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
			parent.window.$('#att-status').append('|'+src);
			parent.window.$('#att-name').append('|'+filename);
			$(element).addClass('on').find('input[type="checkbox"]').prop('checked', true);
		}
	},
	//取消选中
	onElementUnselect: function(element){
		$(element).children("a").removeClass("on");
		var id = $(element).children("a").children("img").attr("id");
		var src = $(element).children("a").children("img").attr("path");
		var filename = $(element).children("a").children("img").attr("filename");
		var size = $(element).children("a").children("img").attr("size");
		var imgstr = parent.window.$("#att-status").html();
		var length = $("a[class='on']").children("img").length;
		var strs = filenames = '';
		$.get('index.php?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
		for(var i=0;i<length;i++){
			strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
			filenames += '|'+$("a[class='on']").children("img").eq(i).attr('filename');
		}
		parent.window.$('#att-status').html(strs);
		parent.window.$('#att-name').html(filenames);
		$(element).removeClass('on').find('input[type="checkbox"]').prop('checked', false);
	}
});
$(function(){
	//区域内的所有可选元素
	var selects = ds.selectables;

	//全选
	$('#all').click(function(){
		ds.setSelection(selects);
	});

	//全不选
	$('#allno').click(function(){
		ds.clearSelection();
	});

	//反选
	$('#other').click(function(){
		ds.toggleSelection(selects);
	});
});
</script>