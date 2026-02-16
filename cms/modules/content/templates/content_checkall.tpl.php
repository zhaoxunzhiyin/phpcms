<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
    <?php if(is_mobile()) {?>
    <div class="content-menu btn-group dropdown-btn-group"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-th-large"></i> <?php echo L('菜单')?> <i class="fa fa-angle-down"></i></a>
        <ul class="dropdown-menu">
        <?php if($super_admin) {?>
        <li><a class="add tooltips<?php if(!$status && $status!=0) echo ' on';?>" href="?m=content&c=content&a=public_checkall&menuid=<?php echo $this->input->get('menuid');?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('all_check_list');?>"><i class="fa fa-check"></i> <?php echo L('all_check_list');?></a></li>
<?php } else {
    echo '<li><a class="on"><i class="fa fa-check"></i> '.L('check_status').'</a></li>';
}
for ($j=0;$j<5;$j++) {
?>
            <div class="dropdown-line"></div>
            <li><a href='?m=content&c=content&a=public_checkall&menuid=<?php echo $this->input->get('menuid');?>&status=<?php echo $j;?>' class="tooltips<?php if($status==$j) echo ' on';?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('workflow_'.$j);?>"><i class="fa fa-<?php if($j) {?>sort-numeric-asc<?php } else {?>sign-out<?php }?>"></i> <?php echo L('workflow_'.$j);?></a></li>
<?php }?>
        </ul>
    </div>
    <?php } else {?>
<div class="content-menu ib-a">
<?php if($super_admin) {?>
<a href='?m=content&c=content&a=public_checkall&menuid=<?php echo $this->input->get('menuid');?>' class="tooltips<?php if(!$status && $status!=0) echo ' on';?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('all_check_list');?>"><i class="fa fa-check"></i> <?php echo L('all_check_list');?></a>
<?php } else {
    echo '<a class="on"><i class="fa fa-check"></i> '.L('check_status').'</a>';
}
for ($j=0;$j<5;$j++) {
?>
<i class="fa fa-circle"></i><a href='?m=content&c=content&a=public_checkall&menuid=<?php echo $this->input->get('menuid');?>&status=<?php echo $j;?>' class="tooltips<?php if($status==$j) echo ' on';?>" data-container="body" data-placement="bottom" data-original-title="<?php echo L('workflow_'.$j);?>"><i class="fa fa-<?php if($j) {?>sort-numeric-asc<?php } else {?>sign-out<?php }?>"></i> <?php echo L('workflow_'.$j);?></a>
<?php }?>
</div>
    <?php }?>
</div>
<div class="content-header"></div>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="portlet-body">
<div class="right-card-box">
<form name="myform" id="myform" action="" method="post" >
<div class="table-list">
    <table width="100%">
        <thead>
            <tr>
            <th width="60">ID</th>
            <th><?php echo L('title');?></th>
            <th><?php echo L('select_model_name');?></th>
            <th width="110"><?php echo L('current_steps');?></th>
            <th width="80"><?php echo L('steps');?></th>
            <th width="100"><?php echo L('belong_category');?></th>
            <th width="180"><?php echo L('contribute_time');?></th>
            <th><?php echo L('username');?></th>
            <th><?php echo L('operations_manage');?></th>
            </tr>
        </thead>
<tbody>
    <?php
    $model_cache = getcache('model','commons');
    foreach ($datas as $r) {
        $arr_checkid = explode('-',$r['checkid']);

        $workflowid = $this->categorys[$r['catid']]['workflowid'];
        if($stepid = $workflows[$workflowid]['steps']) {
            $stepname = L('steps_'.$stepid);
        } else {
            $stepname = '';
        }
        $modelname = $model_cache[$arr_checkid[2]]['name'];
        $flowname = L('workflow_'.$r['status']);
    ?>
        <tr>
        <td align='center' ><?php echo $arr_checkid[1];?></td>
        <td align='left' ><a href="javascript:;" onclick='change_color(this);window.open("?m=content&c=content&a=public_preview&steps=<?php echo $r['status']?>&catid=<?php echo $r['catid'];?>&id=<?php echo $arr_checkid[1];?>&pc_hash=<?php echo dr_get_csrf_token();?>","manage")'><?php echo $r['title'];?></a></td>
        <td align='center' ><?php echo $modelname;?></td>
        <td align='center' ><?php echo $flowname;?></td>
        <td align='center' ><?php echo $stepname;?></td>
        <td align='center' ><a href="?m=content&c=content&a=init&menuid=<?php echo $this->input->get('menuid');?>&catid=<?php echo $r['catid'];?>"><?php echo $this->categorys[$r['catid']]['catname'];?></a></td>
        <td align='center' ><?php echo dr_date($r['inputtime'], null, 'red');?></td>
        <td align='center'>
        <?php
        if($r['sysadd']==0) {
            echo "<a href='?m=member&c=member&a=memberinfo&username=".urlencode($r['username'])."' >".$r['username']."</a>"; 
            echo '<img src="'.IMG_PATH.'icon/contribute.png" title="'.L('member_contribute').'">';
        } else {
            echo $r['username'];
        }
        ?></td>
        <td align='center'><a href="javascript:;" onclick='change_color(this);window.open("?m=content&c=content&a=public_preview&steps=<?php echo $r['status']?>&catid=<?php echo $r['catid'];?>&id=<?php echo $arr_checkid[1];?>&pc_hash=<?php echo dr_get_csrf_token();?>","manage")'><?php echo L('c_check');?></a></td>
    </tr>
     <?php }?>
</tbody>
     </table>
</div>
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</form>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript"> 
<!--
window.top.$("#current_pos_attr").html('<?php echo L('checkall_content');?>');
function change_color(obj) {
    $(obj).css('color','red');
}
//-->
</script>
</body>
</html>