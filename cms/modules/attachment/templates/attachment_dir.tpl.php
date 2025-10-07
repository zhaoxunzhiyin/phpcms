<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<style type="text/css">
body .table-list table tr>td:first-child, body .table-list table tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="?m=attachment&c=manage&a=init<?php echo '&menuid='.$this->input->get('menuid')?>"><?php echo L('database_schema')?></a></p>
</div>
    <div class="right-card-box">
<div class="table-list">
<table width="100%" cellspacing="0">
<thead>
    <tr>
        <th><?php echo L("local_dir")?>：<?php echo $local?></th>
    </tr>
</thead>
<tbody>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left"><a href="<?php echo '?m=attachment&c=manage&a=dir&dir='.stripslashes(dirname($dir)).'&menuid='.$this->input->get('menuid')?>"> <i class="fa fa-folder"></i> <?php echo L("parent_directory")?></a></td>
</tr>
<?php endif;?>
<?php 
if(is_array($list)) {
    foreach($list as $v) {
    $filename = basename($v)
?>
<tr>
<?php if (is_dir($v)) {
    echo '<td><a href="?m=attachment&c=manage&a=dir&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? stripslashes($this->input->get('dir')).'/' : '').$filename.'&menuid='.$this->input->get('menuid').'"> <i class="fa fa-folder"></i> <b>'.$filename.'</b></a></td>';
} else {
    echo '<td><img src="'.WEB_PATH.'api.php?op=icon&fileext='.fileext($filename).'" width="20" /><a href="javascript:;" onclick="preview(\''.APP_PATH.$local.'/'.$filename.'\')">'.$filename.'</a><span style="float: right;">'.format_file_size(filesize(CMS_PATH.$local.'/'.$filename)).'</span></td>';
}?>
</tr>
<?php 
    }
}
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</body>
</html>