<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
            <li><a class="tooltips on" href="?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?>"><i class="fa fa-code"></i> <?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?></a></li>
            <div class="dropdown-line"></div>
            <li><a class="tooltips add fb" href="?m=content&c=sitemodel_field&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('add_field');?>"><i class="fa fa-plus"></i> <?php echo L('add_field');?></a></li><?php if($modelid && $modelid!=-1 && $modelid!=-2) {?><div class="dropdown-line"></div><li><a class="tooltips" href="javascript:;" onclick="javascript:openwinx('priview','?m=content&c=sitemodel_field&a=public_priview&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash=<?php echo dr_get_csrf_token();?>','<?php echo L('priview_modelfield');?>')" data-container="body" data-placement="bottom" data-original-title="<?php echo L('priview_modelfield');?>"><i class="fa fa-eye"></i> <?php echo L('priview_modelfield');?></a></li>
            <?php }?>
        </ul>
    </div>
    <?php } else {?>
    <div class="content-menu ib-a">
<a class="tooltips on" href="?m=content&c=sitemodel_field&a=init&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?>"><i class="fa fa-code"></i> <?php if($modelid==-1) {echo L('category').L('field_manage');} else if($modelid==-2) {echo L('category_page').L('field_manage');} else if($modelid) {echo L('manage_field');} else {echo L('sites').L('field_manage');}?></a>
<i class="fa fa-circle"></i><a class="tooltips add fb" href="?m=content&c=sitemodel_field&a=add&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('add_field');?>"><i class="fa fa-plus"></i> <?php echo L('add_field');?></a><?php if($modelid && $modelid!=-1 && $modelid!=-2) {?><i class="fa fa-circle"></i><a class="tooltips" href="javascript:;" onclick="javascript:openwinx('priview','?m=content&c=sitemodel_field&a=public_priview&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid')?>&pc_hash=<?php echo dr_get_csrf_token();?>','<?php echo L('priview_modelfield');?>')" data-container="body" data-placement="bottom" data-original-title="<?php echo L('priview_modelfield');?>"><i class="fa fa-eye"></i> <?php echo L('priview_modelfield');?></a>
<?php }?>
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
            <th width="50"><?php echo L('system');?></th> 
            <th width="50"><?php echo L('must_input');?></th>
            <th width="50"<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>><?php echo L('search');?></th>
            <th width="50"<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>><?php echo L('contribute');?></th>
            <th width="50" style="text-align:center;"><?php echo L('可用');?></th>
            <th width="150"><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
    <tbody>
    <?php foreach($datas as $r) {?>
    <tr class="odd gradeX" id="dr_row_<?php echo $r['fieldid'];?>">
        <td class="myselect">
            <label class="mt-table mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <?php if(!in_array($r['field'],$forbid_delete)) {?> 
                <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $r['fieldid']?>" />
                <?php } else {?>
                <input type="checkbox" class="" disabled name="" value="" />
                <?php }?>
                <span></span>
            </label>
        </td>
        <td align='center'><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=content&c=sitemodel_field&a=listorder&modelid='.$r['modelid'].'&fieldid='.$r['fieldid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $r['listorder'];?>" class="displayorder form-control input-sm input-inline input-mini"></td>
        <td style="text-align: center;"><?php echo $r['fieldid']?></td>
        <td><?php echo $r['name']?> / <?php echo $r['field']?></td>
        <td align='center'><?php echo $r['formtype']?></td>
        <td align='center'><?php echo $r['issystem'] ? '<i class="fa fa-check-circle font-blue"></i>' : '<i class="fa fa-times-circle font-red"></i>';?></td>
        <td align='center'><?php echo $r['minlength'] ? '<i class="fa fa-check-circle font-blue"></i>' : '<i class="fa fa-times-circle font-red"></i>';?></td>
        <?php require MODEL_PATH.$r['formtype'].DIRECTORY_SEPARATOR.'config.inc.php';?>
        <td align='center'<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>><?php if($r['issystem'] && $field_allow_search) {?><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=content&c=sitemodel_field&a=public_issearch&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$r['issearch']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$r['issearch']) ? 'times' : 'check';?>"></i></a><?php } else {?><?php echo $r['issearch'] ? '<i class="fa fa-check-circle font-blue"></i>' : '<i class="fa fa-times-circle font-red"></i>';?><?php }?></td>
        <td align='center'<?php if(!$modelid || $modelid==-1 || $modelid==-2) {echo ' style="display: none;"';}?>><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=content&c=sitemodel_field&a=public_isadd&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$r['isadd']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$r['isadd']) ? 'times' : 'check';?>"></i></a></td>
        <td align='center'><?php if(!in_array($r['field'],$forbid_fields)) {?><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=content&c=sitemodel_field&a=disabled&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>', 1);" class="badge badge-<?php echo $r['disabled'] ? 'no' : 'yes';?>"><i class="fa fa-<?php echo $r['disabled'] ? 'times' : 'check';?>"></i></a><?php } else {?><?php echo $r['disabled'] ? '<i class="fa fa-check-circle font-blue"></i>' : '<i class="fa fa-times-circle font-red"></i>';?><?php }?></td>
        <td align='center'> <a class="btn btn-xs green" href="?m=content&c=sitemodel_field&a=edit&modelid=<?php echo $r['modelid']?>&fieldid=<?php echo $r['fieldid']?>&menuid=<?php echo $this->input->get('menuid')?>"> <i class="fa fa-edit"></i> <?php echo L('edit');?></a></td>
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
        <button type="button" onclick="ajax_option('?m=content&c=sitemodel_field&a=delete&modelid=<?php echo $modelid?>&menuid=<?php echo $this->input->get('menuid');?>', '<?php echo L('你确定要删除它们吗？');?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
