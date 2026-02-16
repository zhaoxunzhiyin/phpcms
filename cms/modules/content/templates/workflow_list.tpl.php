<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box">
<form name="myform" action="?m=content&c=type_manage&a=listorder" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
        <thead>
    <tr>
    <th width="80">ID</th>
    <th width="180" align="left"><?php echo L('workflow_name');?></th>
    <th width="180"><?php echo L('steps');?></th>
    <th width="120"><?php echo L('workflow_diagram');?></th>
    <th><?php echo L('description');?></th>
    <th><?php echo L('operations_manage');?></th>
    </tr>
        </thead>
    <tbody>
    

<?php
$steps[1] = L('steps_1');
$steps[2] = L('steps_2');
$steps[3] = L('steps_3');
$steps[4] = L('steps_4');
foreach($datas as $r) {
?>
<tr>
<td align="center"><?php echo $r['workflowid']?></td>
<td ><?php echo $r['workname']?></td>
<td align="center"><?php echo $steps[$r['steps']]?></td>
<td align="center"><a class="btn btn-xs yellow" href="javascript:view('<?php echo $r['workflowid']?>','<?php echo $r['workname']?>')"><?php echo L('onclick_view');?></a></td>
<td ><?php echo $r['description']?></td>
<td align="center"><a class="btn btn-xs green" href="javascript:edit('<?php echo $r['workflowid']?>','<?php echo $r['workname']?>')"> <i class="fa fa-edit"></i> <?php echo L('edit');?></a> <a class="btn btn-xs red" href="javascript:;" onclick="data_delete(this,'<?php echo $r['workflowid']?>','<?php echo L('confirm',array('message'=>$r['workname']));?>')"> <i class="fa fa-trash"></i> <?php echo L('delete')?></a> </td>
</tr>
<?php } ?>
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
<script type="text/javascript"> 
<!--
function edit(id, name) {
    artdialog('edit','?m=content&c=workflow&a=edit&workflowid='+id,'<?php echo L('edit_workflow');?>《'+name+'》',680,500);
}
function view(id, name) {
    omnipotent('view','?m=content&c=workflow&a=view&workflowid='+id,'<?php echo L('workflow_diagram');?>《'+name+'》',1,580,300);
}
function data_delete(obj,id,name){
    Dialog.confirm(name,function(){
        $.get('?m=content&c=workflow&a=delete&workflowid='+id+'&pc_hash=<?php echo dr_get_csrf_token();?>',function(data){
            if(data) {
                $(obj).parent().parent().fadeOut("slow");
            }
        })     
    });
};
//-->
</script>
</body>
</html>
