<?php
defined('IN_CMS') or exit('No permission resources.');
defined('IS_ADMIN') or exit('No permission resources.');
$show_header = $show_scroll = true;
include $this->admin_tpl('header','attachment');
?>
<link href="<?php echo JS_PATH?>h5upload/h5upload.css" rel="stylesheet" type="text/css" />
<style type="text/css">
img{max-width: 180px;max-height: 180px;border:none;}
body .table-list table tr>td:first-child, body .table-list table tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="pad-lr-10">
<div class="table-list">
<table width="100%" cellspacing="0" id="imgPreview">
    <thead>
        <tr>
            <th><?php echo L("local_dir")?>：<?php echo $local?></th>
        </tr>
    </thead>
<tbody>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left"><a href="<?php echo '?m=attachment&c=attachments&a=album_dir&args='.$this->input->get('args').'&authkey='.$this->input->get('authkey').'&dir='.stripslashes(dirname($dir)).'&is_iframe=1'?>"> <i class="fa fa-folder"></i> <?php echo L("parent_directory")?></td></a>
</tr>
<?php endif;?>
<?php 
if(is_array($list)):
    foreach($list as $v):
    $filename = basename($v);
?>
<tr>
<?php if (is_dir($v)) {
    echo '<td align="left"><a href="?m=attachment&c=attachments&a=album_dir&args='.$this->input->get('args').'&authkey='.$this->input->get('authkey').'&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? stripslashes($this->input->get('dir')).'/' : '').$filename.'&is_iframe=1"> <i class="fa fa-folder"></i> <b>'.$filename.'</b></a></td>';
} else {
    echo '<td align="left" onclick="javascript:album_cancel(this)"><img src="'.WEB_PATH.'api.php?op=icon&fileext='.fileext($filename).'" width="20" /> <a href="javascript:;" rel="'.$url.$filename.'" name="'.file_name($filename).'"';
    if (dr_is_image(CMS_PATH.$local.'/'.$filename)) {
        echo ' onmouseover="layer.tips(\'<img src='.$url.$filename.'>\',this,{tips: [1, \'#fff\']});" onmouseout="layer.closeAll();"';
    }
    echo '>'.$filename.'</a><span style="float: right;">'.format_file_size(filesize(CMS_PATH.$local.'/'.$filename)).'</span></td>';
}?>
</tr>
<?php 
    endforeach;
endif;
?>
</tbody>
</table>
</div>
</div>
</body>
<script type="text/javascript">
function album_cancel(obj){
    var src = $(obj).children("a").attr("rel");
    var filename = $(obj).children("a").attr("name");
    if($(obj).hasClass('on')){
        $(obj).removeClass("on");
        var length = $("a[class='on']").children("a").length;
        var strs = filenames = '';
        for(var i=0;i<length;i++){
            strs += '|'+$("a[class='on']").children("a").eq(i).attr('rel');
            filenames += '|'+$("a[class='on']").children("a").eq(i).attr('name');
        }
        parent.window.$('#att-status').html(strs);
        parent.window.$('#att-name').html(filenames);
        parent.window.$('#att-id').html(strs);
    } else {
        var num = parent.window.$('#att-status').html().split('|').length;
        var file_upload_limit = '<?php echo $file_upload_limit?>';
        if(num<?php if ($ct) {echo ' + '.$ct;}?> > file_upload_limit) {
            Dialog.alert('不能选择超过'+file_upload_limit+'个附件');
        }else{
            $(obj).addClass("on");
            parent.window.$('#att-status').append('|'+src);
            parent.window.$('#att-name').append('|'+filename);
            parent.window.$('#att-id').append('|'+src);
        }
    }
}
</script>
</html>