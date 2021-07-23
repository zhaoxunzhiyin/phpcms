<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<style type="text/css">
pre {display: block;padding: 9.5px;margin: 0 0 10px;font-size: 13px;line-height: 1.42857;word-break: break-all;word-wrap: break-word;color: #333;background-color: #f5f5f5;border: 1px solid #ccc;border-radius: 4px;}
code, kbd, pre, samp {font-family: Menlo,Monaco,Consolas,"Courier New",monospace;}
code, kbd, pre, samp {font-size: 1em;}
pre, textarea {overflow: auto;}
.alert {border-width: 1px;}
.alert-danger {background-color: #fbe1e3;border-color: #fbe1e3;color: #e73d4a;}
.alert {padding: 15px;border: 1px solid transparent;border-radius: 4px;}
.alert, .thumbnail {margin-bottom: 20px;}
</style>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
	 		<?php foreach($plugin_menus as $_num => $menu) {?>
            <li <?php if($menu['url']==$this->input->get('a')) {?>class="on"<?php }?> <?php if($menu['extend']) {?>onclick="loadfile('<?php echo$menu['url'] ?>')"<?php }?> ><a href="?m=sqltoolplus&c=index&a=<?php echo $menu['url']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo $menu['name']?></a></li>
            <?php }?>
</ul>
<div id="tab-content">
<div class="contentList pad-10">
<!--<form action="?m=sqltoolplus&c=index&a=sqlquery" method="post" id="myform">
<table width="100%" class="table_form">
  	<tr>
	    <th class="align_r"><?php echo L('select_pdo')?></th>
	    <td colspan="3"><?php echo form::select($pdos,$pdo_name,'name="pdo_select"',L('select_pdo'))?></td>
  	</tr>
  <tr>
    <th width="120"><?php echo L('select_sql')?></th>
    <td class="y-bg">
		<textarea style="width:500px;" name="sqls" rows="10" cols="85"></textarea>
		<p style="padding-top:9px;"><?php echo L('select_sql_desc')?></p>
	</td>
  </tr> 
</table>
<div class="bk15"></div>
<input type="hidden" value="<?php echo $_SESSION['pc_hash']?>" name="pc_hash">
<input name="pluginsubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</form>-->
<form action="" method="post" id="sqlform">
<table width="100%" class="table_form">
  	<tr>
	    <th class="align_r"><?php echo L('select_pdo')?></th>
	    <td colspan="3"><?php echo form::select($pdos,$pdo_name,'name="pdo_select"',L('select_pdo'))?></td>
  	</tr>
  <tr>
    <th width="120"><?php echo L('select_sql')?></th>
    <td class="y-bg">
		<textarea style="width:500px;" id="sqls" name="sqls" rows="10" cols="85"></textarea>
		<p style="padding-top:9px;"><?php echo L('select_sql_desc')?></p>
	</td>
  </tr> 
  <tr>
    <th width="120"><?php echo L('execution')?></th>
    <td class="y-bg">
		<div id="sql_result"></div>
	</td>
  </tr> 
  <?php if ($sql_cache) {?>
  <tr>
    <th width="120"><?php echo L('recently')?></th>
    <td class="y-bg">
		<select class="form-control" onchange="$('#sqls').val(this.value)">
			<option value="">--</option>
			<?php foreach ($sql_cache as $t) {?>
			<option value="<?php echo $t?>"><?php echo str_cut($t, 50)?></option>
			<?php }?>
		</select>
	</td>
  </tr> 
  <?php }?>
</table>
<div class="bk15"></div>
<input type="hidden" value="<?php echo $_SESSION['pc_hash']?>" name="pc_hash">
<input type="hidden" value="1" name="pluginsubmit">
<button type="button" onclick="dr_submit_sql_todo('sqlform', '?m=sqltoolplus&c=index&a=sqlquery')" class="button"> <i class="fa fa-database"></i> <?php echo L('立即执行')?></button>
</form>
</div>
</div>
</div>
</div>
</body>
</html>