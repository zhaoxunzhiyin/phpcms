<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
		$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
		$("#name").formValidator({onshow:"<?php echo L("input").L('model_name')?>",onfocus:"<?php echo L("input").L('model_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_name')?>"});
		$("#tablename").formValidator({onshow:"<?php echo L("input").L('model_tablename')?>",onfocus:"<?php echo L("input").L('model_tablename')?>"}).regexValidator({regexp:"^([a-zA-Z0-9]|[_]){0,20}$",onerror:"<?php echo L("model_tablename_wrong");?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=content&c=sitemodel&a=public_check_tablename",datatype : "html",async:'false',success : function(data){	if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('model_tablename').L('exists')?>",onwait : "<?php echo L('connecting')?>"});		
	})
//-->
</script>
<div class="pad-lr-10">
<form action="?m=content&c=sitemodel&a=import" method="post" id="myform" enctype="multipart/form-data">
<fieldset>
	<legend><?php echo L('basic_configuration')?></legend>
	<table width="100%"  class="table_form">
  <tr>
    <th width="150"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[modelname]" id="name" size="30" /></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30" /></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[description]" id="description"  size="30"/></td>
  </tr>
    <tr>
    <th><?php echo L('updatetime_check')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][updatetime_select]" value="0" checked /> <?php echo L('check_not_default')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][updatetime_select]" value="1"  /> <?php echo L('check_default')?> <span></span></label>
    </div></td>
  </tr>
  <tr>
		<th><?php echo L('import_model');?></th>
		<td>
		<input type="text" class='input-text' onchange="FileName.value=this.value" id="myfile" name="myfile" size="26" style="width: 160px;height: 34px;padding: 6px 12px;background-color: #fff;border: 1px solid #c2cad8;border-radius: 4px;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;">&nbsp;<div class="button green uploader"><i class="fa fa-plus"></i> <?php echo L('select_file');?> <input type="file" name="model_import" id="model_import" onchange="myfile.value=this.value"></div>
		</td>
	</tr>
</table>
</fieldset>
<div class="bk15"></div>
<fieldset>
	<legend><?php echo L('template_setting')?></legend>
	<table width="100%"  class="table_form">
 <tr>
  <th width="200"><?php echo L('available_styles');?>：</th>
        <td>
		<?php echo form::select($style_list, '', 'name="default_style" id="default_style" onchange="load_file_list(this.value)"', L('please_select'))?> 
		</td>
</tr>
		<tr>
        <th width="200"><?php echo L('category_index_tpl')?>：</th>
        <td  id="category_template">
		</td>
      </tr>
	  <tr>
        <th width="200"><?php echo L('category_list_tpl')?>：</th>
        <td  id="list_template">
		</td>
      </tr>
	  <tr>
        <th width="200"><?php echo L('content_tpl')?>：</th>
        <td  id="show_template">
		</td>
      </tr>
</table>
</fieldset>
<div class="bk15"></div>
<div class="btn"><input type="submit" id="dosubmit" name="dosubmit" value="<?php echo L('submit');?>" class="button"/></div> 
</form>
</div>
<script language="JavaScript">
<!--
	function load_file_list(id) {
		$.getJSON('?m=admin&c=category&a=public_tpl_file_list&style='+id+'&catid=', function(data){$('#category_template').html(data.category_template);$('#list_template').html(data.list_template);$('#show_template').html(data.show_template);});
	}
	//-->
</script>
</body>
</html>