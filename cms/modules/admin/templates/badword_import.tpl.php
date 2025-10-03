<?php
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
include $this->admin_tpl('header');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="right-card-box myfbody">
<form action="?m=admin&c=badword&a=import" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
     <tr> 
      <th width="100"> <?php echo L('badword_name')?> </th>
      <td><textarea name="info" cols="50" rows="6" require="true" datatype="limit" ></textarea> </td>
    </tr>
   
    <tr> 
      <th> <?php echo L('badword_name')?> <?php echo L('badword_require')?>: </th>
      <td>
<?php echo L('badword_import_infos')?>
 </td>
    </tr> 
</table>
<div class="portlet-body form myfooter">
    <div class="form-actions text-center">
        <button type="button" onclick="dr_ajax_submit('?m=admin&c=badword&a=import', 'myform', '2000')" class="btn green"> <i class="fa fa-save"></i> <?php echo L('submit')?></button>
    </div>
</div>
</form>
</div>
</div>
</div>
</div>
</body>
</html>