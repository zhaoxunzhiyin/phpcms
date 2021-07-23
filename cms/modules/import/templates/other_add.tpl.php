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
<input type="hidden" name="type" value="other">
<input name="setting[modelid]" id="modelid" type="hidden" value="<?php echo $modelid;?>">
<input name="importid" type="hidden" id="importid" value="<?php echo $importid;?>">
<fieldset>
	<legend><?php echo L('import_type_other')?></legend>
	<table width="100%"  class="table_form">
	
	<tr>
		<th width="100"><?php echo L('import_name')?> :</th>
		<td><input type="text" name="setting[import_name]" id="import_name"
			size="30" class="input-text" value="<?php if($_GET['import_name']){echo $_GET['import_name'];}elseif ($setting['import_name']){echo $setting['import_name'];}?>"></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('import_desc')?> :</th>
		<td><input type="text" name="setting[desc]" id="desc"
			size="30" class="input-text" value="<?php if($_GET['desc']){echo $_GET['desc'];}elseif ($setting['desc']){echo $setting['desc'];}?>"></td>
	</tr>

	<tr>
		<th width="100"><?php echo L('dbtype')?> :</th>
		<td><select name="setting[dbtype]" id="dbtype" onchange="if(this.value=='mysql'){$('#db_charset').show();$('#addItem').removeAttr('disabled');}else{$('#db_charset').hide();$('#addItem').attr('disabled','false');}">
			<option value="mysql"  <?php if($setting['dbtype']=='mysql' || $_GET['dbtype']=='mysql') echo 'selected';?>>mysql</option>
			<option value="access" <?php if($setting['dbtype']=='access' || $_GET['dbtype']=='access') echo 'selected';?>>access</option>
		</select></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbhost')?> :</th>
		<td><input type="text" name="setting[dbhost]" id="dbhost"
			size="30" class="input-text" value="<?php if($_GET['dbhost']){echo $_GET['dbhost'];}elseif ($setting['dbhost']){echo $setting['dbhost'];}?>"></td>
	</tr>
	<tr>
		<th width="100"><?php echo L('dbusername')?> :</th>
		<td><input type="text" name="setting[dbuser]" id="dbuser"
			size="30" class="input-text" value="<?php if($_GET['dbuser']){echo $_GET['dbuser'];}elseif ($setting['dbuser']){echo $setting['dbuser'];}?>"></td>
	</tr>
	<tr>
		<th width="100"><?php echo L('dbpassword')?> :</th>
		<td><input type="password" name="setting[dbpassword]" id="dbpassword"
			size="30" class="input-text" value="<?php if($_GET['dbpassword']){echo $_GET['dbpassword'];}elseif ($setting['dbpassword']){echo $setting['dbpassword'];}?>"></td>
	</tr>
	
	<tr id="db_charset"  <?php if($_GET['dbtype']!='mysql' || $setting['dbtype']!='mysql'){echo 'style="display:none;"';}?>>
    <th width="100"><?php echo L('dbcharset')?> :</th>
    <td> 
    <?php $dbcharset = $setting['dbcharset'] ? $setting['dbcharset'] : $_GET['dbcharset'];?>
    <?php echo form::radio(array('gbk'=>'GBK','utf-8'=>'UTF8','latin1'=>'latin1'),$dbcharset,'name="setting[dbcharset]" id="dbcharset"',L('please_select'));?>
	</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbname')?> :</th>
		<td>
		<input type="text" name="setting[dbname]" id="dbname" size="30" class="input-text" onChange="get_tables(this.value)" value="<?php if($_GET['dbname']){echo $_GET['dbname'];}elseif ($setting['dbname']){echo $setting['dbname'];}?>">
		<input type="button" id="testdb" value="<?php echo L('test_con')?>" class="button" >
		</td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('dbtables')?> :</th>
		<td>
		<input type="text" name="setting[dbtables]" id="db_tables" size="30" class="input-text" value="<?php if($_GET['db_tables']){echo $_GET['db_tables'];}elseif ($setting['dbtables']){echo $setting['dbtables'];}?>"><span id="select_tables"></span>
		<input type="button" id="addItem" value="<?php echo L('show_tables')?>" class="button" onClick="get_tables()" <?php if($_GET['dbtype']=='access' || $setting['dbtype']=='access'){echo 'disabled';} ?>><?php echo L('access_input_table')?>
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
			size="30" class="input-text" value="<?php if($_GET['keyid']){echo $_GET['keyid'];}elseif ($setting['keyid']){echo $setting['keyid'];}?>">  <?php echo L('condition_info')?></td>
	</tr>
	
	<tr>
		<th width="100"><?php echo L('maxid')?> :</th>
		<td><input type="text" name="setting[maxid]" id="maxid"
			size="30" class="input-text" value="<?php echo $setting['maxid']?>"> <?php echo L('maxid_info')?></td>
	</tr>
    
</table>

<div class="bk15"></div>
</fieldset>

<br>
<fieldset>
	<legend><?php echo L('select_localhost_db')?></legend>
	<table width="100%" class="table_form">
     <tbody> 
 	<tr>
		<th width="150"><?php echo L('pdo_select')?> :</th>
		<td> 
		<?php echo form::select($pdos,$pdo_name,'name="setting[pdo_select]" id="pdo_select" onChange="get_into_tables(this.value)"',L('please_select_pdo'))?>
		<span id="select_into_tables"></span>
  		<input type="button" id="addItem" value="<?php echo L('show_tables')?>" class="button" onclick="get_into_tables()">
   		</td>
	</tr> 
	
 	<tr>
		<th width="150"><?php echo L('into_tables')?> :</th>
		<td>
		<input type="text" name="setting[into_tables]" id="into_tables" size="30" class="input-text" value="<?php if($_GET['into_tables']){echo $_GET['into_tables'];}elseif ($setting['into_tables']){echo $setting['into_tables'];}?>">
		<input type="button" id="show_keywords" value="<?php echo L('show_tables_fields')?>" class="button" onclick="get_keywords()">
		</td>
	</tr> 
	</tbody>
	</table>
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
	<?php
	if(is_array($get_keywords) && !empty($get_keywords)){
	foreach($get_keywords as $k=>$field){
	?>
	<tr height="40">
	<th width="160" align="right"><?php echo $field;?>:</th>
	<th width="400" align="left" class="list_fields">
	<input name="setting[<?php echo $field;?>][field]" id="field_<?php echo $field;?>" class="input_blur" type="text" value="<?=$setting[$field]['field']?$setting[$field]['field']:''?>"><span id="test"></span>
	</th>
	<th width="150" align="left"><input name="setting[<?php echo $field;?>][value]" class="input_blur" type="text" value="<?php if($setting[$field]['value']!=''){echo$setting[$field]['value']; }?>"></th>
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
	<legend><?php echo L('import_setting')?></legend>
	<table width="100%" class="table_form">
     <tbody> 
 	<tr>
		<th width="150"><?php echo L('number')?> :</th>
		<td><input type="text" name="setting[number]" id="number" size="10" class="input-text" value="<?php if($setting['number']){echo $setting['number'];}else {echo '20';}?>"><?php echo L('tiao')?></td>
	</tr> 
 	<tr>
		<th width="150"><?php echo L('expire')?> :</th>
		<td><input type="text" name="setting[expire]" id="expire" size="10" class="input-text" value="<?php if($setting['expire']){echo $setting['expire'];}else {echo '30';}?>"><?php echo L('miao')?></td>
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
		Dialog.alert(<?php echo L('connect_succeed')?>);
	}
	else
	{
		Dialog.alert(<?php echo L('connect_fail')?>);
	}
	});
});

function get_tables(){
	//if($('#dbtype').val() != 'mysql') return false;
	$.get("?m=import&c=import&a=get_tables&pc_hash=<?php echo $_SESSION['pc_hash']?>",{dbtype:$('#dbtype').val(), dbhost:$('#dbhost').val(), dbuser:$('#dbuser').val(), dbpassword:$('#dbpassword').val(), dbname:$('#dbname').val(), dbcharset:$('#dbcharset').val()}, function(data){
		$("#select_tables").html(data);
	});
}

//处理函数
function test_func(obj){
  	var thisblur = $(obj),val = thisblur.val();
   	$.get("?m=import&c=import&a=test_func&pc_hash=<?php echo $_SESSION[pc_hash];?>",{value:val}, function(data){
   		thisblur.parent('th').children('span').html(data);
 	});
}

//获取选择本系统的数据原对应表
function get_into_tables(){
 	$.get("?m=import&c=import&a=get_into_tables&pc_hash=<?php echo $_SESSION['pc_hash']?>",{pdo_select:$('#pdo_select').val()}, function(data){
		$("#select_into_tables").html(data);
	});
}

function in_tables(val){
	if($('#db_tables').val()!=''){
		$('#db_tables').val($('#db_tables').val()+','+val);
	}else{
		$('#db_tables').val(val);
	}
}
 
function get_keywords() {
	//把选择的数据源配置，数据表都传递给处理程序
	var into_tables = $("#into_tables").val();
	var pdo_selecte = $("#pdo_select").val();
	var importid = $("#importid").val();
	
	var import_name = $("#import_name").val();
	var desc = $("#desc").val();
	var dbtype = $("#dbtype").val();
	var dbhost = $("#dbhost").val();
	var dbuser = $("#dbuser").val();
	var dbpassword = $("#dbpassword").val();
	var dbcharset = $("#dbcharset").val();
	var dbname = $("#dbname").val();
	var db_tables = $("#db_tables").val();
	var condition = $("#condition").val();
	var keyid = $("#keyid").val();
 	location.href='?m=import&c=import&a=import_setting&type=other&modelid=other&importid='+importid+'&pdoname='+pdo_selecte+'&into_tables='+into_tables+'&import_name='+import_name+'&desc='+desc+'&dbtype='+dbtype+'&dbhost='+dbhost+'&dbuser='+dbuser+'&dbpassword='+dbpassword+'&dbname='+dbname+'&dbcharset='+dbcharset+'&db_tables='+db_tables+'&keyid='+keyid+'&pc_hash=<?php echo $_SESSION['pc_hash']?>';
}

function to_tables(val){
	if($('#into_tables').val()!=''){
		$('#into_tables').val($('#into_tables').val()+','+val);
	}else{
		$('#into_tables').val(val);
	}
}

function show_tbl(obj) {//根据选择的本系统数据源，显示该源下面的所有数据表
	var pdoname = $(obj).val();
	var importid = $("#importid").val();
	location.href='?m=import&c=import&a=import_setting&type=other&modelid=other&importid='+importid+'&pdoname='+pdoname+'&pc_hash=<?php echo $_SESSION['pc_hash']?>';
}
function showcreat(tblname, pdo_name) {
	window.top.art.dialog({title:tblname, id:'show', iframe:'?m=admin&c=database&a=public_repair&operation=showcreat&pdo_name='+pdo_name+'&tables=' +tblname,width:'500px',height:'350px'});
}
function reselect() {
	var chk = $("input[name=tables[]]");
	var length = chk.length;
	for(i=0;i < length;i++){
		if(chk.eq(i).attr("checked")) chk.eq(i).attr("checked",false);
		else chk.eq(i).attr("checked",true);
	}
}
</script>