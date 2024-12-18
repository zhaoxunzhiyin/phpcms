<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header', 'admin');
?>
<div class="page-content-white page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
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
                        <th width="50" class="<?php echo dr_sorting('id');?>" name="id"><?php echo L('number');?></th>
                        <th style="text-align:center" width="90" class="<?php echo dr_sorting('type');?>" name="type"><?php echo L('存储类型');?></th>
                        <th class="<?php echo dr_sorting('name');?>" name="name"><?php echo L('name');?></th>
                        <th><?php echo L('operations_manage');?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($datas as $t) {?>
                    <tr class="odd gradeX" id="dr_row_<?php echo $t['id'];?>">
                        <td class="myselect">
                            <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                                <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $t['id'];?>" />
                                <span></span>
                            </label>
                        </td>
                        <td><?php echo $t['id'];?></td>
                        <td style="text-align:center"> <span class="badge<?php if ($color[$t['type']]) {?> badge-<?php echo $color[$t['type']];?><?php }?>"> <?php echo $this->type[$t['type']]['name'];?> </span> </td>
                        <td><?php echo $t['name'];?></td>
                        <td>
                            <label><a href="?m=attachment&c=attachment&a=remote_edit&id=<?php echo $t['id'];?>&menuid=<?php echo $this->input->get('menuid');?>&pc_hash=<?php echo $this->input->get('pc_hash');?>" class="btn btn-xs green"><i class="fa fa-edit"></i> <?php echo L('edit');?></a></label>
                        </td>
                    </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>

            <div class="row list-footer table-checkable">
                <div class="col-md-5 list-select">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label>
                    <label><button type="button" onclick="ajax_option('?m=attachment&c=attachment&a=remote_delete', '<?php echo L('删除后，已关联的附件都会失效，确定要删除吗？')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
                </div>
                <div class="col-md-7 list-page">
                    <?php echo $pages;?>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
</body>
</html>