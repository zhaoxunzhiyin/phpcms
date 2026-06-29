<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.'); 
include $this->admin_tpl('header', 'admin');
?>

<div class="pad-10">
<form action="?m=template&c=file&a=edit_pc_tag&style=<?php echo $this->style?>&dir=<?php echo $dir?>&file=<?php echo urlencode($file)?>&op=<?php echo $op?>&tag_md5=<?php echo $this->input->get('tag_md5')?>" method="post" id="myform">
    <table width="100%"  class="table_form">
      <tr>
    <th width="80"><?php echo L("toolbox_type")?>：</th>
    <td class="y-bg"><?php echo $op?></td>
  </tr>
    <tr>
    <th width="80"><?php echo L("data_address")?>：</th>
    <td class="y-bg"><input type="text" name="url" id="url" size="30" value="<?php echo $this->input->get('url')?>" /></td>
  </tr>
     <tr>
    <th width="80"><?php echo L("check")?>：</th>
    <td class="y-bg"><input type="text" name="return" id="return" size="30" value="<?php echo $this->input->get('return')?>" /> </td>
  </tr>
   <tr>
    <th width="80"><?php echo L("buffer_time")?>：</th>
    <td class="y-bg"><input type="text" name="cache" id="cache" size="30" value="<?php echo $this->input->get('cache')?>" /> <?php echo L("unit_second")?></td>
  </tr>
</table>
</form>
</div>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#url").formValidator({onshow:"<?php echo L("input").L("data_address")?>",onfocus:"<?php echo L("input").L("data_address")?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L("data_address")?>"}).regexValidator({regexp:"^http:\/\/(.*)",param:'i',onerror:"<?php echo L('data_address_reg_sg')?>"});
        $("#cache").formValidator({onshow:"<?php echo L("input").L('buffer_time')?>",onfocus:"<?php echo L("input").L('buffer_time')?>"}).regexValidator({regexp:"num1",datatype:'enum',param:'i',onerror:"<?php echo L('cache_time_can_only_be_positive')?>"});
    })
//-->
</script>
</body>
</html>