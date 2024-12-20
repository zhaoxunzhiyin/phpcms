<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#name").formValidator({onshow:"<?php echo L('type_name_tips')?>",onfocus:"<?php echo L("input").L('type_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('type_name')?>"});
    })
//-->
</script>
<form action="?m=content&c=type_manage&a=edit" method="post" id="myform">
<input type="hidden" name="typeid" value="<?php echo $typeid;?>">
<input type="hidden" name="catids_string" value="<?php echo $catids_string;?>">
<div style="padding:6px 3px">
    <div class="col-2 col-left mr6" style="width:440px">
      <h6><img src="<?php echo IMG_PATH;?>icon/sitemap-application-blue.png" width="16" height="16" /> <?php echo L('edit_type');?></h6>
<table width="100%"  class="table_form">
  <tr>
    <th width="80"><?php echo L('type_name')?>：</th>
    <td class="y-bg"><input type="text" name="info[name]" id="name" class="inputtext" style="width:300px;" value="<?php echo $name;?>"></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><textarea name="info[description]" maxlength="255" style="width:300px;height:60px;"><?php echo $description;?></textarea></td>
  </tr>
</table>
    </div>
    <div class="col-2 col-auto">
        <div class="table-list">
        <table width="100%">
              <thead>
                <tr class="heading">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" value="" id="check_box" onclick="selectall('ids[]');" onmouseover="layer.tips('<?php echo L('selected_all');?>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();" />
                        <span></span>
                    </label></th><th align="left"><?php echo L('catname');?></th>
              </tr>
                </thead>
                 <tbody>
        <?php echo $categorys;?>
        </tbody>
        </table>
        </div>
    </div>
</div>
</form>
</body>
</html>