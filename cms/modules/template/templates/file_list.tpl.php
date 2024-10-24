<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<style type="text/css">
body .table-list table tr>td:first-child, body .table-list table tr>th:first-child {text-align: left;padding: 8px;}
</style>
<div class="subnav">
  <h1 class="title-2"><?php echo $this->style_info['name'].' - '.L('detail')?></h1>
</div>
<div class="content-header"></div>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form action="?m=template&c=file&a=updatefilename&style=<?php echo $this->style?>" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th align="left"><?php echo L("dir")?></th>
        <th align="left"><?php echo L('desc')?></th>
        <th style="text-align:center" width="100"><?php echo L('大小')?></th>
        <th width="166"><?php echo L('修改日期')?></th>
        <th align="left"><?php echo L('operation')?></th>
        </tr>
        </thead>
<tbody>
<tr>
<td align="left" colspan="5"><?php echo L("local_dir")?>：<?php echo $local?></td>
</tr>
<?php if ($dir !='' && $dir != '.'):?>
<tr>
<td align="left" colspan="5"><a href="<?php echo '?m=template&c=file&a=init&style='.$this->style.'&dir='.stripslashes(dirname($dir))?>"><img src="<?php echo IMG_PATH?>folder-closed.png" /><?php echo L("parent_directory")?></a></td>
</tr>
<?php endif;?>
<?php 
if(is_array($list)):
    foreach($list as $v):
    $filename = basename($v);
?>
<tr>
<?php if (is_dir($v)) {
    echo '<td align="left"><img src="'.IMG_PATH.'folder-closed.png" /> <a href="?m=template&c=file&a=init&style='.$this->style.'&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? stripslashes($this->input->get('dir')).DIRECTORY_SEPARATOR : '').$filename.'"><b>'.$filename.'</b></a></td><td align="left"><label style="width: 100%;"><input type="text" name="file_explan['.$encode_local.']['.$filename.']" value="'.(isset($file_explan[$encode_local][$filename]) ? $file_explan[$encode_local][$filename] : "").'"></label></td><td style="text-align:center"> - </td><td>'.dr_date(filemtime($this->filepath.$dir.DIRECTORY_SEPARATOR.$filename), null, 'red').'</td><td></td>';
} else {
     if (substr($filename,-4,4) == 'html') {
        echo '<td align="left"><img src="'.IMG_PATH.'file.png" /> '.$filename.'</td><td align="left"><label style="width: 100%;"><input type="text" name="file_explan['.$encode_local.']['.$filename.']" value="'.(isset($file_explan[$encode_local][$filename]) ? $file_explan[$encode_local][$filename] : "").'"></label></td><td style="text-align:center">'.format_file_size(filesize($this->filepath.$dir.DIRECTORY_SEPARATOR.$filename)).'</td><td>'.dr_date(filemtime($this->filepath.$dir.DIRECTORY_SEPARATOR.$filename), null, 'red').'</td>';
        echo '<td>';
        if($tpl_edit=='1'){
            echo '<a class="btn btn-xs green" href="?m=template&c=file&a=edit_file&style='.$this->style.'&dir='.urlencode(stripslashes($dir)).'&file='.$filename.'">'.L('edit').'</a> <a class="btn btn-xs blue" href="?m=template&c=file&a=visualization&style='.$this->style.'&dir='.urlencode(stripslashes($dir)).'&file='.$filename.'" target="_blank">'.L('visualization').'</a> <a class="btn btn-xs dark" href="javascript:history_file(\''.$filename.'\')">'.L('histroy').'</a>';
        }
        echo '</td>';
     }
}?>
</tr>
<?php 
    endforeach;
endif;
?></tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label><button type="button" onclick="location.href='?m=template&c=style&a=init&menuid=<?php echo $menu_data['id']?>&pc_hash=<?php echo dr_get_csrf_token();?>'" class="btn yellow btn-sm"> <i class="fa fa-mail-reply-all"></i> <?php echo L('returns_list_style')?></button></label>
        <?php if ($tpl_edit=='1') {?>
        <label><button type="button" onclick="add_file()" class="btn blue btn-sm"> <i class="fa fa-plus"></i> <?php echo L('new')?></button></label>
        <?php }?>
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('update')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<?php if ($tpl_edit=='1') {?>
<script type="text/javascript">
<!--
function history_file(name) {
    var w = 700;
    var h = 520;
    omnipotent('history','?m=template&c=template_bak&a=init&style=<?php echo $this->style;?>&dir=<?php echo urlencode(stripslashes($dir))?>&filename='+name+'&pc_hash='+pc_hash,'《'+name+'》<?php echo L("histroy")?>',1,w,h);
}
function add_file() {
    artdialog('add_file','?m=template&c=file&a=add_file&style=<?php echo $this->style;?>&dir=<?php echo urlencode(stripslashes($dir))?>&is_iframe=1','<?php echo L("new")?>',500,300);
}
//-->
</script>
<?php }?>
</body>
</html>