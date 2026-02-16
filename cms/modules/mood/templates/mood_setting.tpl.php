<?php defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10 myfbody">
<form name="myform" id="myform" action="?m=mood&c=mood_admin&a=setting" method="post">
<div class="table-list">
<table width="100%" cellspacing="0">
    <thead>
        <tr>
            <th align="left" width="80"><?php echo L('on_hand')?></th>
            <th align="left" width="220"><?php echo L('name')?></th>
            <th align="left"><?php echo L('pic')?></th>
        </tr>
    </thead>
<tbody>
<?php
    for($i=1; $i<=10; $i++) {
?>
    <tr>
        <td align="left"><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="checkbox" value="1" name="use[<?php echo $i?>]" <?php if($mood_program[$i]['use']==1){echo 'checked';}?>> <span></span></label>
        </div></td>
        <td align="left"><input type="text" name="name[<?php echo $i?>]" value="<?php echo $mood_program[$i]['name']?>"></td>
        <td align="left"><input type="text" name="pic[<?php echo $i?>]" value="<?php echo $mood_program[$i]['pic']?>"><?php if ($mood_program[$i]['pic']) {echo '<img src="'.IMG_PATH.$mood_program[$i]['pic'].'">';}?></td>
    </tr>
<?php
    }

?>
</tbody>
</table>
</div>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=mood&c=mood_admin&a=setting', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</form>
</div>
</body>
</html>