<?php
defined('IN_ADMIN') or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#import_name").formValidator({onshow:"<?php echo L('importname_must')?>",onfocus:"<?php echo L('input_importname')?>"}).inputValidator({min:1,onerror:"<?php echo L('input_importname')?>"}).ajaxValidator({type : "get",url : "",data :"m=import&c=import&a=check_import_name&importid=<?php echo $importid;?>",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('name_is_exit')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed();;
	$('#dbhost').formValidator({onshow:"<?php echo L('dbhost_infos')?>",onfocus:"<?php echo L('please_check_dbhost')?>",oncorrect:"<?php echo L('input_isok')?>"}).inputValidator({min:4,onerror:"<?php echo L('please_check_dbhost')?>"});	
	});
//-->
</script>
<div class="pad-10">
<form action="?m=import&c=import&a=import_setting&importid=<?php echo $importid;?>" method="post" id="myform">
<input type="hidden" name="type" value="member">
<input name="setting[modelid]" type="hidden" value="<?php echo $modelid;?>">
<input name="importid" type="hidden" value="<?php echo $importid;?>">
<fieldset>
	<legend><?php echo L('import_type_member')?></legend>
	<table width="100%"  class="table_form">
	
	<tr>
		<th width="100"><?php echo L('import_name')?> :</th>
		<td><input type="text" name="setting[import_name]" id="import_name"
			size="30" class="input-text" value="<?php echo $setting['import_name']?>" ></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('import_desc')?> :</th>
		<td><input type="text" name="setting[desc]" id="desc"
			size="30" class="input-text" value="<?php echo $setting['desc']?>"></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('dbhost')?> :</th>
		<td><select name="setting[dbtype]" id="dbtype" onchange="if(this.value=='mysql'){$('#db_charset').show();$('#addItem').removeAttr('disabled');}else{$('#db_charset').hide();$('#addItem').attr('disabled','false');}">
			<option value="mysql"  <?php echo $setting['dbtype']=='mysql' ? 'selected' : ''?>>mysql</option>
			<option value="access" <?php echo $setting['dbtype']=='access' ? 'selected' : ''?>>access</option>
		</select></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbhost')?> :</th>
		<td><input type="text" name="setting[dbhost]" id="dbhost"
			size="30" class="input-text" value="<?php echo $setting['dbhost']?>"></td>
	</tr>
	<tr>
		<th width="100"><?php echo L('dbusername')?> :</th>
		<td><input type="text" name="setting[dbuser]" id="dbuser"
			size="30" class="input-text" value="<?php echo $setting['dbuser']?>"></td>
	</tr>
	<tr>
		<th width="100"><?php echo L('dbpassword')?> :</th>
		<td><input type="password" name="setting[dbpassword]" id="dbpassword"
			size="30" class="input-text" value="<?php echo $setting['dbpassword']?>"></td>
	</tr>
	<tr id="db_charset"  <?php if($setting['dbtype'] && $setting['dbtype']!='mysql'){echo 'style="display:none;"';}?>>
    <th width="100"><?php echo L('dbcharset')?> :</th>
    <td> 
    <?php echo form::radio(array('gbk'=>'GBK', 'gb2312'=>'gb2312', 'utf8mb4'=>'UTF8MB4', 'utf8'=>'UTF8','latin1'=>'latin1'),$setting['dbcharset'],'name="setting[dbcharset]" id="dbcharset"',L('please_select'));?>
	</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbname')?> :</th>
		<td>
		<input type="text" name="setting[dbname]" id="dbname" size="30" class="input-text" onChange="get_tables(this.value)" value="<?php echo $setting['dbname']?>">
		<input type="button" id="testdb" value="<?php echo L('test_con')?>" class="button" >
		</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbtables')?> :</th>
		<td>
		<input type="text" name="setting[dbtables]" id="db_tables" size="30" class="input-text" value="<?php echo $setting['dbtables']?>"><span id="select_tables"></span>
		<input type="button" id="addItem" value="<?php echo L('show_tables')?>" class="button" onClick="get_tables()" <?php if($_GET['dbtype']=='access' || $setting['dbtype']=='access'){echo 'disabled';} ?>><?php echo L('access_input_table');?>
		</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('condition')?> :</th>
		<td><input type="text" name="setting[condition]" id="condition"
			size="40" class="input-text" value="<?php echo $setting['condition']?>"> <?php echo L('condition_info')?></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('keyid')?> :</th>
		<td><input type="text" name="setting[keyid]" id="keyid"
			size="30" class="input-text" value="<?php echo $setting['keyid']?>"> <?php echo L('keyid_info');?></td>
	</tr> 
	
	<tr>
		<th width="100"><?php echo L('maxid')?> :</th>
		<td><input type="text" name="setting[maxid]" id="maxid"
			size="30" class="input-text" value="<?php echo $setting['maxid']?>"> <?php echo L('maxid_info');?></td>
	</tr>
    
</table>

<div class="bk15"></div>
</fieldset>
<br>
<fieldset>
	<legend><?php echo L('field_dy')?></legend>
	<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="160" align="right"><?php echo L('field_name')?>:</th>
			<th width="400" align="left"><?php echo L('field_pdo_name')?></th>
			<th width="150" align="left"><?php echo L('field_values')?></th>
			<th width="300" align="left"><?php echo L('field_func')?></th> 
		</tr>
	</thead>
<tbody>

 
		
<!-- 会员字段循环 -->
	<?php
	if(is_array($fields) && !empty($fields)){
	foreach($fields as $k=>$field){
	?>
	<tr height="40">
			<th width="160" align="right"><?php echo $field['name'];?>&nbsp;(<?php echo $k;?>) :</th>
			<th width="400" align="left" class="list_fields">
			<input name="setting[<?php echo $k;?>][field]" id="field_<?php echo $k;?>" class="input_blur" type="text" value="<?=$setting[$k]['field']?$setting[$k]['field']:''?>"><span id="test"></span>
			</th>
			<th width="150" align="left"><input name="setting[<?=$k?>][value]" class="input_blur" type="text" value="<?=$setting[$k]['value']?$setting[$k]['value']:''?>"></th>
			<th width="300" align="left">
			<input name="setting[<?php echo $k;?>][func]" class="" type="text" value="<?=$setting[$k]['func']?$setting[$k]['func']:''?>" onChange="test_func(this)"><span id="test_func"></span> 
			</th> 
	</tr>
	<?php }}?>
	
</tbody>
</table>
</div>
<div class="bk15"></div>
</fieldset>

<br>
<fieldset>
	<legend><?php echo L('import_lanmu_dy')?></legend>
	<table width="100%" class="table_form">
     <tbody> 
 	<tr>
		<th width="150"><?php echo L('defaultgroupid')?>：</th>
		<td>
		
		<select name="setting[defaultgroupid]" id="setting[defaultgroupid]">
		<option value=""><?php echo L('please_select')?></option>
		<?php foreach($new_group as $s=>$g){?>
		<option value="<?php echo $s;?>" <?php if($setting['defaultgroupid']==$s){echo 'selected style="color:red"';}?>><?php echo $g;?></option>
		<?php }?>
 		</select>
 		
  		</td>
	</tr> 
	
 	<tr>
		<th width="150"><?php echo L('v9_group')?> :</th>
		<td><?php echo L('old_group')?></td>
	</tr> 
	<?php
	foreach($group as $k=>$g){
	?>
	<tr>
		<th width="150"><?php echo $g['name'];?> :</th>
		<td><input type="text" name="setting[groupids][<?php echo $k;?>]" size="15" class="input_blur" <?php if(isset($setting['groupids'][$k])){?>value="<?=$setting['groupids'][$k]?>" <?php } ?>> <?php echo L('old_catid_info')?></td>
	</tr>
	<?php }?>
	 </tbody>
	 </table>
  </fieldset>

<br>
<fieldset>
	<legend><?php echo L('import_setting')?></legend>
	<table width="100%" class="table_form">
     <tbody> 
     <tr>
		<th width="150"><?php echo L('membercheck')?>:</th>
		<td>
          <?php if($setting['membercheck']){?>
          <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[membercheck]" value="1" <?php if($setting['membercheck']==1){echo 'checked';}?> class="radio_style"> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[membercheck]" value="0" class="radio_style" <?php if($setting['membercheck']=='0'){echo 'checked';}?>> <?php echo L('no')?> <span></span></label>
          <?php }else{?>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[membercheck]" value="1" checked class="radio_style"> <?php echo L('yes')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="setting[membercheck]" value="0" class="radio_style"> <?php echo L('no')?> <span></span></label>
          </div>
          <?php }?>
		
		</td>
	</tr>
	
 	<tr>
		<th width="150"><?php echo L('number')?> :</th>
		<td><input type="text" name="setting[number]" id="number" size="10" class="input-text" value="<?php if($setting['number']){echo $setting['number'];}else{echo '20';}?>"><?php echo L('tiao')?></td>
	</tr> 
 	<tr>
		<th width="150"><?php echo L('expire')?> :</th>
		<td><input type="text" name="setting[expire]" id="expire" size="10" class="input-text" value="<?php if($setting['expire']){echo $setting['expire'];}else{echo '30';}?>"><?php echo L('miao')?></td>
	</tr> 
	 </tbody>
	 </table>
<div class="bk15"></div>
<input type="submit" id="dosubmit" name="dosubmit" class="button" value="<?php echo L('submit')?>" />
</fieldset>
</form>
</div>
</body>
</html>
<script type="text/javascript">
var html='';
var id = '';

$(".input_blur").click(function(){
	$(".list_fields").children('span').html('&nbsp;');
	id = $(this).attr('id');
	if(html!='' && html != 'no'){
		$(this).parent('th').children('span').html(html);
	}else{
		html = $.ajax({
		type: "GET",
 		data:'m=import&c=import&a=get_fields&dbtype='+$('#dbtype').val()+'&dbhost='+$('#dbhost').val()+'&dbuser='+$('#dbuser').val()+'&dbpassword='+$('#dbpassword').val()+'&dbname='+$('#dbname').val()+'&charset='+$('#charset').val()+'&tables='+$('#db_tables').val()+'',
		async: false 
		}).responseText;
		if(html!='' && html != 'no'){
			$(this).parent('th').children('span').html(html);
		}
	}
});

function put_fields(obj){
	if(obj!='')	{
		$("#"+id).val(obj);
	}
}
$('#testdb').click(function(){
	$.get("?m=import&c=import&a=testdb&pc_hash=<?php echo $_SESSION[pc_hash];?>", {dbtype:$('#dbtype').val(), dbhost:$('#dbhost').val(), dbuser:$('#dbuser').val(), dbpassword:$('#dbpassword').val(), dbname:$('#dbname').val()}, function(data) {
	if(data=='OK') 
	{
		Dialog.alert('<?php echo L('connect_succeed')?>');
	}
	else
	{
		Dialog.alert('<?php echo L('connect_fail')?>');
	}
	});
});

//处理函数
function test_func(obj){
  	var thisblur = $(obj),val = thisblur.val();
   	$.get("?m=import&c=import&a=test_func&pc_hash=<?php echo $_SESSION[pc_hash];?>",{value:val}, function(data){
   		thisblur.parent('th').children('span').html(data);
 	});
}

function get_tables()
{
	if($('#dbtype').val() != 'mysql') return false;
	$.get("?m=import&c=import&a=get_tables&pc_hash=<?php echo $_SESSION['pc_hash']?>",{dbtype:$('#dbtype').val(), dbhost:$('#dbhost').val(), dbuser:$('#dbuser').val(), dbpassword:$('#dbpassword').val(), dbname:$('#dbname').val(), dbcharset:$('#dbcharset').val()}, function(data){
		$("#select_tables").html(data);
	});
}

function in_tables(val){
	if($('#db_tables').val()!=''){
		$('#db_tables').val($('#db_tables').val()+','+val);
	}else{
		$('#db_tables').val(val);
	}
}
</script>