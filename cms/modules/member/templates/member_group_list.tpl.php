<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>
<div class="right-card-box">
<form name="myform" id="myform" action="" method="post">
<input name="dosubmit" type="hidden" value="1">
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
            <th style="text-align: center;" width="100">ID</th>
            <th style="text-align: center;" width="70"><?php echo L('sort')?></th>
            <th style="text-align: center;"><?php echo L('groupname')?></th>
            <th style="text-align: center;"><?php echo L('issystem')?></th>
            <th style="text-align: center;"><?php echo L('membernum')?></th>
            <th style="text-align: center;"><?php echo L('starnum')?></th>
            <th style="text-align: center;"><?php echo L('pointrange')?></th>
            <th style="text-align: center;"><?php echo L('allowattachment')?></th>
            <th style="text-align: center;"><?php echo L('allowpost')?></th>
            <th style="text-align: center;"><?php echo L('member_group_publish_verify')?></th>
            <th style="text-align: center;"><?php echo L('allowsearch')?></th>
            <th style="text-align: center;"><?php echo L('allowupgrade')?></th>
            <th style="text-align: center;"><?php echo L('allowsendmessage')?></th>
            <th><?php echo L('operation')?></th>
        </tr>
    </thead>
<tbody>
<?php
    foreach($member_group_list as $k=>$v) {
?>
    <tr class="odd gradeX" id="dr_row_<?php echo $v['groupid'];?>">
        <td class="myselect">
            <?php if(!$v['issystem']) {?><label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                <input type="checkbox" class="checkboxes" name="ids[]" value="<?php echo $v['groupid'];?>" />
                <span></span>
            </label><?php }?>
        </td>
        <td style="text-align: center;"><?php echo $v['groupid']?></td>
        <td style="text-align: center;"><input type="text" onblur="dr_ajax_save(this.value, '<?php echo '?m=member&c=member_group&a=sort&groupid='.$v['groupid'].'&menuid='.$this->input->get('menuid');?>')" value="<?php echo $v['sort'];?>" class="displayorder form-control input-sm input-inline input-mini"></th>
        <td style="text-align: center;" class="tooltips" data-container="body" data-placement="top" data-original-title="<?php echo $v['description']!='' ? $v['description'] : $v['name'];?>"><?php echo $v['name']?></td>
        <td style="text-align: center;"><?php echo $v['issystem'] ? '<i class="fa fa-check-circle font-blue"></i>' : '<i class="fa fa-times-circle font-red"></i>';?></td>
        <td style="text-align: center;"><?php echo $v['membernum']?></th>
        <td style="text-align: center;"><?php echo $v['starnum']?></td>
        <td style="text-align: center;"><?php echo $v['point']?></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowattachment&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowattachment']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowattachment']) ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowpost&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowpost']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowpost']) ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowpostverify&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowpostverify']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowpostverify']) ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowsearch&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowsearch']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowsearch']) ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowupgrade&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowupgrade']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowupgrade']) ? 'times' : 'check';?>"></i></a></td>
        <td style="text-align: center;"><a href="javascript:;" onclick="dr_ajax_open_close(this, '?m=member&c=member_group&a=public_option&groupid=<?php echo $v['groupid']?>&name=allowsendmessage&menuid=<?php echo $this->input->get('menuid')?>', 0);" class="badge badge-<?php echo (!$v['allowsendmessage']) ? 'no' : 'yes';?>"><i class="fa fa-<?php echo (!$v['allowsendmessage']) ? 'times' : 'check';?>"></i></a></td>
        <td><a class="btn btn-xs green" href="javascript:edit(<?php echo $v['groupid']?>, '<?php echo $v['name']?>')"> <i class="fa fa-edit"></i> <?php echo L('edit')?></a></td>
    </tr>
<?php
    }
?>
</tbody>
 </table>
</div>
<div class="row list-footer table-checkable">
    <div class="col-md-5 list-select">
        <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
            <span></span>
        </label>
        <label><button type="button" onclick="ajax_option('?m=member&c=member_group&a=delete', '<?php echo L('sure_delete')?>', 1)" class="btn red btn-sm"> <i class="fa fa-trash"></i> <?php echo L('delete');?></button></label>
    </div>
    <div class="col-md-7 list-page"><?php echo $pages?></div>
</div>
</form>
<script language="JavaScript">
function edit(id, name) {
    artdialog('edit','?m=member&c=member_group&a=edit&groupid='+id,'<?php echo L('edit').L('member_group')?>《'+name+'》',700,500);
}
</script>
</div>
</div>
</div>
</div>
</body>
</html>