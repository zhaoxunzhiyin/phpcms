<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><?php echo L('联动菜单可以作为地区、行业、类型等，也可以按站点来设置联动菜单值');?></p>
</div>
<div class="right-card-box">
<form class="form-horizontal" role="form" name="myform" id="myform">
    <div class="table-list">
        <table class="table-checkable">
            <thead>
            <tr class="heading">
                <th class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label>
                </th>
                <th width="150"> <?php echo L('名称');?> </th>
                <th width="120"> <?php echo L('别名');?> </th>
                <th width="120"> <?php echo L('表名');?> </th>
                <th width="120" style="text-align:center"> <?php echo L('数据');?> </th>
                <th> <?php echo L('operations_manage');?> </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(is_array($infos)){
            foreach($infos as $info){
            ?>
            <tr class="odd gradeX" id="dr_row_<?php echo $info['id'];?>">
                <td class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $info['id'];?>" />
                        <span></span>
                    </label>
                </td>
                <td><?php echo $info['name'];?></td>
                <td><?php echo $info['code'];?></td>
                <td><?php echo 'linkage_data_'.$info['id'];?></td>
                <td style="text-align:center">
                    <?php echo $info['count'];?>
                </td>
                <td style="overflow:visible">
                    <label><a href="javascript:dr_iframe('edit','?m=admin&c=linkage&a=edit&id=<?php echo $info['id'];?>',500,350);" class="btn btn-xs green"> <i class="fa fa-edit"></i> <?php echo L('修改');?> </a></label>
                    <label><a href="?m=admin&c=linkage&a=public_manage_submenu&key=<?php echo $info['id'];?>&menuid=<?php echo $this->input->get('menuid');?>" class="btn btn-xs dark"> <i class="fa fa-table"></i> <?php echo L('数据管理');?> </a></label>
                    <label><a href="javascript:iframe_show('<?php echo L('一键生成');?>', '?m=admin&c=linkage&a=public_cache&key=<?php echo $info['id'];?>', '500px', '300px');" class="btn btn-xs yellow"> <i class="fa fa-refresh"></i> <?php echo L('一键生成');?></a></label>
                    <?php 
                    if(is_array($dt_data)){
                    foreach($dt_data as $i=>$n){
                    ?>
                    <label>
                        <a class="btn btn-xs red" href="javascript:confirmiframe('?m=admin&c=linkage&a=public_import&code=<?php echo $i;?>&id=<?php echo $info['id'];?>', '<?php echo L('导入');?>', '<?php echo L('操作将会现有的数据覆盖掉，您确定吗？');?>', '500px', '280px');" > <i class="fa fa-sign-in"></i> <?php echo L($n);?></a>
                    </label>
                    <?php }}?>
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
            <button type="button" onclick="ajax_option('?m=admin&c=linkage&a=delete', '<?php echo L('confirm', array('message' => L('selected')));?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button>
        </div>
     </div>

</form>
</div>
</div>
</div>
</div>
</body>
</html>