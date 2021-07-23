<?php 
$show_header = $show_validator = $show_scroll = 1; 
include $this->admin_tpl('header', 'attachment');
?>
<!--上传组件js-->
<script src="<?php echo JS_PATH?>assets/ds.min.js"></script>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<div style="margin-bottom:10px;"><span id="all" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全选</span><span id="allno" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">全不选</span><span id="other" class="btn blue" style="color: #fff;background-color: #32c5d2;border-color: #32c5d2;line-height: 1.44;outline: 0!important;box-shadow: none!important;display: inline-block;margin-bottom: 0;vertical-align: middle;cursor: pointer;border: 1px solid transparent;white-space: nowrap;padding: 6px 12px;font-size: 14px;line-height: 1.42857;border-radius: 4px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;margin-left: 10px;">反选</span></div>
<div class="explain-col"><?php echo L('att_not_used_desc')?></div>
<div class="bk20 hr"></div>
<div class="files clear">
<?php if(is_array($att) && !empty($att)){ foreach ($att as $_v) {?>
    <div class="files_row" onmouseover="layer.tips('<?php echo $_v['filename']?>&nbsp;&nbsp;<?php echo $_v['size']?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();">
        <span class="checkbox"></span>
        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $_v['aid']?>" />
        <a class="off"><img width="<?php echo $_v['width']?>" id="<?php echo $_v['aid']?>" path="<?php echo $_v['src']?>" src="<?php echo $_v['fileimg']?>" filename="<?php echo $_v['filename']?>" size="<?php echo $_v['size']?>"></a>
        <i class="size"><?php echo $_v['size']?></i>
        <i class="name" title="<?php echo $_v['filename']?>"><?php echo $_v['filename']?></i>
    </div>
<?php }}?>
</div>
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
			$.get('index.php?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
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
		$.get('index.php?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
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