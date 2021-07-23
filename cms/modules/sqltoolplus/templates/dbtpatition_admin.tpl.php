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
<form action="?m=sqltoolplus&c=index&a=dbtpatition" method="post" id="myform">
<table width="100%" class="table_form">
  	<tr>
	    <th width="120"><?php echo L('modelmx')?></th>
	    <td class="y-bg"><?php echo form::select($model_array,'','name="modelid"',L('select'))?></td>
  	</tr>
	<tr>
	    <th width="120"><?php echo L('foreachpartition')?></th>
	    <td class="y-bg"><?php echo form::select(array('100000'=>L('selectshiwan'),'500000'=>L('selectwushiwan'),'1000000'=>L('selectyibaiwan'),'5000000'=>L('selectwubaiwan')),'','name="dbtp_range"')?>  <?php echo L('foreachpartitiondesc')?></td>
  	</tr>
	<tr>
	    <th width="120"><?php echo L('partitions')?></th>
	    <td class="y-bg"><input type="text" name="dbtp_num" value="0" />  <?php echo L('partitionsdesc')?></td>
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
</html>