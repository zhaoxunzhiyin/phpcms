<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#name").formValidator({onshow:"<?php echo L("input").L('model_name')?>",onfocus:"<?php echo L("input").L('model_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_name')?>"});
        $("#tablename").formValidator({onshow:"<?php echo L("input").L('model_tablename')?>",onfocus:"<?php echo L("input").L('model_tablename')?>"}).regexValidator({regexp:"^([a-zA-Z0-9]|[_]){0,20}$",onerror:"<?php echo L("model_tablename_wrong");?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('model_name')?>"}).ajaxValidator({type : "get",url : "",data :"m=content&c=sitemodel&a=public_check_tablename",datatype : "html",async:'false',success : function(data){    if( data == "1" ){return true;}else{return false;}},buttons: $("#dosubmit"),onerror : "<?php echo L('model_tablename').L('exists')?>",onwait : "<?php echo L('connecting')?>"});        
    })
//-->
</script>
<div class="pad-lr-10">
<form action="?m=content&c=sitemodel&a=import" method="post" id="myform" enctype="multipart/form-data">
<div class="myfbody">
<fieldset>
    <legend><?php echo L('basic_configuration')?></legend>
    <table width="100%"  class="table_form">
  <tr>
    <th width="150"><?php echo L('model_name')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[modelname]" id="name" size="30" onblur="topinyin('<?php echo WEB_PATH;?>api.php?op=pinyin','name','tablename',12);" /></label></td>
  </tr>
  <tr>
    <th><?php echo L('model_tablename')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[tablename]" id="tablename" size="30" /></label></td>
  </tr>
  <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[description]" id="description" size="30"/></label></td>
  </tr>
  <tr>
    <th><?php echo L('updatetime_check')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][updatetime_select]" value="0" checked /> <?php echo L('check_not_default')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][updatetime_select]" value="1"  /> <?php echo L('check_default')?> <span></span></label>
    </div></td>
  </tr>
  <tr>
    <th><?php echo L('自动填充内容描述')?>：</th>
    <td class="y-bg"><div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][desc_auto]" value="0" checked /> <?php echo L('自动填充')?> <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[setting][desc_auto]" value="1"  /> <?php echo L('手动填充')?> <span></span></label>
    </div>
    <div class="onShow">当描述为空时，系统提取内容中的文字来填充描述字段</div></td>
  </tr>
  <tr>
    <th><?php echo L('提取内容描述字数')?>：</th>
    <td class="y-bg"><label><input type="text" class="input-text" name="info[setting][desc_limit]" id="desc_limit" size="30" value="200" /></label><div class="onShow">在内容中提取描述信息的最大字数限制</div></td>
  </tr>
  <tr>
        <th><?php echo L('import_model');?></th>
        <td>
        <label><input type="text" class='input-text' id="myfile" name="myfile" size="26" readonly="readonly"></label> <label><span class="btn green btn-sm fileinput-button"><i class="fa fa-cloud-upload"></i> <span> <?php echo L('select_file');?> </span> <input type="file" name="model_import" id="model_import" onchange="myfile.value=this.value" title=""></span></label>
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
</div>
<div class="bk15"></div>
<div class="portlet-body form myfooter">
<div class="form-actions text-center"><label><button type="submit" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button></label></div>
</div>
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