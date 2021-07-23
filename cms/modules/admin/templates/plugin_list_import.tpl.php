<?php 
	defined('IN_ADMIN') or exit('No permission resources.');
	include $this->admin_tpl('header', 'admin');
?>
<form name="myform" action="?m=admin&c=position&a=listorder" method="post">
<div class="pad_10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="35%"  align="left"><?php echo L('plugin_list_name','','plugin')?></th>
            <th width="10%"><?php echo L('plugin_list_version','','plugin')?></th>
            <th width="15%"><?php echo L('plugin_list_copy','','plugin')?></th>
            <th width="10%"><?php echo L('plugin_list_dir','','plugin')?></th>
            <th width="15%"></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if(is_array($pluginfo)){
	foreach($pluginfo as $info){
?>   
	<tr>
	<td width="35%"><?php echo $info['name']?></td>
	<td  width="10%" align="center"><?php echo $info['version']?></td>
	<td  width="15%" align="center"><?php echo $info['copyright']?></td>
	<td width="10%" align="center"><?php echo $info['dir']?>/</td>
	<td width="15%" align="center"><a href="?m=admin&c=plugin&a=import&dir=<?php echo $info['dir']?>&menuid=<?php echo $this->input->get('menuid')?>"><?php echo L('plugin_install','','plugin')?></a></td>
	</tr>
<?php 
	}
}
?>
    </tbody>
    </table>
  
    <div class="btn"></div>  </div>

 <div id="pages"> <?php echo $pages?></div>
</div>
</div>
</form>
</body>
<a href="javascript:edit(<?php echo $v['siteid']?>, '<?php echo $v['name']?>')">
</html>
<script type="text/javascript">
<!--
function add(id, name) {
	artdialog('add','?m=pay&c=payment&a=add&code='+id,'<?php echo L('edit')?>--'+name,700,500);
}	
function edit(id, name) {
	artdialog('edit','?m=pay&c=payment&a=edit&id='+id,'<?php echo L('edit')?>--'+name,700,500);
}
//-->
</script>