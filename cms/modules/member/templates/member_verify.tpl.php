<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>

<form name="myform" id="myform" action="?m=member&c=member_verify&a=delete" method="post"  onsubmit="checkuid();return false;">
<div class="pad-lr-10">
<div class="table-list">

<table width="100%" cellspacing="0">
        <thead>
            <tr>
			<th  align="left" width="20" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('userid[]');" />
                        <span></span>
                    </label></th>
			<th align="left"><?php echo L('username')?></th>
			<th align="left"><?php echo L('email')?></th>
			<th align="left"><?php echo L('regtime')?></th>
			<th align="left"><?php echo L('model_name')?></th>
			<th align="left"><?php echo L('verify_message')?></th>
			<th align="left"><?php echo L('verify_status')?></th>
            </tr>
        </thead>
    <tbody>
<?php
	foreach($memberlist as $k=>$v) {
?>
    <tr>
		<td align="left" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['userid']?>" name="userid[]" />
                        <span></span>
                    </label></td>
		<td align="left"><?php echo $v['username']?></td>
		<td align="left"><?php echo $v['email']?></td>
		<td align="left" title="<?php echo $v['regip']?>"><?php echo format::date($v['regdate'], 1);?></td>
		<td align="left"><a href="javascript:member_verify(<?php echo $v['userid']?>, '<?php echo $v['modelid']?>', '')"><?php echo $member_model[$v['modelid']]['name']?><img src="<?php echo IMG_PATH?>admin_img/detail.png"></a></td>
		<td align="left"><?php echo $v['message']?></td>
		<td align="left"><?php $verify_status = array('5'=>L('nerver_pass'), '4'=>L('reject'), '3'=>L('delete'), '2'=>L('ignore'), '0'=>L('need_verify'), '1'=>L('pass')); echo $verify_status[$v['status']]?></td>
    </tr>
<?php
	}
?>
 </tbody>
</table>
<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label>
<input type="submit" class="button" name="dosubmit" value="<?php echo L('verify_pass')?>" onclick="document.myform.action='?m=member&c=member_verify&a=pass'"/>

<input type="submit" class="button" name="dosubmit" value="<?php echo L('reject')?>" onclick="document.myform.action='?m=member&c=member_verify&a=reject'"/>

<input type="button" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});"/>

<input type="submit" class="button" name="dosubmit" value="<?php echo L('ignore')?>" onclick="document.myform.action='?m=member&c=member_verify&a=ignore'"/>

<?php echo L('verify_message')?>：<input type="text" name="message"><input type="checkbox" value=1 name="sendemail" checked/><?php echo L('sendemail')?>
</div> 
<div id="pages"><?php echo $pages?></div>
</div>
</div>
</form>
<script type="text/javascript">
<!--

function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert('<?php echo L('plsease_select').L('member')?>');
		return false;
	} else {
		myform.submit();
	}
}

function member_verify(userid, modelid, name) {
	artdialog('modelinfo','?m=member&c=member_verify&a=modelinfo&userid='+userid+'&modelid='+modelid,'<?php echo L('member_verify')?>',700,500);
}
//-->
</script>
</body>
</html>