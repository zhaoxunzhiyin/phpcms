<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> 菜单 <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <li><a class="tooltips on" href="?m=member&c=member_modelfield&a=manage&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_manage');?>"><i class="fa fa-code"></i> <?php echo L('member_modelfield_manage');?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips add fb" href="?m=member&c=member_modelfield&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_add');?>"><i class="fa fa-plus"></i> <?php echo L('member_modelfield_add');?></a></li>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
        <a class="tooltips on" href="?m=member&c=member_modelfield&a=manage&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_manage');?>"><i class="fa fa-code"></i> <?php echo L('member_modelfield_manage');?></a><i class="fa fa-circle"></i>
        <a class="tooltips add fb" href="?m=member&c=member_modelfield&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('member_modelfield_add');?>"><i class="fa fa-plus"></i> <?php echo L('member_modelfield_add');?></a>
    </div>
    <?php }?>
</div>
<div class="content-header"></div>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<form class="form-horizontal" role="form" id="myform">
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
            <th width="70"><?php echo L('listorder')?></th>
            <th style="text-align: center;" width="65">Id</th>
            <th><?php echo L('fields')?></th>
            <th width="150"><?php echo L('type');?></th>
            <th width="50"><?php echo L('must_input');?></th>
            <th width="100" style="text-align:center;"><?php echo L('disabled');?></th>
            <th width="150"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php
    foreach($datas as $r) {
    ?>
    <tr class="odd gradeX" id="dr_row_<?php echo $r['fieldid'];?>">
        <td class="myselect">
            <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['fieldid']?>" />
                <span></span>
            </label>
        </td>
        <td align='center'><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=member&c=member_modelfield&a=sort&modelid='.$r['modelid'].'&fieldid='.$r['fieldid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $r['listorder'];?>" class="displayorder form-control input-sm input-inline input-mini"></td>
        <td style="text-align: center;"><?php echo $r['fieldid']?></td>
        <td><?php echo $r['name']?> / <?php echo $r['field']?></td>
        <td align='center'><?php echo $r['formtype']?></td>
        <td align='center'><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_modelfield&a=public_isbase&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$r['isbase']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$r['isbase']) ? 'times' : 'check';?>"></i></a></td>
        <td align='center'><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_modelfield&a=disable&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>', 1);" class="badge badge-<?php echo $r['disabled'] ? 'no' : 'yes';?>"><i class="fa fa-<?php echo $r['disabled'] ? 'times' : 'check';?>"></i></a></td>
        <td align='center'><a class="btn btn-xs green" href="?m=member&c=member_modelfield&a=edit&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>"> <i class="fa fa-edit"></i> <?php echo L('modify')?></a></td>
    </tr>
    <?php } ?>
    </tbody>
    </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-12 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <button type="button" onclick="ajax_option('?m=member&c=member_modelfield&a=delete&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid');?>', '<?php echo L('你确定要删除它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>