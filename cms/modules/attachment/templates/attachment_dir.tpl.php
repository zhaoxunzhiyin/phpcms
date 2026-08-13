<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><strong><?php echo L('物理目录');?></strong>：<?php echo IS_DEV ? SYS_UPLOAD_PATH : str_replace(CMS_PATH, '/', SYS_UPLOAD_PATH);?></p>
    <p><strong><?php echo L('访问地址');?></strong>：<?php echo SYS_UPLOAD_URL;?></p>
</div>
<div class="right-card-box">
    <div class="row" style="margin-bottom:12px;">
        <div class="col-md-12">
            <ul class="list-inline" style="margin:0;padding:8px 12px;background:#f5f5f5;border-radius:4px;border:1px solid #e7e7e7;">
                <?php if (isset($breadcrumb) && is_array($breadcrumb) && $breadcrumb) {foreach ($breadcrumb as $c) {?>
                    <?php if ($c['path'] != $listing['rel']) {?>
                    <a href="?m=attachment&c=manage&a=dir&path=<?php echo $c['path'];?>"><?php echo $c['name'];?></a>
                    <?php } else {?>
                    <strong><?php echo $c['name'];?></strong>
                    <?php }?>
                <?php if ($c['path'] != $listing['rel']) {?>
                    /
                <?php }}}?>
            </ul>
        </div>
    </div>
    <form class="form-horizontal" role="form" id="myform">
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
                        <th width="55%"><?php echo L('名称');?></th>
                        <th width="12%"><?php echo L('类型');?></th>
                        <th width="13%"><?php echo L('大小');?></th>
                        <th width="20%"><?php echo L('修改时间');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($listing['rel']) {?>
                    <tr>
                        <td></td>
                        <td><a href="?m=attachment&c=manage&a=dir&path=<?php echo $parent_rel;?>"><i class="fa fa-level-up"></i> <?php echo L('parent_directory');?></a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php } if (isset($listing['dirs']) && is_array($listing['dirs']) && $listing['dirs']) {foreach ($listing['dirs'] as $t) {?>
                    <tr>
                        <td class="myselect">
                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="paths[]" value="<?php echo $t['path'];?>" />
                                <span></span>
                            </label>
                        </td>
                        <td><i class="fa fa-folder"></i> <a href="?m=attachment&c=manage&a=dir&path=<?php echo $t['path'];?>"><?php echo $t['name'];?></a></td>
                        <td><?php echo L('文件夹');?></td>
                        <td><?php echo format_file_size($t['size']);?></td>
                        <td><?php echo dr_date($t['mtime'], null, 'red');?></td>
                    </tr>
                    <?php } } if (isset($listing['files']) && is_array($listing['files']) && $listing['files']) {foreach ($listing['files'] as $t) {?>
                    <tr>
                        <td class="myselect">
                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="paths[]" value="<?php echo $t['path'];?>" />
                                <span></span>
                            </label>
                        </td>
                        <td>
                            <?php if ($t['is_image']) {?>
                            <img src="<?php echo $t['url'];?>" style="max-width:28px;max-height:28px;vertical-align:middle;margin-right:6px;" />
                            <?php } else {?>
                            <img src="<?php echo WEB_PATH;?>api.php?op=icon&fileext=<?php echo $t['ext'];?>" style="max-width:20px;max-height:20px;vertical-align:middle;margin-right:6px;" />
                            <?php }?>
                            <a href="javascript:preview('<?php echo $t['url'];?>');"><?php echo $t['name'];?></a>
                            <?php if ($t['is_image']) {?><a class="btn btn-xs default" style="margin-left:6px;vertical-align:middle;" href="<?php echo $t['url'];?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-external-link"></i></a><?php }?>
                        </td>
                        <td><?php if ($t['ext']) {echo $t['ext'];} else {echo L('文件');}?></td>
                        <td><?php echo format_file_size($t['size']);?></td>
                        <td><?php echo dr_date($t['mtime'], null, 'red');?></td>
                    </tr>
                    <?php } } if (!$listing['dirs'] && !$listing['files'] && !$listing['rel']) {?>
                    <tr><td></td><td><?php echo L('暂无文件');?></td><td></td><td></td><td></td></tr>
                    <?php } else if (!$listing['dirs'] && !$listing['files']) {?>
                    <tr><td></td><td><?php echo L('当前文件夹为空');?></td><td></td><td></td><td></td></tr>
                    <?php }?>
                </tbody>
            </table>
        </div>
        <div class="row list-footer table-checkable">
            <div class="col-md-12 list-select">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                    <span></span>
                </label>
                <label><button type="button" onclick="ajax_option('?m=attachment&c=manage&a=delete&action=dir', '<?php echo L('删除后，已关联的附件都会失效，确定要删除吗？')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
            </div>
        </div>
    </form>
</div>
</div>
</div>
</div>
</body>
</html>