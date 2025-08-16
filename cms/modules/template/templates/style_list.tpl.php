<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo str_replace(array(CMS_PATH,'\\','//'), array('','/','/'), $this->filepath); ?></p>
    <?php if (IS_EDIT_TPL) { ?>
    <p style="color: red;padding-top: 5px;"><?php echo L('目前已开启可编辑文件权限和编辑代码权限，此权限风险极高'); ?></p>
    <?php } else { ?>
    <p style="color: green;padding-top: 5px;"><?php echo L('目前没有开启可编辑文件权限和编辑代码权限，不能编辑模板代码编辑框中的内容'); ?></p>
    <?php } ?>
</div>
<div class="right-card-box">
<form action="?m=template&c=style&a=updatename" method="post">
<input name="dosubmit" type="hidden" value="1">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
        <tr>
        <th width="180"><?php echo L("style_identity")?></th>
        <th><?php echo L("style_chinese_name")?></th>
        <th width="150"><?php echo L("author")?></th>
        <th width="100"><?php echo L("style_version")?></th>
        <th width="80"><?php echo L("status")?></th>
        <th><?php echo L('operations_manage')?></th>
        </tr>
        </thead>
<tbody>
<?php 
if(is_array($list)):
    foreach($list as $v):
?>
<tr>
<td align="center"><a href="?m=template&c=file&a=init&style=<?php echo $v['dirname']?>"><?php echo $v['dirname']?></a></td>
<td align="center"><label style="width: 100%;"><input type="text" name="name[<?php echo $v['dirname']?>]" value="<?php echo $v['name']?>" /></label></td>
<td align="center"><?php if($v['homepage']) {echo  '<a href="'.$v['homepage'].'" target="_blank">';}?><?php echo $v['author']?><?php if($v['homepage']) {echo  '</a>';}?></td>
<td align="center"><?php echo $v['version']?></td>
<td align="center"><?php if($v['disable']){echo L('icon_locked');}else{echo L("icon_unlock");}?></td>
<td align="center"><a class="btn btn-xs dark" href="?m=template&c=style&a=disable&style=<?php echo $v['dirname']?>"><?php if($v['disable']){echo L("enable");}else{echo L('disable');}?></a> <a class="btn btn-xs blue" href="?m=template&c=file&a=init&style=<?php echo $v['dirname']?>"><?php echo L("detail")?></a> <a class="btn btn-xs yellow" href="?m=template&c=style&a=export&style=<?php echo $v['dirname']?>"><?php echo L('export')?></a></td>
</tr>
<?php 
    endforeach;
endif;
?>
</tbody>
</table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label><button type="submit" class="btn green btn-sm"> <i class="fa fa-refresh"></i> <?php echo L('update')?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>