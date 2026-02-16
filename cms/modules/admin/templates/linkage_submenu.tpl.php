<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:iframe_show('<?php echo L('一键生成');?>', '?m=admin&c=linkage&a=public_cache&key=<?php echo $key;?>', '500px', '300px');"><?php echo L('一键生成联动菜单数据');?></a></p>
</div>

<div class="right-card-box">
    <form class="form-horizontal" role="form" id="myform" name="myform">
        <div class="table-list">
            <table class="table-checkable">
                <thead>
                <tr class="heading">
                    <th class="myselect">
                        <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                            <span></span>
                        </label>
                    </th>
                    <th width="80" style="text-align:center"> <?php echo L('排序');?> </th>
                    <th width="60" style="text-align:center"> <?php echo L('状态');?> </th>
                    <th width="80"> Id </th>
                    <th> <?php echo L('名称 / 别名');?> </th>
                    <th>
                        <a href="javascript:dr_iframe('add', '?m=admin&c=linkage&a=public_listk_add&key=<?php echo $key;?>pid=<?php echo $pid;?>');" class="btn btn-xs blue"> <i class="fa fa-plus"></i> <?php echo L('快速添加');?> </a>
                        <a href="?m=admin&c=linkage&a=public_manage_submenu&key=<?php echo $key;?>pid=0&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs dark"> <i class="fa fa-reply"></i> <?php echo L('全部数据');?> </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(is_array($list)){
                foreach($list as $t){
                ?>
                <tr class="odd gradeX" id="dr_row_<?php echo $t['id'];?>">
                    <td class="myselect">
                        <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                            <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $t['id'];?>" />
                            <span></span>
                        </label>
                    </td>
                    <td style="text-align:center"> <input type="text" onblur="dr_ajax_save(this.value, '?m=admin&c=linkage&a=public_displayorder&key=<?php echo $key;?>&id=<?php echo $t['id'];?>')" value="<?php echo $t['displayorder'];?>" class="displayorder form-control input-sm input-inline input-mini"> </td>
                    <td style="text-align:center">
                        <a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=admin&c=linkage&a=public_hidden_edit&key=<?php echo $key;?>&id=<?php echo $t['id'];?>', 1);" class="badge badge-<?php if ($t['hidden']){?>no<?php }else{?>yes<?php }?>"><i class="fa fa-<?php if ($t['hidden']){?>times<?php }else{?>check<?php }?>"></i></a>
                    </td>
                    <td><?php echo $t['id'];?></td>
                    <td><?php echo $t['name'];?> / <?php echo $t['cname'];?></td>
                    <td>
                        <label><a href="javascript:dr_iframe('add', '?m=admin&c=linkage&a=public_listk_add&key=<?php echo $key;?>&pid=<?php echo $t['id'];?>');" class="btn btn-xs blue"> <i class="fa fa-plus"></i> <?php echo L('快速添加');?> </a></label>
                        <label><a href="javascript:dr_iframe('edit','?m=admin&c=linkage&a=public_list_edit&key=<?php echo $key;?>&id=<?php echo $t['id'];?>',500,400);" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('修改');?> </a></label>
                        <?php if ($t['child']){?><label><a href="?m=admin&c=linkage&a=public_manage_submenu&key=<?php echo $key;?>&pid=<?php echo $t['id'];?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs dark"> <i class="fa fa-table"></i> <?php echo L('下级数据管理');?> </a></label><?php }?>
                    </td>
                </tr>
                <?php }}?>
                </tbody>
            </table>
        </div>

        <div class="row list-footer table-checkable">
            <div class="col-md-12 list-select">
                <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                    <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                    <span></span>
                </label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=linkage&a=public_list_del&key=<?php echo $key;?>', '<?php echo L('confirm', array('message' => L('selected')));?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=linkage&a=public_list_open&key=<?php echo $key;?>', '<?php echo L('你确定要启用它们吗？');?>', 1)" class="btn blue btn-sm"> <i class="fa fa fa-check-circle"></i> <?php echo L('启用');?></button></label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=linkage&a=public_list_close&key=<?php echo $key;?>', '<?php echo L('你确定要禁用它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa fa-times-circle"></i> <?php echo L('禁用');?></button></label>
                <label><?php echo $select;?></label>
                <label><button type="button" onclick="ajax_option('?m=admin&c=linkage&a=public_pid_edit&key=<?php echo $key;?>', '<?php echo L('你确定要批量移动它们吗？');?>', 1)" class="btn green btn-sm"> <i class="fa fa-edit"></i> <?php echo L('变更分类');?></button></label>
            </div>
         </div>
    </form>
</div>
</div>
</div>
</div>
</body>
</html>