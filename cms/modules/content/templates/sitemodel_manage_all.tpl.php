<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<div id="closeParentTime" style="display:none"></div>
<SCRIPT LANGUAGE="JavaScript">
<!--
/*if(window.top.$("#current_pos").data('clicknum')==1 || window.top.$("#current_pos").data('clicknum')==null) {
	parent.document.getElementById('display_center_id').style.display='';
	parent.document.getElementById('display_menu_id').style.display='';
	parent.document.getElementById('center_frame').src = '?m=content&c=content&a=public_categorys&type=add&menuid=<?php echo $_GET['menuid'];?>&pc_hash=<?php echo $_SESSION['pc_hash'];?>';
	window.top.$("#current_pos").data('clicknum',0);
}*/
//-->
</SCRIPT>
<div class="pad-10">
<div class="content-menu ib-a blue line-x">
<?php
$pc_hash = $_SESSION['pc_hash'];
foreach($datas2 as $r) {
	echo "<a href=\"?m=content&c=content&a=initall&modelid=".$r['modelid']."&menuid=822&pc_hash=".$pc_hash."\"";
	if($r['modelid']==$modelid) echo "class='on'";
	echo "><em>".$r['name']."</em></a>　";
}
?>
</div>
</div>
</body>
</html>