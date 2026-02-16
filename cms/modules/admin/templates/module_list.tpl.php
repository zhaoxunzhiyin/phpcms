<?php 
defined('IS_ADMIN') && IS_ADMIN or exit('No permission resources.');
$show_dialog = 1;include $this->admin_tpl('header', 'admin');?>
<div class="page-container" style="margin-bottom: 0px !important;">
    <div class="page-content-wrapper">
        <div class="page-content page-content3 mybody-nheader main-content<?php if (param::get('is_iframe') && !param::get('is_menu')) {?> main-content2<?php }?>">
<div class="page-body">
<div class="note note-danger">
    <p><a href="javascript:dr_admin_menu_ajax('?m=admin&c=cache_all&a=init&pc_hash='+pc_hash+'&is_ajax=1',1);"><?php echo L('update_cache_all');?></a></p>
</div>

<div class="table-list">
<form name="myform" action="" method="post" id="myform">
    <table width="100%" cellspacing="0">
        <thead>
            <tr>
            <th width="60"></th>
            <th width="330"><?php echo L('名称 / 目录')?></th>
            <th width='150'><?php echo L('moduleauthor')?></th>
            <th width="80"><?php echo L('versions')?></th>
            <th><?php echo L('operations_manage')?></th>
            </tr>
        </thead>
    <tbody>
 <?php 
if (is_array($directory)){
    foreach ($directory as $i=>$d){
        if (array_key_exists($d, $modules)) {
?>   
    <tr>
    <td><span class="badge badge-success"> <?php echo $i+1;?> </span></td>
    <td><?php echo $modules[$d]['name']?> / <?php echo $d?></td>
    <td><?php echo $modules[$d]['author'] ? $modules[$d]['author'] : '系统';?></td>
    <td><?php echo $modules[$d]['version']?></td>
    <td>
    <?php if ($modules[$d]['iscore']) {?><span class="btn btn-xs dark"> <i class="fa fa-ban"></i> <?php echo L('ban')?></span><?php } else {?><a class="btn btn-xs red" href="javascript:void(0);" onclick="dr_install_uninstall('<?php echo L('confirm', array('message'=>$modules[$d]['name']))?>','?m=admin&c=module&a=uninstall','<?php echo $d?>');"> <i class="fa fa-trash"></i> <?php echo L('unload')?></a><?php }?>
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
    <td><span class="badge badge-success"> <?php echo $i+1;?> </span></td>
    <td><?php echo $modulename?> / <?php echo $d?></td>
    <td><?php echo $author ? $author : '系统';?></td>
    <td><?php echo $version?></td>
    <td>
    <?php if ($isinstall!=L('no_install')) {?> <a class="btn btn-xs blue" href="javascript:dr_install_uninstall('<?php echo L('install_desc')?>','?m=admin&c=module&a=install','<?php echo $d?>');"> <i class="fa fa-plus"></i> <?php echo $isinstall?></a><?php } else {?><?php echo $isinstall?><?php }?>
    </td>
    </tr>
<?php 
        }
    }
}
?>
</tbody>
    </table>
</form>
</div>
</div>
</div>
</div>
</div>
</body>
</html>