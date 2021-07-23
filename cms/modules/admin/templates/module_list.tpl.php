<?php 
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1; 
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10">
<div class="table-list">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="220" align="center"><?php echo L('modulename')?></th>
			<th width='220' align="center"><?php echo L('modulepath')?></th>
			<th width="14%" align="center"><?php echo L('versions')?></th>
			<th width='10%' align="center"><?php echo L('installdate')?></th>
			<th width="10%" align="center"><?php echo L('updatetime')?></th>
			<th width="12%" align="center"><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if (is_array($directory)){
	foreach ($directory as $d){
		if (array_key_exists($d, $modules)) {
?>   
	<tr>
	<td align="center" width="220"><?php echo $modules[$d]['name']?></td>
	<td width="220" align="center"><?php echo $d?></td>
	<td align="center"><?php echo $modules[$d]['version']?></td>
	<td align="center"><?php echo $modules[$d]['installdate']?></td>
	<td align="center"><?php echo $modules[$d]['updatedate']?></td>
	<td align="center">
	<?php if ($modules[$d]['iscore']) {?><span style="color: #999"><?php echo L('ban')?></span><?php } else {?><a href="javascript:void(0);" onclick="dr_install_uninstall('uninstall','<?php echo L('confirm', array('message'=>$modules[$d]['name']))?>','<?php echo L('module_unistall', '', 'admin')?>','?m=admin&c=module&a=uninstall&module=<?php echo $d?>');"><font color="red"><?php echo L('unload')?></font></a><?php }?>
	</td>
	</tr>
<?php 
	} else {  
		$moduel = $isinstall = $modulename = $version = '';
		if (file_exists(PC_PATH.'modules'.DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php')) {
			require PC_PATH.'modules'.DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'config.inc.php';
			$isinstall = L('install');
		} else {
			$module = L('unknown');
			$isinstall = L('no_install');
		}
?>
	<tr class="on">
	<td align="center" width="220"><?php echo $modulename?></td>
	<td width="220" align="center"><?php echo $d?></td>
	<td align="center"><?php echo $version?></td>
	<td align="center"><?php echo L('unknown')?></td>
	<td align="center"><?php echo L('uninstall_now')?></td>
	<td align="center">
	<?php if ($isinstall!=L('no_install')) {?> <a href="javascript:dr_install_uninstall('install','<?php echo L('install_desc')?>','<?php echo L('module_istall')?>','?m=admin&c=module&a=install&module=<?php echo $d?>');"><font color="#009933"><?php echo $isinstall?></font><?php } else {?><font color="#009933"><?php echo $isinstall?></font><?php }?></a>
	</td>
	</tr>
<?php 
		}
	}
}
?>
</tbody>
    </table>
    </div>
 <div id="pages"><?php echo $pages?></div>
</div>
</body>
</html>