<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');
?>
<link href="<?php echo CSS_PATH?>jquery.treeTable.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo JS_PATH?>jquery.treetable.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $("#dnd-example").treeTable({
        indent: 20
        });
  });
  function checknode(obj)
  {
      var chk = $("input[type='checkbox']");
      var count = chk.length;
      var num = chk.index(obj);
      var level_top = level_bottom =  chk.eq(num).attr('level')
      for (var i=num; i>=0; i--)
      {
              var le = chk.eq(i).attr('level');
              if(eval(le) < eval(level_top)) 
              {
                  chk.eq(i).prop("checked", true);
                  var level_top = level_top-1;
              }
      }
      for (var j=num+1; j<count; j++)
      {
              var le = chk.eq(j).attr('level');
              if(chk.eq(num).is(":checked")) {
                  if(eval(le) > eval(level_bottom)) chk.eq(j).prop("checked", true);
                  else if(eval(le) == eval(level_bottom)) break;
              }
              else {
                  if(eval(le) > eval(level_bottom)) chk.eq(j).prop("checked", false);
                  else if(eval(le) == eval(level_bottom)) break;
              }
      }
  }
</script>
<?php if($siteid) {?>
<form class="form-horizontal" name="myform" id="myform" action="?m=admin&c=role&a=role_priv" method="post">
<input type="hidden" name="roleid" value="<?php echo $roleid?>"></input>
<input type="hidden" name="siteid" value="<?php echo $siteid?>"></input>
<div class="myfbody">
<div class="table-scrollable">
<table class="table table-nomargin table-bordered table-striped table-bordered table-advance" id="dnd-example">
<thead>
<tr class="heading">
<th class="myselect table-checkable">
<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label></th>
</tr>
</thead>
<tbody>
<?php echo $categorys;?>
</tbody>
    </table>
</div>
<div class="portlet-body form myfooter">
<div class="form-actions text-center"><input type="submit"  class="button" name="dosubmit" id="dosubmit" value="<?php echo L('submit');?>" /></div>
</div>
</form>
<?php } else {?>
<style type="text/css">
.guery{background: url(<?php echo IMG_PATH?>msg_img/msg_bg.png) no-repeat 0px -560px;padding:10px 12px 10px 45px; font-size:14px; height:100px; line-height:96px}
.guery{background-position: left -460px;}
</style>
<center>
    <div class="guery" style="display:inline-block;display:-moz-inline-stack;zoom:1;*display:inline;">
    <?php echo L('select_site');?>
    </div>
</center>
<?php }?>

</body>
</html>
