<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_header = $show_validator = true;
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
<!--
    $(document).ready(function(){
        $.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){Dialog.alert(msg,function(){$(obj).focus();})}});
        <?php if (is_array($html) && $html['validator']){ echo $html['validator']; unset($html['validator']); }?>
    })
//-->
</script>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
<li<?php if ($this->input->get('order')==1 || !$this->input->get('order')) {?> class="on"<?php }?>><a href="?m=content&c=push&a=init&classname=position_api&action=position_list&order=1&modelid=<?php echo $this->input->get('modelid')?>&catid=<?php echo $this->input->get('catid')?>&id=<?php echo $this->input->get('id')?>"><?php echo L('push_to_position');?></a></li>
<li<?php if ($this->input->get('order')==2) {?> class="on"<?php }?>><a href="?m=content&c=push&a=init&module=special&action=_push_special&order=2&modelid=<?php echo $this->input->get('modelid')?>&catid=<?php echo $this->input->get('catid')?>&id=<?php echo $this->input->get('id')?>"><?php echo L('push_to_special');?></a></li>
<li<?php if ($this->input->get('order')==3) {?> class="on"<?php }?>><a href="?m=content&c=push&a=init&module=content&classname=push_api&action=category_list&order=3&tpl=push_to_category&modelid=<?php echo $this->input->get('modelid')?>&catid=<?php echo $this->input->get('catid')?>&id=<?php echo $this->input->get('id')?>"><?php echo L('push_to_category');?></a></li>
</ul>
<div class='content' style="height:auto;">
<form action="?m=content&c=push&a=init" method="post" name="myform" id="myform">
<input name="dosubmit" type="hidden" value="1">
<input name="module" type="hidden" value="<?php echo $this->input->get('module')?>">
<input name="action" type="hidden" value="<?php echo $this->input->get('action')?>">
<input type="hidden" name="modelid" value="<?php echo $this->input->get('modelid')?>">
<input type="hidden" name="catid" value="<?php echo $this->input->get('catid')?>">
<input type='hidden' name="id" value='<?php echo $this->input->get('id')?>'>
<table width="100%"  class="table_form">
  
  <?php 
  if (isset($html) && is_array($html)) {
  foreach ($html as $k => $v) { ?>
        <tr>
    <th width="80"><?php echo $v['name']?>：</th>
    <td class="y-bg"><?php echo creat_form($k, $v)?></td>
  </tr>
  <?php if ($v['ajax']['name']) {?>
        <tr>
            <th width="80"><?php echo $v['ajax']['name']?>：</th>
            <td class="y-bg" id="<?php echo $k?>_td"><input type="hidden" name="<?php echo $v['ajax']['id']?>" id="<?php echo $v['ajax']['id']?>"></td>
       </tr>
  <?php } ?>

  <?php } } else { echo $html; }?>
  </table>
<div class="bk15"></div>

<input type="hidden" name="return" value="<?php echo $return?>" />
</form>
</div>
</div>
</div>
</body>
</html>