<?php defined('IS_ADMIN') or exit('No permission resources.'); $show_header = $show_validator = $show_scroll = true; include $this->admin_tpl('header', 'attachment');?>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.progress {border: 0;background-image: none;filter: none;-webkit-box-shadow: none;-moz-box-shadow: none;box-shadow: none;}
.progress {height: 20px;background-color: #fff;border-radius: 4px;}
.progress-bar-success {background-color: #3ea9e2;}
</style>
<?php echo load_js(JS_PATH.'jquery-ui/jquery-ui.js');?>
<?php echo load_css(JS_PATH.'jquery-fileupload/css/jquery.fileupload.css');?>
<?php echo load_js(JS_PATH.'jquery-fileupload/js/jquery.fileupload.min.js');?>
<script type="text/javascript">
<?php echo initupload($this->input->get('module'),$this->input->get('catid'),$args,$this->groupid,$this->isadmin)?>
</script>
<script type="text/javascript">
jQuery(document).ready(function() {
    $(":text").removeClass('input-text');
});
</script>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<form action="" class="form-horizontal" method="post" name="myform" id="myform">
<input name="page" id="dr_page" type="hidden" value="<?php echo $page;?>">
<div class="portlet light bordered">
    <div class="portlet-title tabbable-line">
        <ul class="nav nav-tabs" style="float:left;">
            <li<?php if ($page==0) {?> class="active"<?php }?>>
                <a data-toggle="tab_0" onclick="h5upload_ready();$('#dr_page').val('0')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('upload_attachment').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-upload"></i> <?php if (is_pc()) {echo L('upload_attachment');}?> </a>
            </li>
            <li<?php if ($page==1) {?> class="active"<?php }?>>
                <a data-toggle="tab_1" onclick="h5upload_ready();$('#dr_page').val('1')"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('net_file').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-download"></i> <?php if (is_pc()) {echo L('net_file');}?> </a>
            </li>
            <?php if($allowupload && $this->userid) {?>
            <li<?php if ($page==2) {?> class="active"<?php }?>>
                <a data-toggle="tab_2" onclick="h5upload_ready();$('#dr_page').val('2');"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('gallery').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-photo"></i> <?php if (is_pc()) {echo L('gallery');}?> </a>
            </li>
            <?php if($this->isadmin) {?>
            <li<?php if ($page==3) {?> class="active"<?php }?>>
                <a data-toggle="tab_3" onclick="h5upload_ready();$('#dr_page').val('3');"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('directory_browse').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-folder-open"></i> <?php if (is_pc()) {echo L('directory_browse');}?> </a>
            </li>
            <?php }}?>
            <?php if($att_not_used!='') {?>
            <li<?php if ($page==4) {?> class="active"<?php }?>>
                <a data-toggle="tab_4" onclick="h5upload_ready();$('#dr_page').val('4');"<?php if (is_mobile()) {echo ' onmouseover="layer.tips(\''.L('att_not_used').'\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';}?>> <i class="fa fa-ban"></i> <?php if (is_pc()) {echo L('att_not_used');}?> </a>
            </li>
            <?php }?>
        </ul>
    </div>
    <div class="portlet-body form">
        <div class="tab-content">
            <div class="tab-pane<?php if ($page==0) {?> active<?php }?>" id="tab_0">

                <div class="form-body">

                    <div id="queue"></div>
                    <span class="btn green fileinput-button" id="file_upload"><i class="fa fa-cloud-upload"></i> <span> <?php echo L('select_file')?> </span> <input type="file" name="file_data"<?php echo $file_upload_limit > 1 ? ' multiple=""' : ''?> title=""></span>
                    <div id="nameTip" class="onShow"><?php echo L('upload_up_to')?><font color="red"> <?php echo $file_upload_limit?></font> <?php echo L('attachments')?>,<?php echo L('largest')?> <font color="red"><?php echo $file_size_limit;?> MB</font></div>
                    <div class="bk3"></div>
                    <div class="lh24"><?php echo L('supported')?> <font style="font-family: Arial, Helvetica, sans-serif"><?php echo str_replace('|','、',$file_types_post)?></font> <?php echo L('formats')?></div>
                    <div id="progress" class="margin-top-20 fileupload-progress fade" style="display:none">
                        <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-success" style="width:0%;"> </div>
                        </div>
                    </div>
                    <div class="bk10"></div>
                    <fieldset class="blue pad-10" id="h5upload">
                        <legend><?php echo L('lists')?></legend>
                        <div class="files row" id="fileupload_files"></div>
                    </fieldset>

                </div>
            </div>
            <div class="tab-pane<?php if ($page==1) {?> active<?php }?>" id="tab_1">
                <div class="form-body">

                    <div class="form-group">
                        <label class="col-md-2 control-label"><?php echo L('enter_address')?></label>
                        <div class="col-md-9">
                            <input type="text" id="dr_filename" name="info[filename]" class="form-control" value="" onblur="addonlinefile(this)">
                            <span class="help-block"><?php echo L('当目标文件过大或者对方服务器拒绝下载时会导致下载失败')?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label"></label>
                        <div class="col-md-9">
                            <label><button type="button" onclick="dr_download('filename');" class="btn green btn-sm"> <i class="fa fa-download"></i> <?php echo L('下载文件')?></button></label>
                        </div>
                    </div>

                </div>
            </div>
            <?php if($allowupload && $this->userid) {?>
            <div class="tab-pane<?php if ($page==2) {?> active<?php }?>" id="tab_2">
                <div class="form-body">

                    <ul class="attachment-list">
                        <iframe name="album-list" src="<?php echo SELF;?>?m=attachment&c=attachments&a=album_load&args=<?php echo $args?>&ct=<?php echo $ct;?>&authkey=<?php echo $authkey;?>&is_iframe=1" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="380" allowtransparency="true" id="album_list"></iframe>
                    </ul>

                </div>
            </div>
            <?php if($this->isadmin) {?>
            <div class="tab-pane<?php if ($page==3) {?> active<?php }?>" id="tab_3">
                <div class="form-body">

                    <ul class="attachment-list">
                        <iframe name="album-dir" src="<?php echo SELF;?>?m=attachment&c=attachments&a=album_dir&args=<?php echo $args?>&ct=<?php echo $ct;?>&authkey=<?php echo $authkey;?>&is_iframe=1" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="380" allowtransparency="true" id="album_dir"></iframe>
                    </ul>

                </div>
            </div>
            <?php }}?>
            <?php if($att_not_used!='') {?>
            <div class="tab-pane<?php if ($page==4) {?> active<?php }?>" id="tab_4">
                <div class="form-body">

                    <ul class="attachment-list">
                        <iframe name="att-not" src="<?php echo SELF;?>?m=attachment&c=attachments&a=att_not&args=<?php echo $args?>&ct=<?php echo $ct;?>&authkey=<?php echo $authkey;?>&is_iframe=1" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none" width="100%" height="380" allowtransparency="true" id="att_not"></iframe>
                    </ul>

                </div>
            </div>
            <?php }?>
            <div id="att-status" class="hidden"></div>
            <div id="att-name" class="hidden"></div>
            <div id="att-id" class="hidden"></div>
        </div>
    </div>
</div>
</form>
</div>
<script>
function h5upload_ready() {
    $('#att-status').html('');
    $('#att-name').html('');
    $('#att-id').html('');
};
function addonlinefile(obj) {
    var strs = $(obj).val() ? '|'+ $(obj).val() :'';
    $('#att-status').html(strs);
    $('#att-id').html(strs);
}
function dr_download(obj) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '<?php echo SELF;?>?m=attachment&c=attachments&a=download&token='+csrf_hash,
        data: {module:'<?php echo $this->input->get('module');?>',catid:'<?php echo $this->input->get('catid');?>',args:'<?php echo $args;?>',authkey:'<?php echo $authkey;?>',filename:$('#dr_'+obj).val()},
        success: function(json) {
            if (json.code) {
                dr_tips(json.code, json.msg);
                $('#dr_'+obj).val(json.info.url);
                var strs = json.info.url ? '|'+ json.info.url : '';
                var names = json.info.name ? '|'+ json.info.name : '';
                var ids = json.id ? '|'+ json.id : '';
                $('#att-status').html(strs);
                $('#att-name').html(names);
                $('#att-id').html(ids);
            } else {
                dr_tips(json.code, json.msg);
            }
        },
        error: function(HttpRequest, ajaxOptions, thrownError) {
            dr_ajax_admin_alert_error(HttpRequest, ajaxOptions, thrownError)
        }
    });
}
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
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json_del&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        for(var i=0;i<length;i++){
            ids += '|'+$("a[class='on']").children("img").eq(i).attr('id');
            strs += '|'+$("a[class='on']").children("img").eq(i).attr('path');
            filenames += '|'+$("a[class='on']").children("img").eq(i).attr('filename');
        }
        $('#att-status').html(strs);
        $('#att-name').html(filenames);
        $('#att-id').html(ids);
    } else {
        $(obj).addClass("on");
        $(obj).children("a").addClass("on");
        $.get('<?php echo SELF;?>?m=attachment&c=attachments&a=h5upload_json&aid='+id+'&src='+src+'&filename='+filename+'&size='+size);
        $('#attachment_'+id).addClass('on').find('input[type="checkbox"]').prop('checked', true);
        $('#att-status').append('|'+src);
        $('#att-name').append('|'+filename);
        $('#att-id').append('|'+id);
        var imgstr_del_obj = $("a[class!='on']").children("img")
        var length_del = imgstr_del_obj.length;
        var strs_del='';
        for(var i=0;i<length_del;i++){strs_del += '|'+imgstr_del_obj.eq(i).attr('id');}
    }
}
</script>
</div>
</div>
</body>
</html>