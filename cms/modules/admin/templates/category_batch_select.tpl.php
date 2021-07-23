<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="subnav">
  <div class="content-menu ib-a blue line-x">
  <a href='?m=admin&c=category&a=init&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&module=admin' ><em><?php echo L('category_manage')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo $menuid;?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=0', '<?php echo L('add_category')?>')" ><em><?php echo L('add_category')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=1', '<?php echo L('add_page')?>')" ><em><?php echo L('add_page')?></em></a><span>|</span><a href="javascript:addedit('?m=admin&c=category&a=add&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&s=2', '<?php echo L('add_cat_link')?>')" ><em><?php echo L('add_cat_link')?></em></a><span>|</span><a href='?m=admin&c=category&a=public_cache&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&module=admin' ><em><?php echo L('category_cache')?></em></a><span>|</span><a href='?m=admin&c=category&a=count_items&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&' ><em><?php echo L('count_items')?></em></a><span>|</span><a href='javascript:;' class="on"><em><?php echo L('category_batch_edit')?></em></a><span>|</span><a href='?m=content&c=sitemodel_field&a=init&menuid=<?php echo$this->input->get('menuid');?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>&&modelid=-1' ><em><?php echo L('category_field_manage')?></em></a>
  </div>
</div>
<div class="pad-10">
<div class="bk10"></div>
<div class="table-list">
<table width="100%" cellspacing="0">
<form action="?m=admin&c=category&a=batch_edit" method="post" name="myform">
<tbody height="200" class="nHover td-line">
	<tr> 
      <td width="200" align="left">
	<?php echo L('category_batch_edit');?> <font color="red"><?php echo L('category_manage');?></font>
	<div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="type" value="0" <?php if($type==0) echo 'checked';?>> <?php echo L('category_batch_edit');?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="type" value="1" <?php if($type==1) echo 'checked';?>> <?php echo L('category_type_page');?> <span></span></label>
        </div>
	</td>
      <td width="420" align="center">
<select name='catids[]' id='catids'  multiple="multiple"  style="height:300px;width:400px" title="<?php echo L('push_ctrl_to_select','','content');?>">
<?php echo $string;?>
</select></td>
      <td>
	  <input type="hidden" value="<?php echo $type;?>">
	  <input type="submit" value="<?php echo L('submit');?>" class="button">
	  </td>
    </tr>

	</tbody>
	</form>
</table>
</div>
</div>
<script language="JavaScript">
<!--
function addedit(url, name) {
	artdialog('content_id',url,name,'80%','80%');
}
window.top.$('#display_center_id').css('display','none');
//-->
</script>