<?php 
defined('IS_ADMIN') or exit('No permission resources.');
$show_header = $show_validator = $show_scroll = true; 
include $this->admin_tpl('header', 'attachment');
?>
<link href="<?php echo JS_PATH;?>bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
<script src="<?php echo JS_PATH;?>bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
    if (jQuery().datepicker) {
        $('.date-picker').datepicker({
            format: "yyyy-mm-dd",
            orientation: "left",
            autoclose: true
        });
    }
});
</script>
<!--上传组件js-->
<script src="<?php echo JS_PATH?>h5upload/ds.min.js"></script>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
                            <div class="page-body">
<div class="row">
<form name="myform" action="" method="get">
<input type="hidden" value="attachment" name="m">
<input type="hidden" value="attachments" name="c">
<input type="hidden" value="album_load" name="a">
<input type="hidden" value="<?php echo $this->input->get('args');?>" name="args">
<input type="hidden" value="<?php echo $this->input->get('authkey');?>" name="authkey">
    <div class="col-md-9 margin-bottom-20">
        <label><?php echo L('name')?></label>
        <label>
            <input type="text" class="form-control" value="<?php echo isset($filename) && $filename ? $filename : '';?>" name="info[filename]">
        </label>
        <label><?php echo L('date')?></label>
        <label><div class="formdate">
            <div class="form-date input-group">
                <div class="input-group input-time date date-picker">
                <input type="text" class="form-control" name="info[uploadtime]" value="<?php echo $uploadtime;?>">
                <span class="input-group-btn">
                    <button class="btn default" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
                </div>
            </div>
        </div></label>
        <label><button type="submit" class="btn green btn-sm onloading" name="submit"> <i class="fa fa-search"></i> <?php echo L('search')?></button></label>
    </div>
</form>
    <div class="col-md-3 text-right margin-bottom-20">
        <label><span id="all" class="btn green btn-sm" style="margin-right:10px;"><?php echo L('全选');?></span></label><label><span id="allno" class="btn red btn-sm" style="margin-right:10px;"><?php echo L('全不选');?></span></label><label><span id="other" class="btn dark btn-sm"><?php echo L('反选');?></span></label>
    </div>
</div>
<form class="form-horizontal" method="post" role="form" id="myform">
<div class="files row">
<?php foreach($infos as $r) {?>
<div class="col-md-2 col-sm-2 col-xs-6 upload-item">
    <div class="files_row tooltips" data-original-title="<?php echo $r['filename']?>&nbsp;&nbsp;<?php echo format_file_size($r['filesize'])?>">
        <span class="checkbox"></span>
        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['aid']?>" />
        <a><img class="rs-load" src="<?php echo JS_PATH;?>layer/theme/default/loading-1.gif" rs-src="<?php echo $r['src']?>" id="<?php echo $r['aid']?>" width="<?php echo $r['width']?>" path="<?php echo dr_get_file_url($r)?>" size="<?php echo format_file_size($r['filesize'])?>" filename="<?php echo $r['filename']?>"/></a>
        <i class="size"> <?php echo format_file_size($r['filesize'])?> </i>
        <i class="name"><?php echo $r['filename']?></i>
        <a href="javascript:;" class="upload-del" onclick="dr_file_delete('?m=attachment&c=manage&a=delete', this, <?php echo $r['aid'];?>);return false;"><i class="fa fa-trash"></i></a>
    </div>
</div>
<?php } ?>
</div>
</form>
<div class="row right-card-box">
<div class="col-md-12 text-center margin-bottom-20"><?php echo $pages?></div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
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
            //Dialog.alert('不能选择超过'+file_upload_limit+'个附件');
        }else{
            $(element).children("a").addClass("on");
            $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
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
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
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