<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo $path; ?></p>
    <?php if (IS_EDIT_TPL) { ?>
    <p style="color: red;padding-top: 5px;"><?php echo L('目前已开启可编辑文件权限和编辑代码权限，此权限风险极高'); ?></p>
    <?php } else { ?>
    <p style="color: green;padding-top: 5px;"><?php echo L('目前没有开启可编辑文件权限和编辑代码权限，不能编辑模板代码编辑框中的内容'); ?></p>
    <?php } ?>
</div>
<div class="right-card-box">
<form action="?m=template&c=file&a=updatefilename&style=<?php echo $this->style?>" method="post" id="myform">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr class="heading">
        <th class="myselect table-checkable">
            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                <span></span>
            </label>
        </th>
        <th align="left"><?php echo L("dir")?></th>
        <th align="left"><?php echo L('desc')?></th>
        <th style="text-align:center" width="100"><?php echo L('大小')?></th>
        <th width="166"><?php echo L('修改日期')?></th>
        <th align="left"><?php echo L('operation')?></th>
        </tr>
        </thead>
<tbody>
<?php if ($dir !='' && $dir != '.'):?>
<tr class="odd gradeX" id="dr_row_0">
            <td class="myselect">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="checkboxes" name="ids[]" value="" />
                    <span></span>
                </label>
            </td>
<td><a href="<?php echo '?m=template&c=file&a=init&style='.$this->style.'&dir='.trim(dirname($dir), '.');?>"> <i class="fa fa-folder"></i> ..</a></td>
<td></td>
<td style="text-align:center"> - </td>
<td></td>
<td></td>
</tr>
<?php endif;?>
<?php 
if(is_array($list)):
    foreach($list as $v):
    $filename = basename($v);
?>
<tr class="odd gradeX" id="dr_row_<?php echo md5($filename); ?>">
<?php if (is_dir($v)) {
    echo '<td class="myselect">'.(($filename=='pc' || $filename=='mobile') ? '' : '<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="checkboxes" name="ids[]" value="'.$filename.'" /><span></span></label>').'</td><td align="left"><a href="?m=template&c=file&a=init&style='.$this->style.'&dir='.($this->input->get('dir') && !empty($this->input->get('dir')) ? urlencode($this->input->get('dir').'/') : '').$filename.'"> <i class="fa fa-folder"></i> <b>'.$filename.'</b></a></td><td align="left"><label style="width: 100%;"><input type="text" name="file_explan['.$encode_local.']['.$filename.']" value="'.(isset($file_explan[$encode_local][$filename]) ? $file_explan[$encode_local][$filename] : "").'"></label></td><td style="text-align:center"> - </td><td>'.dr_date(filemtime($this->filepath.$dir.'/'.$filename), null, 'red').'</td><td></td>';
} else {
     if (substr($filename,-4,4) == 'html') {
        echo '<td class="myselect"><label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="checkboxes" name="ids[]" value="'.$filename.'" /><span></span></label></td><td align="left"><a href="?m=template&c=file&a=edit_file&style='.$this->style.'&dir='.urlencode(stripslashes($dir)).'&file='.$filename.'"><img src="'.WEB_PATH.'api.php?op=icon&fileext=html" width="20" /> '.$filename.'</a></td><td align="left"><label style="width: 100%;"><input type="text" name="file_explan['.$encode_local.']['.$filename.']" value="'.(isset($file_explan[$encode_local][$filename]) ? $file_explan[$encode_local][$filename] : "").'"></label></td><td style="text-align:center">'.format_file_size(filesize($this->filepath.$dir.'/'.$filename)).'</td><td>'.dr_date(filemtime($this->filepath.$dir.'/'.$filename), null, 'red').'</td>';
        echo '<td><a class="btn btn-xs green" href="?m=template&c=file&a=edit_file&style='.$this->style.'&dir='.urlencode(stripslashes($dir)).'&file='.$filename.'"> <i class="fa fa-edit"></i> '.L('edit').'</a> <a class="btn btn-xs blue" href="?m=template&c=file&a=visualization&style='.$this->style.'&dir='.urlencode(stripslashes($dir)).'&file='.$filename.'" target="_blank"> <i class="fa fa-eye"></i> '.L('visualization').'</a> <a class="btn btn-xs dark" href="javascript:history_file(\''.$filename.'\')"> <i class="fa fa-history"></i> '.L('histroy').'</a></td>';
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
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <button type="button" onclick="ajax_option('<?php echo $delete; ?>', '<?php echo L('你确定要删除它们吗？'); ?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete'); ?></button>
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('update')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
<script type="text/javascript">
function history_file(name) {
    omnipotent('history','?m=template&c=template_bak&a=init&style=<?php echo $this->style;?>&dir=<?php echo urlencode(stripslashes($dir))?>&filename='+name+'&pc_hash='+pc_hash,'《'+name+'》<?php echo L("histroy")?>',1,700,520);
}
</script>
</body>
</html>