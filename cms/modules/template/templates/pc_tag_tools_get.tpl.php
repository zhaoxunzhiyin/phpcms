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
    <th width="80">SQL：</th>
    <td class="y-bg"><textarea name="sql" id="sql" style="width:386px;height:178px;"><?php echo $this->input->get('sql')?></textarea></td>
  </tr>
   <tr>
    <th width="80"><?php echo L('dbsource')?>：</th>
    <td class="y-bg"><?php echo form::select($dbsource_list, $this->input->get('dbsource'), 'name="dbsource" id="dbsource"')?></td>
  </tr>
     <tr>
    <th width="80"><?php echo L("check")?>：</th>
    <td class="y-bg"><input type="text" name="return" id="return" size="30" value="<?php echo $this->input->get('return')?>" /> </td>
  </tr>
   <tr>
    <th width="80"><?php echo L("buffer_time")?>：</th>
    <td class="y-bg"><input type="text" name="cache" id="cache" size="30" value="<?php echo $this->input->get('cache')?>" /> </td>
  </tr>
</table>
</form>
</div>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#sql").formValidator({onshow:"<?php echo L("input").'SQL'?>",onfocus:"<?php echo L("input").'SQL'?>"}).inputValidator({min:1,onerror:"<?php echo L("input").'SQL'?>"});
        $("#dbsource").formValidator({onshow:"<?php echo L("input").L("dbsource")?>",onfocus:"<?php echo L("input").L("dbsource")?>"});
        $("#cache").formValidator({onshow:"<?php echo L("enter_the_cache_input_will_not_be_cached")?>",onfocus:"<?php echo L("enter_the_cache_input_will_not_be_cached")?>",empty:true}).regexValidator({regexp:"num1",datatype:'enum',param:'i',onerror:"<?php echo L("cache_time_can_only_be_positive")?>"});
        $("#num").formValidator({onshow:"<?php echo L('input').L("num")?>",onfocus:"<?php echo L('input').L("num")?>",empty:true}).regexValidator({regexp:"num1",datatype:'enum',param:'i',onerror:"<?php echo L('that_shows_only_positive_numbers')?>"});
    })
//-->
</script>
</body>
</html>