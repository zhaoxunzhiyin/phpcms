<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th><?php echo L('title')?></th>
            <th width="14%" align="center"><?php echo L('startdate')?></th>
            <th width="14%" align="center"><?php echo L('enddate')?></th>
            <th width='20%' align="center"><?php echo L('inputtime')?></th>
        </tr>
    </thead>
<tbody>
<?php
if(is_array($infos)){
    foreach($infos as $info){
        ?>
    <tr onclick="return_id(<?php echo $info['subjectid'];?>, '<?php echo new_addslashes($info['subject'])?>')" style="cursor:hand" title="<?php echo L('check_select')?>">
        <td><?php if($target=='dialog') {?><label class="mt-radio mt-radio-outline"><input type='radio' id="voteid_<?php echo $info['subjectid']?>" name="subjectid"><span></span></label><?php } echo $info['subject']?></td>
        <td><?php echo dr_date(strtotime($info['fromdate']), 'Y-m-d', 'red');?></td>
        <td><?php echo dr_date(strtotime($info['todate']), 'Y-m-d', 'red');?></td>
        <td><?php echo dr_date($info['addtime'], 'Y-m-d h-i', 'red');?></td>
    </tr>
    <?php
    }
}
?>
</tbody>
</table>
</div>
<input type="hidden" name="msg_id" id="msg_id">
<div class="row">
    <div class="col-md-12 col-sm-12 text-right"><?php echo $pages?></div>
</div>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
    function return_id(voteid, title) {
    <?php if ($target=='dialog') {?>
    $('#voteid_'+voteid).prop('checked', 'true');
    $('#msg_id').val('vote|'+voteid+'|'+title);
    dialogOpener.$('#voteid').val(voteid);<?php if(!$target) {?>ownerDialog.close(); <?php }?>
    <?php } else{?>
    dialogOpener.$S('voteid').value=voteid;<?php if(!$target) {?>ownerDialog.close(); <?php }?>
    <?php }?>
}
//-->
</SCRIPT>
</body>
</html>
