<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<script type="text/javascript">
$(document).ready(function() {
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg)}});
	$("#name").formValidator({onshow:"<?php echo L('input').L('nodename')?>",onfocus:"<?php echo L('input').L('nodename')?>"}).inputValidator({min:1,onerror:"<?php echo L('input').L('nodename')?>"}).ajaxValidator({type : "get",url : "",data :"m=collection&c=node&a=public_name",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('nodename').L('exists')?>",onwait : "<?php echo L('connecting')?>"});

});
</script>
<div class="pad-10">
<form name="myform" action="?m=collection&c=node&a=node_import" method="post" id="myform"  enctype="multipart/form-data">
<div class="common-form">
	<table width="100%" class="table_form">
		<tr>
			<td width="120"><?php echo L('collect_call')?>：</td> 
			<td>
			<input type="text" name="name" id="name"  class="input-text" value="" />
			</td>
		</tr>
		<tr>
			<td width="120"><?php echo L('cfg')?>：</td> 
			<td>
			<input type="text" class='input-text' onchange="FileName.value=this.value" id="myfile" name="myfile" size="26" style="width: 160px;height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;border-radius: 4px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">&nbsp;<div class="button green uploader"><i class="fa fa-plus"></i> <?php echo L('select_file');?> <input type="file" name="file" id="file" onchange="myfile.value=this.value"></div>
			<br /><?php echo L('only_support_txt_file_upload')?>
			</td>
		</tr>
	</table>
    <input name="dosubmit" type="submit" id="dosubmit" value="<?php echo L('submit')?>" class="dialog">
</div>
</div>
</form>

</body>
</html>