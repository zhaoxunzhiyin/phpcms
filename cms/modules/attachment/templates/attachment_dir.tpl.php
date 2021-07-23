<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<style type="text/css">
body .table-list tr>td:first-child, body .table-list tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="bk15"></div>
<div class="pad-lr-10">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">
		<a href="?m=attachment&c=manage<?php echo '&menuid='.$this->input->get('menuid')?>"><?php echo L('database_schema')?></a>
		</div>
		</td>
		</tr>
    </tbody>
</table>
<div class="table-list">
<table width="100%" cellspacing="0">
<tr>
<td align="left"><?php echo L("local_dir")?>：<?php echo $local?></td><td></td><td></td>
</tr>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left"><a href="<?php echo '?m=attachment&c=manage&a=dir&dir='.stripslashes(dirname($dir)).'&menuid='.$this->input->get('menuid')?>"><img src="<?php echo IMG_PATH?>folder-closed.gif" /><?php echo L("parent_directory")?></a></td><td></td><td></td>
</tr>
<?php endif;?>
<?php 
if(is_array($list)) {
	foreach($list as $v) {
	$filename = basename($v)
?>
<tr>
<?php if (is_dir($v)) {
	echo '<td align="left"><img src="'.IMG_PATH.'folder-closed.gif" /> <a href="?m=attachment&c=manage&a=dir&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? stripslashes($this->input->get('dir')).'/' : '').$filename.'&menuid='.$this->input->get('menuid').'"><b>'.$filename.'</b></a></td><td width="10%"></td><td width="10%"></td>';
} else {
	echo '<td align="left"><img src="'.file_icon($filename,'gif').'" /><a rel="'.$local.'/'.$filename.'">'.$filename.'</a></td><td width="10%">'.format_file_size(filesize(CMS_PATH.$local.'/'.$filename)).'</td><td width="10%"><a href="javascript:;" onclick="preview(\''.$local.'/'.$filename.'\')">'.L('preview').'</a> | <a href="javascript:;" onclick="att_delete(this,\''.urlencode($filename).'\',\''.urlencode($local).'\')">'.L('delete').'</a></td>';
}?>
</tr>
<?php 
	}
}
?>
</table>
</div>
</div>
</body>
<script type="text/javascript">
function preview(file) {
	if(IsImg(file)) {
        var width = '400px';
        var height = '300px';
        var att = 'height: 260px;';
        if (is_mobile()) {
            width = height = '90%';
            var att = 'height: 90%;';
        }
        var diag = new Dialog({
            title:'<?php echo L('preview')?>',
            html:'<style type="text/css">a,a:hover{color: #337ab7; text-decoration:none;}</style><div style="'+att+'line-height: 24px;word-break: break-all;overflow: hidden auto;"><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><a href="'+file+'" target="_blank"><img style="max-width:100%" src="'+file+'"></a></p></div>',
            width:width,
            height:height,
            modal:true
        });
        diag.show();
    } else if(IsMp4(file)) {
        var width = '500px';
        var height = '320px';
        var att = 'width="420" height="238"';
        if (is_mobile()) {
            width = height = '90%';
            var att = 'width="90%" height="200"';
        }
        var diag = new Dialog({
            title:'<?php echo L('preview')?>',
            html:'<style type="text/css">a,a:hover{color: #337ab7; text-decoration:none;}</style><p style="word-break: break-all;text-align: center;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"> <video class="video-js vjs-default-skin" controls="true" preload="auto" '+att+'><source src="'+file+'" type="video/mp4"/></video>\n</p>',
            width:width,
            height:height,
            modal:true
        });
        diag.show();
    } else if(IsMp3(file)) {
        var diag = new Dialog({
            title:'<?php echo L('preview')?>',
            html:'<style type="text/css">a,a:hover{color: #337ab7; text-decoration:none;}</style><p style="text-align: center;word-break: break-all;margin-bottom: 20px;"><a href="'+file+'" target="_blank">'+file+'</a></p><p style="text-align: center;"><audio src="'+file+'" controls="controls"></audio></p>',
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
function att_delete(obj,filename,localdir){
	Dialog.confirm('<?php echo L('del_confirm')?>', function(){$.get('?m=attachment&c=manage&a=pulic_dirmode_del&filename='+filename+'&dir='+localdir+'&pc_hash='+pc_hash,function(data){if(data == 1) $(obj).parent().parent().fadeOut("slow");})});
};
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
function IsMp4(url){
    var sTemp;
    var b=false;
    var opt="mp4";
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
function IsMp3(url){
    var sTemp;
    var b=false;
    var opt="mp3";
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
</html>