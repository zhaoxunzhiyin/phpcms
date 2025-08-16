<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header','admin');
?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo str_replace(array(CMS_PATH,'\\','//'), array('','/','/'), $path); ?></p>
    <?php if (IS_EDIT_TPL) { ?>
    <p style="color: red;padding-top: 5px;"><?php echo L('目前已开启可编辑文件权限和编辑代码权限，此权限风险极高'); ?></p>
    <?php } else { ?>
    <p style="color: green;padding-top: 5px;"><?php echo L('目前没有开启可编辑文件权限和编辑代码权限，不能编辑模板代码编辑框中的内容'); ?></p>
    <?php } ?>
</div>
<div class="right-card-box">
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
            <th><?php echo L('文件名'); ?></th>
            <th><?php echo L('别名'); ?></th>
            <th style="text-align:center" width="100"><?php echo L('大小'); ?></th>
            <th width="166"><?php echo L('修改日期'); ?></th>
            <th><?php echo L('操作'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($list) && is_array($list) && $list) { $key_t=-1;$count_t=dr_count($list);foreach ($list as $t) { $key_t++; $is_first=$key_t==0 ? 1 : 0;$is_last=$count_t==$key_t+1 ? 1 : 0;?>
        <tr class="odd gradeX" id="dr_row_<?php echo $t['id']; ?>">
            <td class="myselect">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $t['file']; ?>" />
                    <span></span>
                </label>
            </td>
            <td>
                <?php if ($t['url']) { ?>
                <a href="<?php echo $t['url']; ?>">
                <?php } ?>
                    <img src="<?php echo $t['icon']; ?>" width="16" style="margin-right:10px"><?php echo $t['name']; ?>
                <?php if ($t['url']) { ?>
                </a>
                <?php } ?>
            </td>
            <td><a href="<?php echo $t['cname_edit']; ?>"><?php echo $t['cname']; ?></a></td>
            <td style="text-align:center"><?php echo $t['size']; ?></td>
            <td><?php echo $t['time']; ?></td>
            <td>
                <?php if ($t['edit']) { ?>
                <label><a href="<?php echo $t['edit']; ?>" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('修改'); ?></a></label>
                <?php }  if ($t['zip']) { ?>
                <label><a href="<?php echo $t['zip']; ?>" class="btn btn-xs red"> <i class="fa fa-file-zip-o"></i> <?php echo L('解压'); ?></a></label>
                <?php } ?>
            </td>
        </tr>
        <?php } } ?>
        </tbody>
    </table>
</div>

<div class="row list-footer table-checkable ">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <button type="button" onclick="ajax_option('<?php echo $delete; ?>', '<?php echo L('你确定要删除它们吗？'); ?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete'); ?></button>
    </div>
    <div class="col-md-7 list-page">
        <?php echo $mypages; ?>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>