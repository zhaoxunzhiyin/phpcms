<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div class="pad-10">
<div class="col-tab">
<ul class="tabBut cu-li">
	 		<?php foreach($plugin_menus as $_num => $menu) {?>
            <li <?php if($menu['url']==$this->input->get('a')) {?>class="on"<?php }?> <?php if($menu['extend']) {?>onclick="loadfile('<?php echo$menu['url'] ?>')"<?php }?> ><a href="?m=sqltoolplus&c=index&a=<?php echo $menu['url']?>&pc_hash=<?php echo $_SESSION['pc_hash']?>"><?php echo $menu['name']?></a></li>
            <?php }?>
</ul>
<div id="tab-content">
<div class="contentList pad-10">
<form action="?m=sqltoolplus&c=index&a=sqlreplace" method="post" id="myform">
  <table width="100%" class="table_form">
    <tr>
      <th width="120"><?php echo L('select_pdo')?></th>
      <td class="y-bg"><table class="table_form">
          <tbody>
            <tr>
              <td class="y-bg" style="border-bottom:0px !important;"><?php echo form::select($pdos,$pdo_name,'name="pdo_select" onchange="select_db_table(this.value)"',L('select_pdo'))?></td>
              <td class="y-bg" style="border-bottom:0px !important;"><?php echo L('db_table')?></td>
              <td class="y-bg" style="border-bottom:0px !important;"><select id="db_table" onchange="get_fields(this.value)" name="db_table"></select></td>
              <td class="y-bg" style="border-bottom:0px !important;"><?php echo L('db_field')?></td>
              <td class="y-bg" style="border-bottom:0px !important;"><select id="db_field" name="db_field"></select></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
    <tr>
      <th width="120"><?php echo L('alternative')?></th>
      <td class="y-bg"><?php echo form::radio(array('0'=>L('regularreplaced'),'1'=>L('replacematch'),'2'=>L('replaceordinary')),2,'name="replace_type" onclick="clk_replace_type(this.value)"',200)?></td>
    </tr>
    <tr id="db_pr_tr">
      <th width="120"><?php echo L('db_pr_field')?></th>
      <td class="y-bg"><select id="db_pr_field" name="db_pr_field">
        </select></td>
    </tr>
    <tr>
      <th width="120"><?php echo L('search_rule')?></th>
      <td class="y-bg"><textarea style="width:500px;" name="search_rule" rows="10" cols="85"></textarea></td>
    </tr>
    <tr>
      <th width="120"><?php echo L('replace_data')?></th>
      <td class="y-bg"><textarea style="width:500px;" name="replace_data" rows="10" cols="85"></textarea></td>
    </tr>
    <tr>
      <th width="120"><?php echo L('sql_where')?></th>
      <td class="y-bg"><textarea style="width:500px;" name="sql_where" rows="10" cols="85"></textarea></td>
    </tr>
  </table>
  <div class="bk15"></div>
  <input type="hidden" value="<?php echo $_SESSION['pc_hash']?>" name="pc_hash">
  <input name="pluginsubmit" type="submit" value="<?php echo L('submit')?>" class="button">
</form>
</div>
</div>
</div>
</div>
</body>
<script type="text/javascript">
<!--
var db_source;
var db_tables;
function select_db_table(obj)
{
	if(obj=='default'){obj='MM_LOCALHOST';}
	db_source = obj;
	$("#db_table").html('<option value=""><?php echo L('select')?></option>');
	if(obj!='')
	{ 
		$.getJSON('?m=sqltoolplus&c=index&a=ajax_get_dbtable&name='+obj+'&pc_hash=<?php echo $_SESSION['pc_hash']?>&callback=?',function(data){
			if(data)
			{
				$.each(data,function(i,n){
                    var selected = '';
					$("#db_table").append('<option value="'+n.tablename+'" '+selected+'>'+(n.nickname ? n.nickname : n.tablename)+'</option>');
				});
			}
			else
			{
				Dialog.alert('<?php echo L('nodatafound')?>');
			}
		})
	}
}
function get_fields(val)
{
	db_tables = val;
	$('#db_field').html('<option value=""><?php echo L('select')?></option>');
	if(val!='')
	{
		$.getJSON('?m=sqltoolplus&c=index&a=ajax_get_fields&name='+db_source+'&tables='+val+'&pc_hash=<?php echo $_SESSION['pc_hash']?>&callback=?',function(data){
			if(data)
			{
				$.each(data,function(i,n){
					var str = '<option value="'+n.field+'">'+(n.nickname?n.nickname:n.field)+'</option>';
					$('#db_field').append(str);
				});
				if($('#db_pr_field').html()!=''){
					$('#db_pr_field').html($('#db_field').html());
				}
			}
			else
			{
				Dialog.alert('<?php echo L('nodatafound')?>');
			}
		});
	}
}
function clk_replace_type(v){
	if(v==2){
		$('#db_pr_tr').hide();
		$('#db_pr_field').html('');
	}else{
		$('#db_pr_tr').show();
		$('#db_pr_field').html($('#db_field').html());
	}
}
clk_replace_type(2);
//-->
</script>
</html>