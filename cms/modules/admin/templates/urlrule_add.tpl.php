<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#file").formValidator({onshow:"<?php echo L('input').L('urlrule_file')?>",onfocus:"<?php echo L('input').L('urlrule_file')?>"}).regexValidator({regexp:"^([a-zA-Z0-9]|[_]){0,20}$",onerror:"<?php echo L('enter_the_correct_catname');?>"}).inputValidator({min:1,onerror:"<?php echo L('input').L('urlrule_file')?>"});
        $("#example").formValidator({onshow:"<?php echo L('input').L('urlrule_example')?>",onfocus:"<?php echo L('input').L('urlrule_example')?>"}).inputValidator({min:1,onerror:"<?php echo L('input').L('urlrule_example')?>"});
        $("#urlrule").formValidator({onshow:"<?php echo L('input').L('urlrule_url')?>",onfocus:"<?php echo L('input').L('urlrule_url')?>"}).inputValidator({min:1,onerror:"<?php echo L('input').L('urlrule_url')?>"});

    })
//-->
</script>
<style type="text/css">
.input-botton {
    border:none;
    border-bottom:1px dotted #E1A035;
    background:none;
}
</style>
<div class="pad_10">
<table width="100%" cellpadding="2" cellspacing="1" class="table_form">
<form action="?m=admin&c=urlrule&a=add" method="post" name="myform" id="myform">
    <tr> 
      <th width="20%"><?php echo L('urlrule_file')?> :</th>
      <td><input type="text" name="info[file]" id="file" size="20"></td>
    </tr>
    <tr> 
      <th width="20%"><?php echo L('urlrule_module')?> :</th>
      <td><?php echo form::select($modules,'content',"name='info[module]' id='module'");?></td>
    </tr>
    <tr> 
      <th width="20%"><?php echo L('urlrule_ishtml')?> :</th>
      <td>
        <div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" value="1" name="info[ishtml]" /> <?php echo L('yes');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" value="0" name="info[ishtml]" checked="checked" /><?php echo L('no');?> <span></span></label>
        </div>
    </td>
    </tr>
    <tr> 
      <th width="20%"><?php echo L('urlrule_example')?> :</th>
       <td><input type="text" name="info[example]" id="example" size="70"></td>
    </tr>
    <tr> 
      <th width="20%"><?php echo L('urlrule_url')?> :</th>
       <td><input type="text" name="info[urlrule]" id="urlrule" size="70">
</td>
    </tr>
    <tr> 
      <th width="20%"><?php echo L('urlrule_func')?> :</th>
       <td><?php echo L('complete_parent_path');?>：<label><input type="text" name="f1" value="{$parentdir}" size="10" class="input-botton"></label>，<?php echo L('complete_part_path');?>：<label><input type="text" name="f1" value="{$categorydir}" size="10" class="input-botton"></label>
       <div class="bk6"></div>
       <?php echo L('category_path');?>：<label><input type="text" name="f1" value="{$catdir}" size="10" class="input-botton"></label>，<?php echo L('catid');?>：<label><input type="text" name="f1" value="{$catid}" size="5" class="input-botton"></label>
       <div class="bk6"></div>
       <?php echo L('year');?>：<label><input type="text" name="f1" value="{$year}" size="7" class="input-botton"></label> <?php echo L('month');?>：<label><input type="text" name="f1" value="{$month}" size="9" class="input-botton"></label>，<?php echo L('day');?>：<label><input type="text" name="f1" value="{$day}" size="7" class="input-botton"></label>
       <div class="bk6"></div>
       <?php echo L('ID');?>：<label><input type="text" name="f1" value="{$id}" size="4" class="input-botton"></label>，<?php echo L('prefix');?>：<label><input type="text" name="f1" value="{$prefix}" size="7" class="input-botton"></label>，<?php echo L('paging');?>：<label><input type="text" name="f1" value="{$page}" size="7" class="input-botton"></label>
    </td>
    </tr>
    </form>
</table> 

</div>
</body>
</html>