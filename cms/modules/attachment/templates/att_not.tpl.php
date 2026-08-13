<?php 
defined('IS_ADMIN') or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = true; 
include $this->admin_tpl('header', 'attachment');
?>
<!--上传组件js-->
<?php if (is_pc()) {?>
<script src="<?php echo JS_PATH?>h5upload/ds.min.js"></script>
<?php }?>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<?php if (is_pc()) {?><div class="row">
    <div class="col-md-12 margin-bottom-20">
        <label><span id="all" class="btn green btn-sm" style="margin-right:10px;"><?php echo L('全选');?></span></label><label><span id="allno" class="btn red btn-sm" style="margin-right:10px;"><?php echo L('全不选');?></span></label><label><span id="other" class="btn dark btn-sm"><?php echo L('反选');?></span></label>
    </div>
</div><?php }?>
<div class="row">
    <div class="col-md-12 margin-bottom-20">
        <div class="note note-danger">
            <p><?php echo L('att_not_used_desc')?></p>
        </div>
    </div>
</div>
<form class="form-horizontal" method="post" role="form" id="myform">
<?php echo dr_form_hidden();?>
<div class="files row">
<?php if(is_array($att) && !empty($att)){ foreach ($att as $_v) {?>
<div class="col-md-2 col-sm-2 col-xs-6 upload-item">
    <div class="files_row tooltips" data-original-title="<?php echo $_v['filename']?>&nbsp;&nbsp;<?php echo $_v['size']?>"<?php if (is_mobile()) {?> id="attachment_<?php echo $_v['aid']?>" onclick="javascript:att_cancel(this)"<?php }?>>
        <span class="checkbox"></span>
        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $_v['aid']?>" />
        <a href="javascript:preview('<?php echo $_v['src']?>');"><img class="rs-load" id="<?php echo $_v['aid']?>" path="<?php echo $_v['src']?>" src="<?php echo JS_PATH;?>layer/theme/default/loading-1.gif" rs-src="<?php echo $_v['fileimg']?>" filename="<?php echo $_v['filename']?>" size="<?php echo $_v['size']?>"></a>
        <i class="size"><?php echo $_v['size']?></i>
        <i class="name"><?php echo $_v['filename']?></i>
        <div class="upload-del" onclick="dr_file_delete('?m=attachment&c=manage&a=delete', this, <?php echo $_v['aid'];?>);return false;"><i class="fa fa-trash"></i></div>
    </div>
</div>
<?php }}?>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
<?php if (is_pc()) {?>
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
        if(num<?php if ($ct) {echo ' + '.$ct;}?> > file_upload_limit) {
            //Dialog.alert('<?php echo L('attachment_tip1');?>'+file_upload_limit+'<?php echo L('attachment_tip2');?>');
        }else{
            $(element).children("a").addClass("on");
            $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
            parent.window.$('#att-status').append('|'+src);
            parent.window.$('#att-name').append('|'+filename);
            parent.window.$('#att-id').append('|'+id);
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
        var length = $("a[class='on']").children("img").length;
        var ids = strs = filenames = '';
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        for(var i=0;i<length;i++){
            ids += '|'+$("a[class='on']").children("img").eq(i).attr('id');
            strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
            filenames += '|'+$("a[class='on']").children("img").eq(i).attr('filename');
        }
        parent.window.$('#att-status').html(strs);
        parent.window.$('#att-name').html(filenames);
        parent.window.$('#att-id').html(ids);
        $(element).removeClass('on').find('input[type="checkbox"]').prop('checked', false);
    }
});
<?php } else {?>
function att_cancel(obj){
    var id = $(obj).children("a").children("img").attr("id");
    var src = $(obj).children("a").children("img").attr("path");
    var filename = $(obj).children("a").children("img").attr("filename");
    var size = $(obj).children("a").children("img").attr("size");
    if($(obj).hasClass('on')){
        $(obj).removeClass("on");
        $(obj).children("a").removeClass("on");
        $('#attachment_'+id).removeClass('on').find('input[type="checkbox"]').prop('checked', false);
        var length = $("a[class='on']").children("img").length;
        var ids = strs = filenames = '';
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        for(var i=0;i<length;i++){
            ids += '|'+$("a[class='on']").children("img").eq(i).attr('id');
            strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
            filenames += '|'+$("a[class='on']").children("img").eq(i).attr('filename');
        }
        parent.window.$('#att-status').html(strs);
        parent.window.$('#att-name').html(filenames);
        parent.window.$('#att-id').html(ids);
    } else {
        var num = parent.window.$('#att-status').html().split('|').length;
        var file_upload_limit = '<?php echo $file_upload_limit?>';
        if(num<?php if ($ct) {echo ' + '.$ct;}?> > file_upload_limit) {
            Dialog.alert('<?php echo L('attachment_tip1');?>'+file_upload_limit+'<?php echo L('attachment_tip2');?>');
            return false;
        }
        $(obj).addClass("on");
        $(obj).children("a").addClass("on");
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        $('#attachment_'+id).addClass('on').find('input[type="checkbox"]').prop('checked', true);
        parent.window.$('#att-status').append('|'+src);
        parent.window.$('#att-name').append('|'+filename);
        parent.window.$('#att-id').append('|'+id);
        var imgstr_del_obj = $("a[class!='on']").children("img")
        var length_del = imgstr_del_obj.length;
        var strs_del='';
        for(var i=0;i<length_del;i++){strs_del += '|'+imgstr_del_obj.eq(i).attr('id');}
    }
}
<?php }?>
$(function(){
    // 懒加载缩略图：仅当图片进入视口时再加载，避免文件过多时同时请求导致卡顿
    var rsLoad = document.querySelectorAll(".rs-load");
    if (rsLoad.length && "IntersectionObserver" in window) {
        var io = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    var src = img.getAttribute("rs-src");
                    if (src) {
                        img.src = src;
                        img.removeAttribute("rs-src");
                        img.classList.remove("rs-load");
                        io.unobserve(img);
                    }
                }
            });
        }, { rootMargin: "50px", threshold: 0.01 });
        rsLoad.forEach(function(img) { io.observe(img); });
    } else {
        $(".rs-load").each(function() {
            $(this).attr("src", $(this).attr("rs-src"));
        });
    }
    <?php if (is_pc()) {?>
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
    <?php }?>
});
</script>