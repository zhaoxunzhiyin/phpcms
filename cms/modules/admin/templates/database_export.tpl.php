<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header');?>
<script language="javascript" type="text/javascript" src="<?php echo JS_PATH?>jquery-3.5.1.min.js"></script>
<div class="pad_10">
<div class="table-list">
<form method="post" name="myform" id="myform" action="?m=admin&c=database&a=export">
<input type="hidden" name="tabletype" value="db" id="cmstables">
<table width="100%" cellspacing="0">
<thead>
  	<tr>
    	<th class="tablerowhighlight" colspan=4><?php echo L('backup_setting')?></th>
  	</tr>
</thead>
  	<tr>
	    <td class="align_r"><?php echo L('sizelimit')?></td>
	    <td colspan=3><input type=text name="sizelimit" value="2048" size=5> K</td>
  	</tr>
   	<tr>
	    <td class="align_r"><?php echo L('sqlcompat')?></td>
	    <td colspan=3><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="sqlcompat" value="" checked> <?php echo L('default')?> <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="sqlcompat" value="MYSQL40"> MySQL 3.23/4.0.x <span></span></label>
          <label class="mt-radio mt-radio-outline"><input type="radio" name="sqlcompat" value="MYSQL41"> MySQL 4.1.x/5.x <span></span></label>
        </div></td>
  	</tr>
   	<tr>
	    <td class="align_r"><?php echo L('sqlcharset')?></td>
	    <td colspan=3><div class="mt-radio-inline">
          <label class="mt-radio mt-radio-outline"><input type="radio" name="sqlcharset" value="" checked> <?php echo L('default')?> <span></span></label>
        </div></td>
  	</tr>
  	<tr>
	    <td class="align_r"><?php echo L('select_pdo')?></td>
	    <td colspan=3><?php echo form::select($pdos,$pdo_name,'name="pdo_select" onchange="show_tbl(this)"',L('select_pdo'))?></td>
  	</tr>
  	<tr>
	    <td></td>
	    <td colspan=3><input type="submit" name="dosubmit" value=" <?php echo L('backup_starting')?> " class="button"></td>
  	</tr>
</table>
    <table width="100%" cellspacing="0" class="table-checkable">
 <?php 
if(is_array($infos)){
?>   
	<thead><tr><th align="center" colspan="8"><strong><?php echo $pdo_name?> <?php echo L('pdo_name')?></strong></th></tr></thead>
    <thead>
       <tr>
           <th class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="group-checkable" data-set=".checkboxes" />
                        <span></span>
                    </label></th>
           <th ><?php echo L('database_tblname')?></th>
           <th width="10%"><?php echo L('database_type')?></th>
           <th width="10%"><?php echo L('database_char')?></th>
           <th width="15%"><?php echo L('database_records')?></th>
           <th width="15%"><?php echo L('database_size')?></th>
           <th width="15%"><?php echo L('database_block')?></th>
           <th width="15%"><?php echo L('database_op')?></th>
       </tr>
    </thead>
    <tbody>
	<?php foreach($infos['cmstables'] as $v){?>
	<tr>
	<td align="center" class="myselect">
                    <label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline">
                        <input type="checkbox" class="checkboxes" name="tables[]" value="<?php echo $v['name']?>" />
                        <span></span>
                    </label></td>
	<td align="center"><?php echo $v['name']?></td>
	<td align="center"><?php echo $v['engine']?></td>
	<td align="center"><?php echo $v['collation']?></td>
	<td align="center"><?php echo $v['rows']?></td>
	<td align="center"><?php echo format_file_size($v['size'])?></td>
	<td align="center"><?php echo $v['data_free']?></td>
	<td align="center"><a href="?m=admin&c=database&a=public_repair&operation=optimize&pdo_name=<?php echo $pdo_name?>&tables=<?php echo $v['name']?>"><?php echo L('database_optimize')?></a> | <a href="?m=admin&c=database&a=public_repair&operation=repair&pdo_name=<?php echo $pdo_name?>&tables=<?php echo $v['name']?>"><?php echo L('database_repair')?></a> | <a href="?m=admin&c=database&a=public_repair&operation=flush&pdo_name=<?php echo $pdo_name?>&tables=<?php echo $v['name']?>"><?php echo L('database_flush')?></a> | <a href="?m=admin&c=database&a=public_repair&operation=jc&pdo_name=<?php echo $pdo_name?>&tables=<?php echo $v['name']?>"><?php echo L('database_check')?></a> | <a href="javascript:void(0);" onclick="showcreat('<?php echo $v['name']?>','<?php echo $pdo_name?>')"><?php echo L('database_showcreat')?></a></td>
	</tr>
	<?php } ?>
	</tbody>
<?php 
}
?>
</table>
 <?php 
if(is_array($infos)){
?>
<div class="fc-list-select table-checkable">
<label class="mt-table mt-checkbox mt-checkbox-single mt-checkbox-outline"><input type="checkbox" class="group-checkable" data-set=".checkboxes" /><span></span></label>
<input type="button" class="button" name="dosubmit" onclick="dr_bfb_submit('<?php echo L('batch_optimize')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=y&pdo_name=<?php echo $pdo_name?>')" value="<?php echo L('batch_optimize')?>"/>
<input type="button" class="button" name="dosubmit" onclick="dr_bfb_submit('<?php echo L('batch_repair')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=x&pdo_name=<?php echo $pdo_name?>')" value="<?php echo L('batch_repair')?>"/>
<input type="button" class="button" name="dosubmit" onclick="dr_bfb_submit('<?php echo L('batch_flush')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=s&pdo_name=<?php echo $pdo_name?>')" value="<?php echo L('batch_flush')?>"/>
<input type="button" class="button" name="dosubmit" onclick="dr_bfb_submit('<?php echo L('batch_check')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=jc&pdo_name=<?php echo $pdo_name?>')" value="<?php echo L('batch_check')?>"/>
<input type="button" class="button" name="dosubmit" onclick="dr_bfb_submit('<?php echo L('batch_utf8mb4')?>', 'myform', '<?php echo SELF;?>?m=admin&c=database&a=public_add&operation=ut&pdo_name=<?php echo $pdo_name?>')" value="<?php echo L('batch_utf8mb4')?>"/>
</div>
<?php 
}
?>
</form>
</div>
</div>
</form>
</body>
<script type="text/javascript">
<!--
function show_tbl(obj) {
	var pdoname = $(obj).val();
	location.href='?m=admin&c=database&a=export&pdoname='+pdoname+'&pc_hash=<?php echo $_SESSION['pc_hash']?>';
}
function showcreat(tblname, pdo_name) {
	omnipotent('show','?m=admin&c=database&a=public_repair&operation=showcreat&pdo_name='+pdo_name+'&tables='+tblname,tblname,1,'60%','70%')
}
//-->
</script>
</html>
