<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
	$("#type_name").formValidator({onshow:"<?php echo L("input").L('slider_postion_name')?>",onfocus:"<?php echo L("input").L('slider_postion_name')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('slider_postion_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=slider&c=slider&a=public_check_name&typeid=<?php echo $typeid;?>",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('slider_postion_name').L('exists')?>",onwait : "<?php echo L('connecting')?>"}).defaultPassed(); 
	 
	})
//-->
</script>
<div class="pad-lr-10">
<form action="?m=slider&c=slider&a=edit_type&typeid=<?php echo $typeid; ?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="60"><?php echo L('slider_postion_name')?>：</th>
		<td><input type="text" name="type[name]" id="type_name"
			size="30" class="input-text" value="<?php echo $name;?>"></td>
	</tr>

	<input
		type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> ">
	 

</table>
</form>
</div>
</body>
</html>
