<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="member" name="m">
<input type="hidden" value="member" name="c">
<input type="hidden" value="search" name="a">
<input type="hidden" value="879" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
				
				<?php echo L('regtime')?>：
				<?php echo form::date('start_time', $start_time)?>-
				<?php echo form::date('end_time', $end_time)?>
				<?php if($_SESSION['roleid'] == 1) {?>
				<?php echo form::select($sitelist, $siteid, 'name="siteid"', L('all_site'));}?>
							
				<select name="status">
					<option value='0' <?php if(isset($_GET['status']) && $_GET['status']==0){?>selected<?php }?>><?php echo L('status')?></option>
					<option value='1' <?php if(isset($_GET['status']) && $_GET['status']==1){?>selected<?php }?>><?php echo L('lock')?></option>
					<option value='2' <?php if(isset($_GET['status']) && $_GET['status']==2){?>selected<?php }?>><?php echo L('normal')?></option>
				</select>
				<?php echo form::select($modellist, $modelid, 'name="modelid"', L('member_model'))?>
				<?php echo form::select($grouplist, $groupid, 'name="groupid"', L('member_group'))?>
				
				<select name="type">
					<option value='1' <?php if(isset($_GET['type']) && $_GET['type']==1){?>selected<?php }?>><?php echo L('username')?></option>
					<option value='2' <?php if(isset($_GET['type']) && $_GET['type']==2){?>selected<?php }?>><?php echo L('uid')?></option>
					<option value='3' <?php if(isset($_GET['type']) && $_GET['type']==3){?>selected<?php }?>><?php echo L('email')?></option>
					<option value='4' <?php if(isset($_GET['type']) && $_GET['type']==4){?>selected<?php }?>><?php echo L('regip')?></option>
					<option value='5' <?php if(isset($_GET['type']) && $_GET['type']==5){?>selected<?php }?>><?php echo L('nickname')?></option>
				</select>
				
				<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];}?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" id="myform" action="?m=member&c=member&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left" width="20" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('userid[]');" />
                        <span></span>
                    </label></th>
			<th align="left"></th>
			<th align="left"><?php echo L('uid')?></th>
			<th align="left"><?php echo L('username')?></th>
			<th align="left"><?php echo L('nickname')?></th>
			<th align="left"><?php echo L('email')?></th>
			<th align="left"><?php echo L('member_group')?></th>
			<th align="left"><?php echo L('regip')?></th>
			<th align="left"><?php echo L('lastlogintime')?></th>
			<th align="left"><?php echo L('amount')?></th>
			<th align="left"><?php echo L('point')?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($memberlist)){
	foreach($memberlist as $k=>$v) {
?>
    <tr>
		<td align="left" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" value="<?php echo $v['userid']?>" name="userid[]" />
                        <span></span>
                    </label></td>
		<td align="left"><?php if($v['islock']) {?><img onmouseover="layer.tips('<?php echo L('lock')?>',this,{tips: [1, '#000']});" onmouseout="layer.closeAll();" src="<?php echo IMG_PATH?>icon/icon_padlock.gif"><?php }?></td>
		<td align="left"><?php echo $v['userid']?></td>
		<td align="left"><img src="<?php echo $v['avatar']?>" height=18 width=18 onerror="this.src='<?php echo IMG_PATH?>member/nophoto.gif'"><?php if($v['vip']) {?><img title="<?php echo L('vip')?>" src="<?php echo IMG_PATH?>icon/vip.gif"><?php }?><?php echo $v['username']?><a href="javascript:member_infomation(<?php echo $v['userid']?>, '<?php echo $v['modelid']?>', '')"><?php echo $member_model[$v['modelid']]['name']?><img src="<?php echo IMG_PATH?>admin_img/detail.png"></a></td>
		<td align="left"><?php echo new_html_special_chars($v['nickname'])?></td>
		<td align="left"><?php echo $v['email']?></td>
		<td align="left"><?php echo $grouplist[$v['groupid']]?></td>
		<td align="left"><?php echo $v['regip']?></td>
		<td align="left"><?php echo format::date($v['lastdate'], 1);?></td>
		<td align="left"><?php echo $v['amount']?></td>
		<td align="left"><?php echo $v['point']?></td>
		<td align="left">
			<a href="javascript:edit(<?php echo $v['userid']?>, '<?php echo $v['username']?>')">[<?php echo L('edit')?>]</a>
			<a href="?m=member&c=member&a=alogin_index&id=<?php echo $v['userid']?>" target="_blank">[<?php echo L('login')?>]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label> <input type="button" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="Dialog.confirm('<?php echo L('sure_delete')?>',function(){$('#myform').submit();});"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=member&c=member&a=lock'" value="<?php echo L('lock')?>"/>
<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=member&c=member&a=unlock'" value="<?php echo L('unlock')?>"/>
<input type="button" class="button" name="dosubmit" onclick="move();return false;" value="<?php echo L('move')?>"/>
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	artdialog('edit','?m=member&c=member&a=edit&userid='+id,'<?php echo L('edit').L('member')?>《'+name+'》',700,500);
}
function move() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		Dialog.alert('<?php echo L('plsease_select').L('member')?>');
		return false;
	}
	artdialog('move','?m=member&c=member&a=move&ids='+ids,'<?php echo L('move').L('member')?>',700,500);
}

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

function member_infomation(userid, modelid, name) {
	artdialog('modelinfo','?m=member&c=member&a=memberinfo&userid='+userid+'&modelid='+modelid,'<?php echo L('memberinfo')?>',700,500);
}

//-->
</script>
</body>
</html>