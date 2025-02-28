<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
    $(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        $("#workname").formValidator({onshow:"<?php echo L("input").L('workflow_name')?>",onfocus:"<?php echo L("input").L('workflow_name')?>",oncorrect:"<?php echo L('input_right');?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('workflow_name')?>"});
    })
//-->
</script>
<div class="pad-lr-10">
<form action="?m=content&c=workflow&a=add" method="post" id="myform">
<div class="myfbody">
    <table width="100%"  class="table_form">
  <tr>
    <th width="200"><?php echo L('workflow_name')?>：</th>
    <td class="y-bg"><input type="text" class="input-text" name="info[workname]" id="workname" size="30" /></td>
  </tr>
    <tr>
    <th><?php echo L('description')?>：</th>
    <td class="y-bg"><textarea name="info[description]" maxlength="255" style="width:300px;height:60px;"></textarea></td>
  </tr>
    <tr>
    <th><?php echo L('steps')?>：</th>
    <td class="y-bg">
    <select name="info[steps]" onchange="select_steps(this.value)">
    <option value='1' selected><?php echo L('steps_1');?></option>
    <option value='2'><?php echo L('steps_2');?></option>
    <option value='3'><?php echo L('steps_3');?></option>
    <option value='4'><?php echo L('steps_4');?></option>
    </select></td>
  </tr>
   <tr id="step1">
    <th><?php echo L('steps_1');?> <?php echo L('admin_users')?>：</th>
    <td class="y-bg">
    <?php echo form::checkbox($admin_data,'','name="checkadmin1[]"','',120);?>
    </td>
  </tr>
   <tr id="step2" style="display:none">
    <th><?php echo L('steps_2');?> <?php echo L('admin_users')?>：</th>
    <td class="y-bg">
        <?php echo form::checkbox($admin_data,'','name="checkadmin2[]"','',120);?>
    </td>
  </tr>
   <tr id="step3" style="display:none">
    <th><?php echo L('steps_3');?> <?php echo L('admin_users')?>：</th>
    <td class="y-bg">
        <?php echo form::checkbox($admin_data,'','name="checkadmin3[]"','',120);?>
    </td>
  </tr>
   <tr id="step4" style="display:none">
    <th><?php echo L('steps_4');?><?php echo L('admin_users')?>：</th>
    <td class="y-bg">
        <?php echo form::checkbox($admin_data,'','name="checkadmin4[]"','',120);?>
    </td>
  </tr>
  <tr>
    <th><B><?php echo L('nocheck_users')?></B>：</th>
    <td class="y-bg">
        <?php echo form::checkbox($admin_data,'','name="nocheck_users[]"','',120);?>
    </td>
  </tr>
  <tr>
    <th><?php echo L('checkstatus_flag')?>：</th>
    <td class="y-bg">
      <div class="mt-radio-inline">
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[flag]" value="1" > 是 <span></span></label>
        <label class="mt-radio mt-radio-outline"><input type="radio" name="info[flag]" value="0" checked> 否 <span></span></label>
      </div>
    </td>
  </tr>
</table>
</div>
<div class="portlet-body form myfooter">
<div class="form-actions text-center"><input type="submit" id="dosubmit" class="button" name="dosubmit" value="<?php echo L('submit')?>"/></div>
</div>
</form>
</div>
</body>
</html>
<SCRIPT LANGUAGE="JavaScript">
<!--
function select_steps(stepsid) {
    for(i=4;i>1;i--) {
        if(stepsid>=i) {
            $('#step'+i).css('display','');
        } else {
            $('#step'+i).css('display','none');
        }
    }
}
//-->
</SCRIPT>